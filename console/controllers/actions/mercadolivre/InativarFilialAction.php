<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\Imagens;

class InativarFilialAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/inativar_filial_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;nome;categoria;preço;link\n");
        
        $filial = Filial::find()->andWhere(['=', 'id', 69])->one();
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $produtos_filiais = ProdutoFilial::find()->andWhere(["=", "filial_id", $filial->id])->orderBy(["id" => SORT_DESC])->all();
            
            foreach($produtos_filiais as $k => $produto_filial){
                
                $response_item = $meli->get("/items/".$produto_filial->meli_id."?access_token=" . $meliAccessToken);
                //print_r($response_item); die;
                
                echo "\n".$k." - ".$produto_filial->id." - ".$produto_filial->meli_id;
                if(property_exists($response_item["body"], "sold_quantity")){
                    echo " - Quant: ".$response_item["body"]->sold_quantity." - ";
                    
                    if($response_item["body"]->sold_quantity > 0){
                        $produto_filial->filial_id = 8;
                        var_dump($produto_filial->save());
                    }
                    else{
                        $body = ["available_quantity" => 0];
                        $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                        
                        $produto_filial->quantidade = 0;
                        var_dump($produto_filial->save());
                    }
                }
                else{
                    echo " - SEM QUANTIDADE VENDIDA";
                    
                    $body = ["available_quantity" => 0];
                    $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                    
                    $produto_filial->quantidade = 0;
                    var_dump($produto_filial->save());
                }
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 