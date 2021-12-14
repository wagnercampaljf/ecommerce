<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use Yii;

class CreateProdutosSemJurosAction extends Action
{
    
    
    public function run($cliente = 1){
        
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/log_produtos_sem_juros_ml_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id_origem;produto_filial_id;meli_id;nome;status;permalink");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5e2efe08144ef6000642cdb6-193724256");
        $response = ArrayHelper::getValue($user, 'body');
        
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            $x = 0;
            $i = 0;
            $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                if ($i >= 376){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){

                        //if($meli_id != 'MLB864673232'){continue;}
                        
			echo "\n".++$x." - MELI_ID: ".$meli_id;
                        fwrite($arquivo_log, "\n".$meli_id);
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=','meli_id',$meli_id])
								->andWhere(['=','filial_id', 72])
								->andWhere(['is','meli_id_sem_juros', null])
								->one();
                        if($produto_filial){
                            $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                            echo " - ".ArrayHelper::getValue($response_item, 'body.status')." - ";
                            $status = "Anúncio não pausado";
                            
                            if(ArrayHelper::getValue($response_item, 'body.status') == "paused"){
                                echo ArrayHelper::getValue($response_item, 'body.sold_quantity') ." - ". ArrayHelper::getValue($response_item, 'body.available_quantity');
                                foreach(ArrayHelper::getValue($response_item, 'body.sub_status') as $k => $substatus){
                                    //if($substatus == "suspended" && ArrayHelper::getValue($response_item, 'body.sold_quantity') > 0 && ArrayHelper::getValue($response_item, 'body.available_quantity') > 0 ){
				    if($substatus == "suspended" && ArrayHelper::getValue($response_item, 'body.available_quantity') > 0 ){
                                        
                                        //$response_descricao = $meli->get("/items/".$meli_id."/description?access_token=" . $meliAccessToken);
                                        //$page = ArrayHelper::getValue($response_descricao, 'body.plain_text');
					$title = Yii::t('app', '{nome} cod {cod}', [
                        		    'cod' => $produto_filial->produto->codigo_global,
                        		    'nome' => $produto_filial->produto->nome
        		                ]);

		                        $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produto_filial]);
                                        $page = str_replace("'", "", $page);
                                        $page = str_replace("<p>", "", $page);
                                        $page = str_replace("</p>", "", $page);
                                        $page = str_replace("<br>", "\n", $page);
                                        $page = str_replace("<BR>", "\n", $page);
                                        $page = str_replace("<br/>", "\n", $page);
                                        $page = str_replace("<BR/>", "\n", $page);
                                        $page = str_replace("<strong>", "", $page);
                                        $page = str_replace("</strong>", "", $page);                                        

					$condicao = ($produto_filial->produto->e_usado)? "used" : "new";

					$preco = ArrayHelper::getValue($response_item, 'body.price');
					$preco += 0.30;
					if (ArrayHelper::getValue($response_item, 'body.listing_type_id') == 'gold_pro'){
						$preco = $preco * 1.06;
						echo " - GOLD_PRO";
					}

                                        $body = [
                                            "title"                 => utf8_encode(((strlen($title) <= 60) ? $title : substr($title, 0, 60))),
                                            "category_id"           => utf8_encode(ArrayHelper::getValue($response_item, 'body.category_id')),
                                            "listing_type_id"       => ArrayHelper::getValue($response_item, 'body.listing_type_id'),
                                            "currency_id"           => "BRL",
                                            "price"                 => $preco,
                                            "available_quantity"    => ArrayHelper::getValue($response_item, 'body.available_quantity'),
                                            "seller_custom_field"   => utf8_encode("SJ.".$produto_filial->id),
                                            "condition"             => $condicao,
                                            "description"           => ["plain_text" => $page],
                                            "pictures"              => ArrayHelper::getValue($response_item, 'body.pictures'),
                                            "shipping"              => ArrayHelper::getValue($response_item, 'body.shipping'),
                                            //"sale_terms"            => ArrayHelper::getValue($response_item, 'body.sale_terms'),
                                            //"attributes"            => ArrayHelper::getValue($response_item, 'body.attributes'),
					    'attributes' =>[
                                        	[
                                	        'id'                    => 'EAN',
                        	                'name'                  => 'EAN',
                	                        'value_name'            => $produtoFilial->produto->codigo_barras,
        	                                'attribute_group_id'    => 'OTHERS',
	                                        'attribute_group_name'  => 'Outros',
                                        	]
                                	    ],
					    'attributes' =>[
                		                [
                		                'id'                    => 'SELLER_SKU',
                		                'name'                  => 'SKU',
                		                'value_name'            => 'SJ'.$produtoFilial->produto->codigo_global,
                		                'attribute_group_id'    => 'OTHERS',
                		                'attribute_group_name'  => 'Outros',
                		                ]
		                            ],
                                        ];
                                        //print_r($body);
                                        $response_outro = $meli->post("items?access_token=" . $meliAccessToken,$body);
                                        //print_r($response_outro);
                                        if ($response_outro['httpCode'] >= 300) {
                                            print_r($response_outro);
                                            print_r($body);
                                            echo " - Não Publicado \n";
                                            fwrite($arquivo_log, ";".$produto_filial->id.";;".$produto_filial->produto->nome.";Produto não craido no ML;");
                                        }
                                        else {
                                            $produto_filial->meli_id_sem_juros = ArrayHelper::getValue($response_outro, 'body.id');
                                            echo " - ";print_r(ArrayHelper::getValue($response_outro, 'body.permalink'));
                                            fwrite($arquivo_log, ";".$produto_filial->id.";".ArrayHelper::getValue($response_outro, 'body.id').";".$produto_filial->produto->nome.";Produto craido no ML");
                                            if ($produto_filial->save()) {
                                                echo " - Meli_ID salvo";
                                                fwrite($arquivo_log, " - Estoque salvo no PecaAgora");
                                            }
                                            else{
                                                echo " - Meli_ID não salvo";
                                                fwrite($arquivo_log, " - Estoque não salvo no PecaAgora");
                                            }
                                            fwrite($arquivo_log, ";".ArrayHelper::getValue($response_outro, 'body.permalink'));
                                        }
                                        break;
                                    }
                                }
                            }
                            else{
                                fwrite($arquivo_log, ";".$produto_filial->id.";;".$produto_filial->produto->nome.";".$status.";");
                            }
                        }
                        else{
			    echo " - Estoque não encontrado";
                            fwrite($arquivo_log, ";;;;Estoque não encontrado;");
                        }
                    }
                }
                echo "\n Scroll: ".$i++;
                $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}

