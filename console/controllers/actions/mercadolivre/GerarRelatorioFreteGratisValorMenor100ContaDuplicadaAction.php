<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\Imagens;

class GerarRelatorioFreteGratisValorMenor100ContaDuplicadaAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        //$arquivo_log = fopen("/var/tmp/principal_produtos_frete_gratis_valor_menor_100_".date("Y-m-d_H-i-s").".csv", "a");
        $arquivo_log = fopen("/var/tmp/conta_duplicada_produtos_frete_gratis_valor_menor_100_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;nome;categoria;preço;link\n");
        
        //$filial = Filial::find()->andWhere(['=', 'id', 72])->one();
        $filial = Filial::find()->andWhere(['=', 'id', 98])->one();
        echo 111;
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        echo 222;
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        print_r($user);
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            //echo "\n\n".$meliAccessToken."\n\n";
            
            $x = 0;
            $i = 0;
            //$response_itens = $meli->get("/users/193724256/items/search?search_type=scan&status=active&shipping_cost=free&limit=100&orders=price_asc&access_token=" . $meliAccessToken);
            $response_itens = $meli->get("/users/435343067/items/search?search_type=scan&status=active&shipping_cost=free&limit=100&orders=price_asc&access_token=" . $meliAccessToken);
            
            //$response_itens = $meli->get("/sites/MLB/search?seller_id=193724256&available_filters&access_token=" . $meliAccessToken);
            //print_r($response_itens); die;
            
            while (ArrayHelper::getValue($response_itens, 'httpCode') <> 404){
                
                if ($i >= 0){
                    foreach (ArrayHelper::getValue($response_itens, 'body.results') as $meli_id){

                        echo "\n".++$x." - MELI_ID: ".$meli_id;
                        
                        if($x < 39000){ //82290 - principal //83604 - Conta Duplicada 
                            echo " - pular";
                            continue;
                        }
                        
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        //print_r($response_item); die;
                        echo " - ".$response_item['body']->price." - ".$response_item['body']->shipping->mode." - ".$response_item['body']->shipping->free_shipping." - ".$response_item['body']->status;

                        if($response_item['body']->status != "active"){
                            continue;
                        }
                        
                        if ($response_item['body']->price > 80){
                            continue;
                        }

                        if($response_item['body']->shipping->free_shipping){
                            echo "\n\n".$response_item['body']->permalink."\n\n";
                            
                            fwrite($arquivo_log, $meli_id.";".ArrayHelper::getValue($response_item, 'body.title').";".ArrayHelper::getValue($response_item, 'body.category_id').";".ArrayHelper::getValue($response_item, 'body.sold_quantity').";".ArrayHelper::getValue($response_item, 'body.price').";".ArrayHelper::getValue($response_item, 'body.permalink').";".$response_item['body']->shipping->free_shipping.";".$response_item['body']->shipping->mode.";".$response_item['body']->status."\n");
                            
                            $preco = $response_item['body']->price;
                            if($response_item['body']->price > 98){
                                $preco *=  1.1;
                            }    
                            
                            $body = [
                                "shipping"      => [
                                    "mode"          => "me2",
                                    "methods"       => [],
                                    "local_pick_up" => true,
                                    "free_shipping" => false,
                                    "logistic_type" => "cross_docking",
                                ],
                                "price" => round($preco,2)
                            ];
                            
                            $response = $meli->put("items/{$meli_id}?access_token=" . $meliAccessToken, $body, []);
                            if($response["httpCode"] >= 300){
                                echo " - Erro (Principal)";
                                $status = "Erro";
                                fwrite($arquivo_log, ";erro\n");
                            }
                            else{
                                echo " - OK (Principal)"." - ".$response["body"]->permalink;
                                fwrite($arquivo_log, ";".ArrayHelper::getValue($response, 'body.shipping.free_shipping').";".ArrayHelper::getValue($response, 'body.price')."\n");
                            }
                        }
                        else{
                            echo " - Produto cadastrado no modo correto";
                        }
                    }
                }
                
                echo "\n Scroll: ".$i++;
                //$response_itens = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_itens, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
                $response_itens = $meli->get("/users/435343067/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_itens, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
        }
         
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 
