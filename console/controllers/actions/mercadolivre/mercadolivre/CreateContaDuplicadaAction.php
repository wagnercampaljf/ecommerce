<?php
namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class CreateContaDuplicadaAction extends Action
{
    public function run()
    {
        echo "Criando produtos...\n\n";
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()
            //->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['=','id',97])
            ->all();

        foreach ($filials as $filial) {

            $produtoFilials = $filial->getProdutoFilials()
                    ->andWhere(['IS', 'meli_id', NULL])
                    //->andWhere(['>', 'quantidade', 0])
                    ->andWhere(['=', 'produto_id', 334027])
                    ->joinWith('produto')
                    ->andWhere(['=',"(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)",'MACANETA EXTERNA'])
                    ->orderBy('id')
                    ->all();

            foreach ($produtoFilials as $k => $produtoFilial) {
                
                $produto_filial_outro = ProdutoFilial::find()->andWhere(['=', 'produto_filial_origem_id', $produtoFilial->id])->one();

                if($produto_filial_outro->meli_id <> "" and $produto_filial_outro->meli_id <> null){
                    continue;
                }
                
                echo "\n".$k." - Origem: ".$produtoFilial->id . " - Destino: ".$produto_filial_outro->id;
                
                $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                if (!isset($subcategoriaMeli)) {
                    echo "\n\n sem subcategoria \n\n";
                    continue;
                }
                if (is_null($produtoFilial->valorMaisRecente)) {
                    continue;
                }
                $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produto.php',['produto' => $produtoFilial]);
                
                $title = Yii::t('app', '{nome} ({code})', [
                    'cod' => $produtoFilial->produto->codigo_global,
                    'nome' => $produtoFilial->produto->nome
                ]);
                
                $preco = round($produtoFilial->getValorMercadoLivre(), 2);                    

                $body = [
                    "title" => utf8_encode((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                    "category_id" => utf8_encode("MLB116445"),
                    "listing_type_id" => "bronze",
                    "currency_id" => "BRL",
                    "price" => utf8_encode($preco),
                    "available_quantity" => utf8_encode($produtoFilial->quantidade),
                    "seller_custom_field" => utf8_encode($produtoFilial->id),
                    "condition" => "new",
                    "description" => ["plain_text" => $page],
                    "pictures" => $produtoFilial->produto->getUrlImagesML(),
                    //"pictures" => [["source" => utf8_encode($produtoFilial->produto->getUrlImageML())],],
                    "shipping" => [
                        "mode" => "me2",
                        "local_pick_up" => true,
                        "free_shipping" => false,
                        "free_methods" => [],
                    ],
                    "warranty" => "6 meses",
                ];

                print_r($body);
                die;
                //continue;
                
                $user_outro     = $meli->refreshAccessToken($produto_filial_outro->filial->refresh_token_meli);
                $response_outro = ArrayHelper::getValue($user_outro, 'body');
                
                if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
                    $meliAccessToken_outro = $response_outro->access_token;
                
                    $response_outro = $meli->post("items?access_token=" . $meliAccessToken_outro,$body);
                    print_r($response_outro);
                    if ($response_outro['httpCode'] >= 300) {
                        echo " - Não Publicado \n";
                    } 
                    else {
                        $produto_filial_outro->meli_id = ArrayHelper::getValue($response_outro, 'body.id');
                        echo " - Publicado";
                       
                        if (!$produto_filial_outro->save()) {
                            echo " - Meli_ID não salvo";
                        }
                        echo "\n";
                    }
                }
                
                die;

            }
        }
    }
}
