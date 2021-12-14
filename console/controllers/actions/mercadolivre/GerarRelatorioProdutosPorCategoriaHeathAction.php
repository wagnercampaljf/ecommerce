<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Imagens;

class GerarRelatorioProdutosPorCategoriaHeathAction extends Action
{
    
    
    public function run($cliente = 1){
        
        echo "INÍCIO\n\n";

        // Escreve no log
        $arquivo_log = fopen("/var/tmp/produtos_por_categoria".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;nome;categoria_meli_id;quantidade;status_ml;health;produto_filial_id;vinculação\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5d3ee920d98a8e0006998e2b-193724256");

        $response = ArrayHelper::getValue($user, 'body');
        
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            //echo "\n\n".$meliAccessToken."\n\n";
            
            $x = 0;
            $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                    $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                    //print_r($response_item);
                    
                    $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id])->one();
                    
                    $status_site        = "Produto Não Vinculado";
                    $produto_filial_id  = (int) null;
                    if($produto_filial){
                        $status_site        = "Produto Vinculado";
                        $produto_filial_id  = $produto_filial->id;
                    }
                    
                    fwrite($arquivo_log, $meli_id.";".ArrayHelper::getValue($response_item, 'body.title').";".ArrayHelper::getValue($response_item, 'body.category_id').";".ArrayHelper::getValue($response_item, 'body.available_quantity').";".ArrayHelper::getValue($response_item, 'body.status').";".ArrayHelper::getValue($response_item, 'body.health').";".$produto_filial_id.";".$status_site."\n");
                    
                    echo "\n".++$x." - MELI_ID: ".$meli_id;
                }

                $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
