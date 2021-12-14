<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateNumeroPecaAction extends Action
{
    public function run()
    {

        $nome_arquivo = "/var/tmp/log_update_numero_peca_ml_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;meli_id;status;meli_id_sem_juros;status_juros");

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
                                            ->andWhere(['produto_filial.meli_id_sem_juros' => ['MLB1451872859']])
                                            //->andWhere(['produto_filial.id' => []])
                    			    //->andWhere(['produto_filial.produto_id' => [236162]])
                    			    //->andWhere(['=','e_nome_alterado',true])
                                            ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id;
		    fwrite($arquivo_log, "\n".$produtoFilial->id.";".$produtoFilial->meli_id);

		    //if($produtoFilial->meli_id != 'MLB979128351'){die;}

       		    if ($produtoFilial->produto->fabricante_id != null) {

                        //Atualiza Numero Peca
           		$body = ['attributes' =>[
				    [
				    'id'                    => 'PART_NUMBER',
                                    //'name'                  => 'Numero Peca',
				    'value_name'            => $produtoFilial->produto->codigo_global,
           		            'attribute_group_id'    => 'OTHERS',
           		            'attribute_group_name'  => 'Outros',
           		            ]
				]
           		];
                        $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            echo " - ERROR Numero Peca";
                            fwrite($arquivo_log, ";Numero Peca nao alterado");
                        }
                        else {
                            echo " - OK Numero Peca";
                            echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                            fwrite($arquivo_log, ";Numero Peca alterado");
                        }
                        //Aqui termina o código
                        
                        //Altera Numero Peca SEM JUROS
                        if($produtoFilial->meli_id_sem_juros != null && $produtoFilial->meli_id_sem_juros != ""){
			    fwrite($arquivo_log, ";".$produtoFilial->meli_id_sem_juros);
                            $body = ['attributes' =>[
                            	[
                                'id'                    => 'PART_NUMBER',
                                'name'                  => 'Número de Peça',
				'value_id'		=> null,
                                'value_name'            => $produtoFilial->produto->codigo_global,
				'value_struct'		=> null,
				'values'		=> [[
					'id'	=> null,
					'name'	=> $produtoFilial->produto->codigo_global,
					'struct'=> null,
				]],
				'attribute_group_id'	=> "OTHERS",
				'attribute_group_name'	=> "Outros"
                                ]
                              ]
                            ];
			    print_r($body);
                            $response = $meli->put("items/{$produtoFilial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                echo " - SEM JUROS ERROR Numero Peca";
                                print_r($response);
                                fwrite($arquivo_log, ";SEM JUROS Numero Peca nao alterado");
                            }
                            else {
                                $status_titulo = true;
                                echo " - SEM JUROS OK Numero Peca";
                                                echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                                fwrite($arquivo_log, ";SEM JUROS Numero Peca alterado");
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

