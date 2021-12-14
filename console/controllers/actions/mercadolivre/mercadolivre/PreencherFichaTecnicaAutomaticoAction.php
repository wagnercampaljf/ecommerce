<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class PreencherFichaTecnicaAutomaticoAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/categorias_ML_2019-09-03.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5d3ee920d98a8e0006998e2b-193724256");
        $response = ArrayHelper::getValue($user, 'body');
        
        $faixas = array();
        $faixas = [
            1 => array(0 , 0.99 , 24, true),
        ];
        
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            foreach ($LinhasArray as $k => &$linhaArray ){
                
                if ($k <= 0){
                    continue;
                }
                
                $categoria_meli_id = $linhaArray[0];
                
                echo "\n".$k." - ".$categoria_meli_id; 

                //$response_categoria = $meli->get("/categories/".$categoria_meli_id."/attributes");
                $response_categoria = $meli->get("/categories/MLB271124/attributes");
                //print_r($response_categoria);
                //die;
                foreach (ArrayHelper::getValue($response_categoria, 'body') as $k => $categoria_atributos){
                    //echo "\n".$k." - ".ArrayHelper::getValue($meli_itens, 'id');
                    print_r($categoria_atributos);
                    
                    /*$response_atributos = $meli->post("/moderations/denounces/items/".ArrayHelper::getValue($meli_itens, 'id')."?access_token=APP_USR-3822451133228935-080111-7df18a1ebe0e261e8a50bd342fa209dc-449329745", $body);
                    if (ArrayHelper::getValue($response_atributos, 'httpCode') < 300){
                        
                    }*/
                }
                die;
            }
        }
    }
}




 