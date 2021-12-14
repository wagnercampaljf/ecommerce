<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 27/06/2016
 * Time: 18:54
 */
/* SELECT id from produto_filial where produto_id = (SELECT id from produto WHERE codigo_global='242337'); */
namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateContaDuplicadaAction extends Action
{
    public function run()
    {
        
        $meli_origem = new Meli(static::APP_ID, static::SECRET_KEY);
        $user_origem = $meli_origem->refreshAccessToken('TG-5c4f26ef9b69e60006493768-193724256');
        $response_origem = ArrayHelper::getValue($user_origem, 'body');
        
        if (is_object($response_origem) && ArrayHelper::getValue($user_origem, 'httpCode') < 400) {
            $meliAccessToken_origem = $response_origem->access_token;
        }
        else{
            echo "Token de origem inválido!";
            return;
        }
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

    	$filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['id' => [43]])
            ->all();

        /* @var $filial Filial */
        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";
            //continue;

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            print_r($response); die;
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

       		$meliAccessToken = $response->access_token;
            echo $meliAccessToken; die;
                $produtoFilials = $filial->getProdutoFilials()
                                        ->andWhere(['is not','meli_id',null])
                                        ->andWhere(['is not','produto_filial_origem_id',null])
                                        //->andWhere(['>=','id',164660])
                                        ->orderBy('produto_filial.id')
                                        ->all();

                foreach ($produtoFilials as $k => $produto_filial) {
                    
                    $produtoFilial = ProdutoFilial::find()->andWhere(['=', 'id', $produto_filial->produto_filial_origem_id])->one();

                    if ($produtoFilial->produto->fabricante_id != null || $produtoFilial->meli_id == null || $produtoFilial->meli_id == "") {
                        continue;
                    }

                    echo "\n".$k ." - Cópia: ".$produto_filial->id." - ". $produto_filial->meli_id ." - Origem: ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ";

                    $response_item_origem = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                    print_r($response_item_origem);
                    
                    //PREÇO
                    $preco = round($produtoFilial->getValorMercadoLivre(), 2);
                    $preco = $preco * 0.9;
                    //PREÇO
                    
                    $body = [
                        //"price" => $preco,
    	                /*"pictures" => $produtoFilial->produto->getUrlImagesML(),
			            "shipping" => [
                                            "mode" => "me2",
                                            "local_pick_up" => true,
                                            "free_shipping" => false,
                                          	"free_methods" => [],
                                          ],*/
                    ];
                    continue;
                    $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);

	                if ($response['httpCode'] >= 300) {
    					echo "ERROR \n";
            			}
            		else {
                			echo "ok \n";
    				        //print_r(ArrayHelper::getValue($response, 'body.permalink'));
    	            }

                    //Aqui termina o código
                }
            }
            echo "Fim da filial: " . $filial->nome . "\n";
        }
    }
}

