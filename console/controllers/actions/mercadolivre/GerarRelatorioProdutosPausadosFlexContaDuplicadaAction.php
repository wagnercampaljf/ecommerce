<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\Imagens;

class GerarRelatorioProdutosPausadosFlexContaDuplicadaAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        //$arquivo_log = fopen("/var/tmp/principal_produtos_frete_gratis_valor_menor_100_".date("Y-m-d_H-i-s").".csv", "a");
        $arquivo_log = fopen("/var/tmp/conta_duplicada_produtos_flex_pausados_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;nome;status;link\n");
        
        //$filial = Filial::find()->andWhere(['=', 'id', 72])->one();
        $filial = Filial::find()->andWhere(['=', 'id', 98])->one();
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            //echo "\n\n".$meliAccessToken."\n\n";
            
            $x = 0;
            $i = 0;
            //$response_itens = $meli->get("/users/193724256/items/search?search_type=scan&status=active&shipping_cost=free&limit=100&orders=price_asc&access_token=" . $meliAccessToken);
            $response_itens = $meli->get("/users/435343067/items/search?search_type=scan&limit=100&orders=price_asc&access_token=" . $meliAccessToken);
            
            //$response_itens = $meli->get("/sites/MLB/search?seller_id=193724256&available_filters&access_token=" . $meliAccessToken);
            //print_r($response_itens); die;
            
            while (ArrayHelper::getValue($response_itens, 'httpCode') <> 404){
                
                if ($i >= 0){
                    foreach (ArrayHelper::getValue($response_itens, 'body.results') as $meli_id){

                        echo "\n".++$x." - MELI_ID: ".$meli_id;
                        
                        if($x < 56795){ //82290 - principal //83604 - Conta Duplicada 
                            echo " - pular";
                            continue;
                        }
                        
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        //print_r($response_item); die;
                        echo " - ".$response_item['body']->available_quantity." - ".$response_item['body']->status;

                        //continue;
                        if($response_item['body']->available_quantity > 0 && ArrayHelper::getValue($response_item, 'body.status') == "under_review"){
                            echo " - Produto pausado";
                            fwrite($arquivo_log, $meli_id.";".ArrayHelper::getValue($response_item, 'body.title').";".ArrayHelper::getValue($response_item, 'body.status').";".ArrayHelper::getValue($response_item, 'body.permalink')."\n");
                        }
                        else{
                            echo " - Produto não pausado";
                        }
                    }
                }
                
                echo "\n Scroll: ".$i++;
                //$response_itens = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_itens, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
                $response_itens = $meli->get("/users/435343067/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_itens, 'body.scroll_id')."&limit=100&orders=price_asc&access_token=" . $meliAccessToken);
            }
        }
         
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 
