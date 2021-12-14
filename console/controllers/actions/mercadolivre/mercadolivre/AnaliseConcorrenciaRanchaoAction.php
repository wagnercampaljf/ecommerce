<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Produto;

class AnaliseConcorrenciaRanchaoAction extends Action
{
    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/analise_concorrente_Ranchao_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "Nome;Preço;Data Criação;Quantidade Vendida;URL\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5d3ee920d98a8e0006998e2b-193724256");
        $response = ArrayHelper::getValue($user, 'body');
        //print_r($response); die;
        
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            print_r($meliAccessToken); die;
            
            //360447035 -> ALGOMAISPECAS
            for($x=0;$x<=2000;$x+=50){
                echo "\n".$x;
                $response_order = $meli->get("sites/MLB/search?seller_id=45927183&search_type=scan&offset=".$x."&access_token=" . $meliAccessToken);
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $meli_itens){
                    $response_itens = $meli->get("/items/".ArrayHelper::getValue($meli_itens, 'id'));
                    
                    echo "\n".$k." - ".ArrayHelper::getValue($response_itens, 'body.sold_quantity')." - ".ArrayHelper::getValue($response_itens, 'body.price');
                    
                    fwrite($arquivo_log,ArrayHelper::getValue($response_itens, 'body.title').";".
                                        ArrayHelper::getValue($response_itens, 'body.price').";".
                                        ArrayHelper::getValue($response_itens, 'body.start_time').";".
                                        ArrayHelper::getValue($response_itens, 'body.sold_quantity').";".   
                                        ArrayHelper::getValue($response_itens, 'body.permalink').";".
                                        "\n");
                }
            }
        }
    
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
