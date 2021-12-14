<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Subcategoria;

class ObterDadosProdutosPublicadosAction extends Action
{  
    
    public function run($cliente = 1){
        
        echo "INÍCIO\n\n";
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken('TG-5c4f26ef9b69e60006493768-193724256');
        $response = ArrayHelper::getValue($user, 'body');
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $x = 0;
            $y = 0;
            
            $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            //$response_order = $meli->get("/orders/1914659251?access_token=" . $meliAccessToken);
            //print_r($response_order);            
            //die;
            
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                //print_r($response_order);
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                    $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id])->one();
                    //echo "PRODUTO_FILIAL: ";var_dump($produto_filial);
                    echo "\n".++$x;
                    if ($produto_filial == NULL){
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        if (ArrayHelper::getValue($response_item, 'body.status') <> "closed" and ArrayHelper::getValue($response_item, 'body.status') <> "paused"){
                            echo " - MELI_ID: ".$meli_id." - ".ArrayHelper::getValue($response_item, 'body.start_time')." - ".ArrayHelper::getValue($response_item, 'body.status')." - ".ArrayHelper::getValue($response_item, 'body.permalink')."\n";
                            
                            //print_r($response_item);
                            $response_categoria_status = $meli->get("/users/193724256/shipping_modes?category_id=".ArrayHelper::getValue($response_item, 'body.category_id'));
                            foreach (ArrayHelper::getValue($response_categoria_status, 'body') as $categoria_status){
                                print_r($categoria_status);
                            }
                        }
                    }
                }
                $y++;
                $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
        }
        
        echo "\n\nFIM!\n\n";
    }
}