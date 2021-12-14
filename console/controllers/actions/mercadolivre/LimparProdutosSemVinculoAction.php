<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use Yii;

class LimparProdutosSemVinculoAction extends Action
{
    public function run($cliente = 1){

        echo "INICIO\n\n";

	$arquivo_log = fopen("/var/tmp/log_limpar_produtos_sem_vinculo_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;status");

	//$file = fopen("/var/tmp/produtos_sem_vinculo_18-03-2020.csv", 'r');
	$file = fopen("/var/tmp/produtos_sem_vinculo_23-03-2020.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

	$meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5e2efe08144ef6000642cdb6-193724256");
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
		$meliAccessToken = $response->access_token;

	        foreach ($LinhasArray as $i => &$linhaArray){
	            echo "\n".$i." - ".$linhaArray[0];
		    fwrite($arquivo_log, "\n".$linhaArray[0]);

		    $produto_filial  		= ProdutoFilial::find()->andWhere(['=', 'meli_id', $linhaArray[0]])->one();
		    $produto_filial_sem_juros	= ProdutoFilial::find()->andWhere(['=', 'meli_id_sem_juros', $linhaArray[0]])->one();

		    if($produto_filial){
			echo " - Produto encontrado";
			fwrite($arquivo_log, ";Produto com vinculo");
			continue;
		    }
		    else{
			echo " - Produto nao encontrado";
		    }
		    if($produto_filial_sem_juros){
	                echo " - Produto SEM JUROS encontrado";
			fwrite($arquivo_log, ";Produto com vinculo SEM JUROS");
                        continue;
	            }
	            else{
	                echo " - Produton SEM JUROS nao encontrado";
	            }

		    $response = $meli->get("items/{$linhaArray[0]}?access_token=" . $meliAccessToken);
		    if ($response['httpCode'] >= 300) {
                        //print_r($response);
                        echo " - Nao encontrdo";
			fwrite($arquivo_log, ";Produto nao encontrado");
                    }
                    else {
			//print_r($response); die;
			//echo " - "; print_r(ArrayHelper::getValue($response, 'body.sold_quantity'));
			echo " - "; print_r(ArrayHelper::getValue($response, 'body.status'));
			//echo " - "; print_r(ArrayHelper::getValue($response, 'body.permalink'));
                        echo " - Encontrdo";

			if(ArrayHelper::getValue($response, 'body.status') == "closed"){
				fwrite($arquivo_log, ";Produto DELETADO");
                        }
                        else{
				fwrite($arquivo_log, ";Produto nao DELETADO");
                        }

			/*if(ArrayHelper::getValue($response, 'body.sold_quantity') == 0 && ArrayHelper::getValue($response, 'body.status') <> "closed"){
				echo " - Deletar";
				
				$body = ["status" => 'closed',];
	                        $response = $meli->put("items/{$linhaArray[0]}?access_token=" . $meliAccessToken, $body, []);
	                        if ($response['httpCode'] >= 300) {
	                                echo " - Nao deletado";
	                                fwrite($arquivo_log, ";Produto NAO DELETADO");
	                        }
	                        else {
	                                echo " - Deletado";
	                                fwrite($arquivo_log, ";Produto DELETADO");
	                        }
			}
			else{
				if(ArrayHelper::getValue($response, 'body.status') == "closed"){
					fwrite($arquivo_log, ";Produto DELETADO");
				}
				if(ArrayHelper::getValue($response, 'body.sold_quantity') > 0 ){
                                        fwrite($arquivo_log, ";Produto nao DELETADO, com vendas");
                                }
				echo " - Nao deletar";
			}*/
		    }
	    }
            //if ($i <= 0){continue;}
	}

	fclose($arquivo_log);

	die;

        // Escreve no log
        /*$arquivo_log = fopen("/var/tmp/log_limpar_produtos_sem_vinculo_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;status_vinculo;status_delecao");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5e2efe08144ef6000642cdb6-193724256");
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            $x = 0;
            $i = 0;
            $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);

            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                if ($i >= 0){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){

                        //if($meli_id != 'MLB864673232'){continue;}

                        echo "\n".++$x." - MELI_ID: ".$meli_id;
                        fwrite($arquivo_log, "\n".$meli_id);

                        $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id])->one();

                        $produto_filial_sem_juros = ProdutoFilial::find()->andWhere(['=','meli_id_sem_juros', $meli_id])->one();

			if($produto_filial){
			    echo " - Produto COM vinculo, Comum";
			    fwrite($arquivo_log, ";Produto COM vinculo, Comum");
			}
			if($produto_filial_sem_juros){
			    echo " - Produto COM vinculo, Sem juros";
			    fwrite($arquivo_log, ";Produto COM vinculo, Sem juros");
			}
			if(!$produto_filial && !$produto_filial_sem_juros){
			    echo " - Produto SEM vinculo";
			    fwrite($arquivo_log, ";Produto SEM vinculo");
                        }
                    }
                }

                echo "\n Scroll: ".$i++;
                $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
        }*/

        fclose($arquivo_log);

        echo "\n\nFIM!\n\n";

    }
}


