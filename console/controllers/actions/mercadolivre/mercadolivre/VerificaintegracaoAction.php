<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class VerificaintegracaoAction extends Action
{
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        //LOG da Vericação
        if (file_exists("/var/tmp/log_verificacao_correspondencia_ML.csv")){
            unlink("/var/tmp/log_verificacao_correspondencia_ML.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_verificacao_correspondencia_ML.csv", "a");
        fwrite($arquivo_log, "MELI_ID;DATA_CRIACAO;STATUS;LINK\n");
        //LOG da Vericação
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        //$user = $meli->refreshAccessToken('TG-5c4f26ef9b69e60006493768-193724256');
        $user = $meli->refreshAccessToken('TG-5cb495b0eefc400006279a24-390464083');
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $x = 0;
            $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                    $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id])->one();
                    //echo "PRODUTO_FILIAL: ";var_dump($produto_filial);
                    
                    if ($produto_filial == NULL){
                        print_r($meli_id);echo "\n";
                        print_r($produto_filial);
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        //print_r($response_item);die;
                        if (ArrayHelper::getValue($response_item, 'body.status') <> "closed" and ArrayHelper::getValue($response_item, 'body.status') <> "paused"){
                            echo ++$x." - MELI_ID: ".$meli_id."\n"." - ".ArrayHelper::getValue($response_item, 'body.start_time')." - ".ArrayHelper::getValue($response_item, 'body.status')." - ".ArrayHelper::getValue($response_item, 'body.permalink')."\n";
                            fwrite($arquivo_log, $meli_id.";".ArrayHelper::getValue($response_item, 'body.start_time').";".ArrayHelper::getValue($response_item, 'body.status').";".ArrayHelper::getValue($response_item, 'body.permalink')."\n");
                        }
                    }                        
                }
                $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }            
        }
        
        //LOG Fecha o arquivo
        fclose($arquivo_log); 
        
        echo "\n\nFIM!\n\n";
    }
}