<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class PPPIGalvaoAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/denuncia_produtos_clonados_GALVAO_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "Empresa ID;ID Denuncia PPP14;response_pppi4;ID Denuncia PPP16;response_pppi6\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5d3ee920d98a8e0006998e2b-193724256");
        $response = ArrayHelper::getValue($user, 'body');
        //print_r($response); die;
        
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            //print_r($meliAccessToken); die;
            
            //30314367 -> GALVAO
            for($x=0;$x<=10000;$x+=50){
                echo "\n".$x;
                $response_order = $meli->get("sites/MLB/search?seller_id=30314367&search_type=scan&offset=".$x."&access_token=" . $meliAccessToken);
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $meli_itens){
                    //$response_itens = $meli->get("/items/".ArrayHelper::getValue($meli_itens, 'id'));
                    
                    echo "\n".$k." - ".ArrayHelper::getValue($meli_itens, 'id');
                    
                    fwrite($arquivo_log, "\n30314367;");
                    
                    $body = [
                        "report_reason_id" => "PPPI4",
                        "comment" => "",
                    ];
                    $response_pppi = $meli->post("/moderations/denounces/items/".ArrayHelper::getValue($meli_itens, 'id')."?access_token=APP_USR-3822451133228935-080111-7df18a1ebe0e261e8a50bd342fa209dc-449329745", $body);
                    if (ArrayHelper::getValue($response_pppi, 'httpCode') < 300){
                        fwrite($arquivo_log, ArrayHelper::getValue($response_pppi, 'body.denounce_id').";".ArrayHelper::getValue($response_pppi, 'httpCode'));
                    }
                    else{
                        fwrite($arquivo_log, ";".ArrayHelper::getValue($response_pppi, 'httpCode'));
                    }
                    
                    $body = [
                        "report_reason_id" => "PPPI6",
                        "comment" => "",
                    ];
                    $response_pppi = $meli->post("/moderations/denounces/items/".ArrayHelper::getValue($meli_itens, 'id')."?access_token=APP_USR-3822451133228935-080111-7df18a1ebe0e261e8a50bd342fa209dc-449329745", $body);
                    if (ArrayHelper::getValue($response_pppi, 'httpCode') < 300){
                        fwrite($arquivo_log, ";".ArrayHelper::getValue($response_pppi, 'body.denounce_id').";".ArrayHelper::getValue($response_pppi, 'httpCode'));
                    }
                    else{
                        fwrite($arquivo_log, ";;".ArrayHelper::getValue($response_pppi, 'httpCode'));
                    }
                }
            }
        }
    }
}




 