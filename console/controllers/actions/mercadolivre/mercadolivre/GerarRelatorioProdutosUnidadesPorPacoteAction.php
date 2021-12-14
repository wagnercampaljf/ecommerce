<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Imagens;

class GerarRelatorioProdutosUnidadesPorPacoteAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/produtos_unidades_por_pacote_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;nome;categoria\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5e2efe08144ef6000642cdb6-193724256");
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            //echo "\n\n".$meliAccessToken."\n\n";
            
            $x = 0;
            $i = 0;
            $response_itens = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            
            while (ArrayHelper::getValue($response_itens, 'httpCode') <> 404){
                
                if ($i >= 0){
                    foreach (ArrayHelper::getValue($response_itens, 'body.results') as $meli_id){
                        
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        //print_r($response_item);
                        echo "\n".$meli_id.": ";
                        
                        foreach(ArrayHelper::getValue($response_item, 'body.attributes') as $atributo){
                            echo ArrayHelper::getValue($atributo, 'id')." - ";
                            if(ArrayHelper::getValue($atributo, 'id')== "UNITS_PER_PACKAGE"){
                                fwrite($arquivo_log, $meli_id.";".ArrayHelper::getValue($response_item, 'body.title').";".ArrayHelper::getValue($atributo, 'id').";".ArrayHelper::getValue($atributo, 'value_name')."\n");
                            }
                        }
                        
                        echo "\n".++$x." - MELI_ID: ".$meli_id;
                        
                    }
                }

                echo "\n Scroll: ".$i++;
                $response_itens = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_itens, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 