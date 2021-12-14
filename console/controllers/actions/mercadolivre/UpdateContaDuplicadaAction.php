<?php

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
        echo "Criando produtos...\n\n";

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filial_duplicada   = Filial::find()->andWhere(['=','id',98])->one();
        $user_outro         = $meli->refreshAccessToken($filial_duplicada->refresh_token_meli);
        $response_outro     = ArrayHelper::getValue($user_outro, 'body');

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['<>','id',98])
	    ->andWhere(['<>','id',43])
	    ->orderBy('id')
            ->all();

        if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
            $meliAccessToken_outro = $response_outro->access_token;
$o = 0;
            foreach ($filials as $y => $filial) {

		echo "\n\n".$y." - Filial: ".$filial->id." - ".$filial->nome."\n\n"; 

		if($y<0){
			continue;
		}

                $produtoFilials = $filial->getProdutoFilials()  ->andWhere(['IS NOT', 'meli_id', NULL])
								//->andWhere(['=', 'id', 187545])
                                                    		->orderBy('id')
                                                                ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
echo "\n".$o++;continue;
		    echo "\n".$k." - Origem: ".$produtoFilial->id;

		    /*if($k <= 18000 && $produtoFilial->filial_id == 97){
			echo " - pulou";
			continue;
		    }*/

                    $produto_filial_outro = ProdutoFilial::find()   ->andWhere(['=', 'produto_filial_origem_id', $produtoFilial->id])
                                                                    ->andWhere(['=', 'filial_id', 98])
                                                                    ->one();

		    if($produto_filial_outro){
			echo " - Destino: ".$produto_filial_outro->id." - ".$produto_filial_outro->meli_id;
		    }
		    else{
			echo " - Produto não encontrado";
			continue;
		    }

                    if($produto_filial_outro->meli_id == "" || $produto_filial_outro->meli_id == null){
            		//print_r($produto_filial_outro); die;
            		//echo "\n\n com meli_id \n\n";
                        continue;
                    }

                    $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                    if (!isset($subcategoriaMeli)) {
                        echo "\n\n sem subcategoria \n\n";
                        continue;
                    }
                    if (is_null($produtoFilial->valorMaisRecente)) {
                        echo "\n\n sem valor \n\n";
                        continue;
                    }
                    //$page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produtoFilial]);
    
                    //$title = Yii::t('app', '{nome} ({code})', ['code' => $produto_filial_outro->produto->codigo_global,'nome' => $produto_filial_outro->produto->nome]);
    
                    $preco = round($produtoFilial->getValorMercadoLivre(), 2);
		    echo " - ". $preco;
                    
                    /*$condicao = "used";
                    $pos_nome_semi_novo1                    = strpos($title, "SEMI NOVO");
                    $pos_nome_semi_novo2                    = strpos($title, "SEMI-NOVO");
                    $pos_nome_semi_novo3                    = strpos($title, "SEMINOVO");
                    $pos_nome_reman                         = strpos($title, "REMAN");
                    $pos_nome_usado                         = strpos($title, "USADO");
                    $pos_descricao_semi_novo1               = strpos($title, "SEMI NOVO");
                    $pos_descricao_semi_novo2               = strpos($title, "SEMI-NOVO");
                    $pos_descricao_semi_novo3               = strpos($title, "SEMINOVO");
                    $pos_descricao_reman                    = strpos($title, "REMAN");
                    $pos_descricao_usado                    = strpos($title, "USADO");
                    $pos_aplicacao_semi_novo1               = strpos($title, "SEMI NOVO");
                    $pos_aplicacao_semi_novo2               = strpos($title, "SEMI-NOVO");
                    $pos_aplicacao_semi_novo3               = strpos($title, "SEMINOVO");
                    $pos_aplicacao_reman                    = strpos($title, "REMAN");
                    $pos_aplicacao_usado                    = strpos($title, "USADO");
                    $pos_aplicacao_complementar_semi_novo1  = strpos($title, "SEMI NOVO");
                    $pos_aplicacao_complementar_semi_novo2   = strpos($title, "SEMI-NOVO");
                    $pos_aplicacao_complementar_semi_novo3   = strpos($title, "SEMINOVO");
                    $pos_aplicacao_complementar_reman        = strpos($title, "REMAN");
                    $pos_aplicacao_complementar_usado        = strpos($title, "USADO");
                    if ($pos_nome_semi_novo1 === false
                        and $pos_nome_semi_novo2  === false
                        and $pos_nome_semi_novo3  === false
                        and $pos_nome_reman  === false
                        and $pos_nome_usado  === false
                        and $pos_descricao_semi_novo1  === false
                        and $pos_descricao_semi_novo2  === false
                        and $pos_descricao_semi_novo3  === false
                        and $pos_descricao_reman  === false
                        and $pos_descricao_usado  === false
                        and $pos_aplicacao_semi_novo1  === false
                        and $pos_aplicacao_semi_novo2  === false
                        and $pos_aplicacao_semi_novo3  === false
                        and $pos_aplicacao_reman  === false
                        and $pos_aplicacao_usado  === false
                        and $pos_aplicacao_complementar_semi_novo1  === false
                        and $pos_aplicacao_complementar_semi_novo2  === false
                        and $pos_aplicacao_complementar_semi_novo3  === false
                        and $pos_aplicacao_complementar_reman  === false
                        and $pos_aplicacao_complementar_usado  === false
                        
                        )
                    {
                        $condicao = "new";
                    }*/
		    echo " - ".$subcategoriaMeli;
                    $body = [
                        //"title" => utf8_encode((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                        "category_id" => utf8_encode($subcategoriaMeli),
                        //"listing_type_id" => "bronze",
                        //"currency_id" => "BRL",
                        //"price" => utf8_encode($preco),
                        //"available_quantity" => utf8_encode($produtoFilial->quantidade),
                        //"seller_custom_field" => utf8_encode($produtoFilial->id),
                        //"condition" => $condicao,
                        //"description" => ["plain_text" => $page],
                        //"pictures" => $produtoFilial->produto->getUrlImagesML(),
                        /*"shipping" => [
                            "mode" => "me2",
                            "local_pick_up" => true,
                            "free_shipping" => false,
                            "free_methods" => [],
                        ],
            		    "sale_terms" =>   [
                            [    "id" => "WARRANTY_TYPE",
                                 "value_id" => "2230280"
                            ],
                            [    "id" => "WARRANTY_TIME",
                                 "value_name" => "3 meses"
                            ]
                        ]*/
                    ];

                    //print_r($body);
                    //continue;

		    $response_outro	= $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, []);
		    //echo "items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro;
                    //print_r($response_outro);
                    if ($response_outro['httpCode'] >= 300) {
			//print_r($response_outro);
                        echo " - Não Atualizado \n";
			//die;
                    }
                    else {
                        echo " - ";print_r(ArrayHelper::getValue($response_outro, 'body.permalink'));
                        echo " - Atualizado \n";
			//die;
                    }
                }
            }
        }
    }
}


