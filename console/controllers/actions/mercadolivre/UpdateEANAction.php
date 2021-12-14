<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateEANAction extends Action
{
    public function run()
    {

        $nome_arquivo = "/var/tmp/log_update_titulo_ml_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;status;produto_filial_conta_duplicada;status");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        echo "Começo da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => [84]])
    	    ->andWhere(['<>','id', 43])
            ->andWhere(['<>','id', 98])
    	    ->orderBy('id')
            ->all();

        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

                $meliAccessToken = $response->access_token;
                $produtoFilials = $filial   ->getProdutoFilials()
                                            ->andWhere(['is not','meli_id',null])
                                            //->andWhere(['>','quantidade',0])
                                            //->andWhere(['produto_filial.meli_id_sem_juros' => ['MLB1450584964']])
                                            //->andWhere(['produto_filial.id' => [34637]])
                    			    //->andWhere(['produto_filial.produto_id' => [236162]])
                    			    ->andWhere(['=','e_nome_alterado',true])
                                            ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id;
       		    if ($produtoFilial->produto->fabricante_id != null) {

                        //Atualização EAN
           		        if (is_null($produtoFilial->produto->codigo_barras)) {
           		            $body = [
					//'sku'           => 'PA'.$produtoFilial->produto->id,
					'attributes' 	=>[
					 	[
           		                 	'id'                    => 'EAN',
           		                 	'name'                  => 'EAN',
           		                 	'value_name'            => $produtoFilial->produto->codigo_barras,
           		                 	'attribute_group_id'    => 'OTHERS',
           		                 	'attribute_group_name'  => 'Outros',
           		                 	]
					]
           		            ];
           		            $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
           		            if ($response['httpCode'] >= 300) {
           		                echo " - ERROR EAN";
           		                fwrite($arquivo_log, ";Titulo não alterado");
           		            }
           		            else {
           		                echo " - OK EAN";
           		                echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
           		                fwrite($arquivo_log, ";Titulo alterado");
           		            }
           		        }

                        //Atualização SKU
           		        $body = ['attributes' =>[
				    [
           		            'id'                    => 'SELLER_SKU',
           		            'name'                  => 'SKU',
           		            'value_name'            => $produtoFilial->produto->codigo_global,
           		            'attribute_group_id'    => 'OTHERS',
           		            'attribute_group_name'  => 'Outros',
           		           ]
				 ]
           		        ];
                        $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            echo " - ERROR SKU";
                            fwrite($arquivo_log, ";Titulo não alterado");
                        }
                        else {
                            echo " - OK SKU";
                            echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                            fwrite($arquivo_log, ";Titulo alterado");
                        }
                        //Aqui termina o código
                        
                        //Alteração SEM JUROS
                        if($produtoFilial->meli_id_sem_juros != null && $produtoFilial->meli_id_sem_juros != ""){
                            //Atualização EAN
//			    echo "\n\n==>".$meliAccessToken."<==\n\n"; die;
                            $body = [
//				'sku' 		=> 'PA'.$produtoFilial->produto->id,
				'attributes' 	=>[
					[
                                	'id'                    => 'EAN',
	                                'name'                  => 'EAN',
        	                        'value_name'            => $produtoFilial->produto->codigo_barras,
        	                        'attribute_group_id'    => 'OTHERS',
        	                        'attribute_group_name'  => 'Outros',
        	                        ]
				]
                            ];
                            $response = $meli->put("items/{$produtoFilial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                echo " - SEM JUROS ERROR EAN";
                                print_r($response);
                                fwrite($arquivo_log, ";SEM JUROS Titulo não alterado");
                            }
                            else {
                                echo " - SEM JUROS OK EAN";
                                echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                                fwrite($arquivo_log, ";SEM JUROS Titulo alterado");
                            }


			    //Atualização SKU
                            $body = ['attributes' =>[
                                [
                                'id'                    => 'SELLER_SKU',
                                'name'                  => 'SKU',
                                'value_name'            => 'PA'.$produtoFilial->produto->id,
                                'attribute_group_id'    => 'OTHERS',
                                'attribute_group_name'  => 'Outros',
                                ]
                              ]
                            ];
                            $response = $meli->put("items/{$produtoFilial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                echo " - SEM JUROS ERROR SKU";
                                print_r($response);
                                fwrite($arquivo_log, ";SEM JUROS Titulo não alterado");
                            }
                            else {
                                $status_titulo = true;
                                echo " - SEM JUROS OK SKU";
                                                echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                                fwrite($arquivo_log, ";SEM JUROS Titulo alterado");
                            }

			    //Atualização DADOS FISCAIS
                            $body = [
//				"seller_id"	=> "193724256",
				"sku"		=> "PA".$produtoFilial->produto->id,
				"title" => substr($produtoFilial->produto->nome,0,60),
				"type" => "single",
				"tax_information" => [
				    "ncm" 		=> $produtoFilial->produto->codigo_montadora,
				    "origin_type"	=> "reseller",
				    "origin_detail"	=> "0",
				    "csosn"		=> "102",
				    "cest"		=> $produtoFilial->produto->cest,
				    "ean"		=> "",
				    "empty"		=> false
				],
				"register_type" => "final",
                            ];
                            //$response = $meli->put("items/{$produtoFilial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, [] );
			    $response = $meli->post("items/fiscal_information?access_token=" . $meliAccessToken, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                echo " - SEM JUROS ERROR DADOS FISCAIS";
                                fwrite($arquivo_log, ";SEM JUROS DADOS FISCAIS não alterado");
                            }
                            else {
                                echo " - SEM JUROS OK DADOS FISCAIS";
                                fwrite($arquivo_log, ";SEM JUROS DADOS FISCAIS alterado");
                            }

			}
                        //Alteração SEM JUROS
                    }
                }
            }
        echo "Fim da filial: " . $filial->nome . "\n";
        }

    	fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
    	fwrite($arquivo_log, "\nFim da rotina de atualização dos produtos no ML");
    	fclose($arquivo_log);

        echo "Fim da rotina de atualização dos produtos no ML";
    }
}

