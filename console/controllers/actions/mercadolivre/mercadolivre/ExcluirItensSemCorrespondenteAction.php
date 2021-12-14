<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class ExcluirItensSemCorrespondenteAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de exclusão de itens sem correspondencia: \n\n";
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken('TG-5c4f26ef9b69e60006493768-193724256');
        $response = ArrayHelper::getValue($user, 'body');
        
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $LinhasArray = Array();
            $file = fopen('/var/tmp/log_verificacao_correspondencia_ML.csv', 'r');
            while (($line = fgetcsv($file,null,';')) !== false)
            {
                $LinhasArray[] = $line;
            }
            fclose($file);
            
            if (file_exists("/var/tmp/log_exclusao_sem_correspondencia.csv")){
                unlink("/var/tmp/log_exclusao_sem_correspondencia.csv");
            }
            
            $arquivo_log = fopen("/var/tmp/log_exclusao_sem_correspondencia.csv", "a");
            // Escreve no log
            fwrite($arquivo_log, "MELI_ID;DATA_CRIACAO;STATUS;LINK\n");
            
            foreach ($LinhasArray as $k => &$linhaArray){
                
                if ($linhaArray[0]=='MELI_ID'){
                    continue;
                }
                
                print_r($linhaArray[0]); echo " - ";
                
                $response_item = $meli->get("/items/".$linhaArray[0]."?access_token=" . $meliAccessToken);
                
                //print_r($response_item);
                
                //Update Item
                $body = ["available_quantity" => 0];
                
                $response = $meli->put("items/".$linhaArray[0]."?access_token=" . $meliAccessToken, $body, []);
                
                print_r($response['httpCode']); echo "\n";
                
                // Escreve no log
                fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].";".$response['httpCode'].'\n');
            }
            
            // Fecha o arquivo
            fclose($arquivo_log);
        }
        
        echo "\n\nFIM da rotina de exclusão de itens sem correspondencia.";
    }
}