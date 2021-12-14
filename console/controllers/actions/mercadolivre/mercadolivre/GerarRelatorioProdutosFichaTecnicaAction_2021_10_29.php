<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;

class GerarRelatorioProdutosFichaTecnicaAction extends Action
{
    public function run(){

        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/log_produtos_ficha_tecnica_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;nome;categoria_meli_id;status_ml\n");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5f1eb688dd81f00006ef37b4-193724256");
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            //echo "\n\n".$meliAccessToken."\n\n"; die;
            
            $x = 0;
            $i = 0;

            $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);

            while (ArrayHelper::getValue($response_order, 'httpCode') <> 400){

                if ($i >= 1387){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){

                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        echo "\n".++$x." - MELI_ID: ".$meli_id." - ".ArrayHelper::getValue($response_item, 'body.category_id');

                        if(ArrayHelper::getValue($response_item, 'body.status') == "closed"){
                            continue;
                        }

                        $tags = ArrayHelper::getValue($response_item, 'body.tags');
                        foreach($tags as $k => $tag){
                            if($tag == "incomplete_technical_specs"){
                                echo " - Ficha tecnica incompleta";
                                fwrite($arquivo_log, $meli_id.";".ArrayHelper::getValue($response_item, 'body.title').";".ArrayHelper::getValue($response_item, 'body.category_id').";".ArrayHelper::getValue($response_item, 'body.status')."\n");
                                break;
                            }
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
 