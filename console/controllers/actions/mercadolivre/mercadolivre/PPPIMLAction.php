<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class PPPIMLAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/denuncia_produtos_clonados_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "Empresa ID;URL;ID Denuncia PPP14;response_pppi4;ID Denuncia PPP16;response_pppi6\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        
        $LinhasArray = Array();
        //$file = fopen('/var/tmp/busca_produtos_clonados_parcial_12-07-2019.csv', 'r');
        //$file = fopen('/var/tmp/busca_produtos_clonados_parcial_15-07-2019.csv', 'r');
        //$file = fopen('/var/tmp/busca_produtos_clonados_completa_sem_galvao_18-07-2019.csv', 'r');
        $file = fopen('/var/tmp/produtos_clonados_GALVAO_completa.csv', 'r');
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        foreach ($LinhasArray as $k => &$linhaArray){
            
            echo "\n".$k." - ";
            
            if ($k <= 0){
                continue;
            }
            
            $meli_id = explode("-",str_replace("https://produto.mercadolivre.com.br/","",$linhaArray[5]));
            
            $meli_id_conc = $meli_id[0].$meli_id[1];
            echo $meli_id_conc;
            
            fwrite($arquivo_log, "\n".$linhaArray[4].";".$linhaArray[5].";");
            
            $body = [
                "report_reason_id" => "PPPI4",
                "comment" => "",
            ];
            $response_pppi = $meli->post("/moderations/denounces/items/".$meli_id_conc."?access_token=APP_USR-3822451133228935-071913-4753a8a0968d2727e6a82592c26599f1-449329745", $body);
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
            $response_pppi = $meli->post("/moderations/denounces/items/".$meli_id_conc."?access_token=APP_USR-3822451133228935-071913-4753a8a0968d2727e6a82592c26599f1-449329745", $body);
            if (ArrayHelper::getValue($response_pppi, 'httpCode') < 300){
                fwrite($arquivo_log, ";".ArrayHelper::getValue($response_pppi, 'body.denounce_id').";".ArrayHelper::getValue($response_pppi, 'httpCode'));
            }
            else{
                fwrite($arquivo_log, ";;".ArrayHelper::getValue($response_pppi, 'httpCode'));
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}




 