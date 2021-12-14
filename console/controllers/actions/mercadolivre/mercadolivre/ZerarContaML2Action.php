<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class ZerarContaML2Action extends Action
{
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        //LOG da Vericação
        if (file_exists("/var/tmp/log_zerar_conta_2_ML.csv")){
            unlink("/var/tmp/log_zerar_conta_2_ML.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_zerar_conta_2_ML.csv", "a");
        fwrite($arquivo_log, "MELI_ID;STATUS");
        //LOG da Vericação
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        //$user = $meli->refreshAccessToken('TG-5c4f26ef9b69e60006493768-193724256');
        $user = $meli->refreshAccessToken('TG-5cb495b0eefc400006279a24-390464083');
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $x = 0;
            $response_order = $meli->get("/users/390464083/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                    
                    echo "\n".++$x." - MELI_ID: ".$meli_id;
                    fwrite($arquivo_log, "\n".$meli_id.";");
                    
                    $body = ["available_quantity" => 0];
                    $response_zerar = $meli->put("items/".$meli_id."?access_token=" . $meliAccessToken,$body,[]);
                    if ($response_zerar['httpCode'] >= 300) {
                        fwrite($arquivo_log, "Não Zerado");
                        echo " - Não Zerado";
                    }
                    else{
                        fwrite($arquivo_log, "Zerado");
                        echo " - Zerado";
                    }
                }
                $response_order = $meli->get("/users/390464083/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }            
        }
        
        //LOG Fecha o arquivo
        fclose($arquivo_log); 
        
        echo "\n\nFIM!\n\n";
    }
}