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
        $arquivo_log = fopen("/var/tmp/produtos_baixa_qualidade_imagens_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;nome;categoria_meli_id;status_ml\n");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5f7b39a0f7091400062479e0-193724256");//CONTA PRINCIPAL
        // $user = $meli->refreshAccessToken("TG-5f68a5780cc4c1000723e128-435343067");//CONTA DUPLICADA
        //print_r($user);
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {


            $meliAccessToken = $response->access_token;



            //echo "\n\n".$meliAccessToken."\n\n"; die;

            $x = 0;
            $i = 0;

           $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken); //CONTA PRINCIPAL
            // $response_order = $meli->get("/users/435343067/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken); //CONTA DUPLICADA




            while (ArrayHelper::getValue($response_order, 'httpCode') <> 400){
                //print_r($response_order);
                if ($i >= 844){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){

                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        echo "\n".++$x." - MELI_ID: ".$meli_id." - ".ArrayHelper::getValue($response_item, 'body.category_id');

                        if(ArrayHelper::getValue($response_item, 'body.status') == "closed"){
                            continue;
                        }

                        $tags = ArrayHelper::getValue($response_item, 'body.tags');
                        foreach($tags as $k => $tag){
                            if($tag == "poor_quality_picture" || $tag == "poor_quality_thumbnail" ){
                                print_r($tag);

                                echo " - Imagens de baixa  qualidade";
                                fwrite($arquivo_log, $meli_id.";".ArrayHelper::getValue($response_item, 'body.title').";".ArrayHelper::getValue($response_item, 'body.category_id').";".ArrayHelper::getValue($response_item, 'body.status')."\n");
                                break;
                            }
                        }
                    }
                }
                
                echo "\n Scroll: ".$i++;
               $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);


                // $response_order = $meli->get("/users/435343067/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);



            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 