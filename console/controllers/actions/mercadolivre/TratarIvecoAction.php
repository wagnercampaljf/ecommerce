<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\Imagens;

class TratarIvecoAction extends Action
{
    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/tratar_iveco_".date("Y-m-d_H-i-s").".csv", "a");
        //fwrite($arquivo_log, "produto_filial_id;filial_id;meli_id;nome;status;categoria_meli_id;quantidade;quantidade_vendida;data_criacao;tempo_para_venda");
        
        //$filiais     = Filial::find()->andWhere(['id' => [72, 98]])->all();
	$filiais     = Filial::find()->andWhere(['id' => [100]])->all();
        
        foreach($filiais as $k => $filial){
            $meli       = new Meli(static::APP_ID, static::SECRET_KEY);
            $user       = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response   = ArrayHelper::getValue($user, 'body');
            
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                
                //echo "\n\n".$meliAccessToken."\n\n";
                
                $x = 0;
                $i = 0;
                $response_order = $meli->get("/users/195972862/items/search?search_type=scan&limit=100&q=Iveco&access_token=" . $meliAccessToken);
                
                while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                    
                    if ($i >= 0){
                        foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                            
                            echo "\n".++$x." - MELI_ID: ".$meli_id;
                            
                            //break;
                            $response_item              = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                            $response_item_description   = $meli->get("/items/".$meli_id."/description?access_token=" . $meliAccessToken);
                            //print_r($response_item);
                            //print_r($response_item_description);
                            //die;
                            
                            $title = str_replace("IVECO", "", str_replace("Iveco", "", ArrayHelper::getValue($response_item, 'body.title')));
                            $body = ["title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60))];
                            $response = $meli->put("items/".$meli_id."?access_token=".$meliAccessToken, $body, [] );

			    while ($response['httpCode'] == 429) {
                		echo " - ERRO";
		                $response = $meli->put("items/".$meli_id."?access_token=".$meliAccessToken, $body, [] );
            		    }
                            if ($response['httpCode'] >= 300) {
				//print_r($response["body"]->message);
                                echo " - Nome não alterado";
                            }
                            else {
                                echo " - Nome alterado";//.$response["body"]->permalink;
                            }

			    //echo " ("; var_dump(property_exists($response_item_description["body"], "plain_text")); echo ") ";
			    if(property_exists($response_item_description["body"], "plain_text")){
	                            $page = str_replace("IVECO", "", str_replace("Iveco", "", ArrayHelper::getValue($response_item_description, 'body.plain_text')));
	                            $body = ['plain_text' => $page];
	                            $response = $meli->put("items/".$meli_id."/description?api_version=2&access_token=" . $meliAccessToken, $body, []);

				    while ($response['httpCode'] == 429) {
	                                echo " - ERRO";
					$response = $meli->put("items/".$meli_id."/description?api_version=2&access_token=" . $meliAccessToken, $body, []);
	                            }
	                            if ($response['httpCode'] >= 300) {
					//print_r($response["body"]->message);
	                                echo " - Descricao não alterado";
	                            }
	                            else {
	                                echo " - Descricao alterado";
	                            }
                            }
                            //fwrite($arquivo_log, "\n".$produto_filial_id.';"'.$filial_id.'";"'.$meli_id.'";"'.ArrayHelper::getValue($response_item, 'body.title').'";"'.ArrayHelper::getValue($response_item, 'body.status').'";"'.ArrayHelper::getValue($response_item, 'body.category_id').'";"'.ArrayHelper::getValue($response_item, 'body.available_quantity').'";"'.ArrayHelper::getValue($response_item, 'body.sold_quantity').'";"'.ArrayHelper::getValue($response_item, 'body.date_created').'";"'.$tempo_para_venda.'"');
                            
                        }
                    }
                    
                    echo "\n Scroll: ".$i++;
                    $response_order = $meli->get("/users/195972862/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&q=Iveco&access_token=" . $meliAccessToken);
                    
                }
            }
        }
        
        //fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 
