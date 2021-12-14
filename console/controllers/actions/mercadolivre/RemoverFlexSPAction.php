<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\Imagens;

class RemoverFlexSPAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/remover_flex_sp_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;nome;categoria;preço;link\n");
        
        $filial = Filial::find()->andWhere(['=', 'id', 72])->one();
        //$filial = Filial::find()->andWhere(['=', 'id', 98])->one();
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            //echo "\n\n".$meliAccessToken."\n\n";
            
            $x = 0;
            $i = 0;
            $response_itens = $meli->get("/users/193724256/items/search?search_type=scan&status=active&shipping_cost=free&limit=100&orders=price_asc&access_token=" . $meliAccessToken);
            
            //$response_itens = $meli->get("/sites/MLB/search?seller_id=193724256&available_filters&access_token=" . $meliAccessToken);
            //print_r($response_itens); die;
            
            while (ArrayHelper::getValue($response_itens, 'httpCode') <> 404){
                
                if ($i >= 0){
                    foreach (ArrayHelper::getValue($response_itens, 'body.results') as $meli_id){

                        echo "\n".++$x." - MELI_ID: ".$meli_id;
                        //continue;
                        
                        if($x < 10126){
                            echo " - pular";
                            continue;
                        }
                        
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        echo " - ".$response_item['body']->status;

                        //echo "\nPermalink: ";print_r(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : "");
                        //echo "Quantidade vendida: ";print_r($response_item["body"]->sold_quantity);
                        
                        $e_flex = false;
                        foreach($response_item["body"]->shipping->tags as $tag){
                            //echo "\nTag: ".$tag;
                            if($tag == "self_service_in"){
                                $e_flex = true;
                                break;
                            }
                        }
                        
                        $produto_filial = ProdutoFilial::find() ->orWhere(["=", "meli_id", $meli_id])
                                                                ->orWhere(["=", "meli_id_sem_juros", $meli_id])
                                                                ->orWhere(["=", "meli_id_full", $meli_id])
                                                                ->orWhere(["=", "meli_id_flex", $meli_id])
                                                                ->one();
                        
                        $e_fisica = false;                                                                
                        if($produto_filial){
                            if($produto_filial->filial_id == 96){
                                $e_fisica = true;
                            }
                        }
                        
                        echo $e_fisica ? " - É física" : " - Não é física";
                                                                
                        if($e_flex){
                            echo " - Anúncio Flex";
                            //$response = $meli->delete("/sites/MLB/shipping/selfservice/items/{$produto_filial_duplicado->meli_id_flex}?access_token=" . $meliAccessToken, [] );
                            //print_r($response["httpCode"]);
                            //fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial_duplicado->id.";meli_id_sem_juros;".$produto_filial_duplicado->meli_id_flex.";sim;".$response["httpCode"].";".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                        }
                        else{
                            echo " - Anúncio fora Flex";
                            //fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial_duplicado->id.";meli_id_sem_juros;".$produto_filial_duplicado->meli_id_flex.";não;".";".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                        }
                        
                        if(!$e_fisica && $e_flex){
                            echo "                       - ERRO";
                            $response = $meli->delete("/sites/MLB/shipping/selfservice/items/{$meli_id}?access_token=" . $meliAccessToken, [] );
                            print_r($response["httpCode"]);
                        }
                        
                        //fwrite($arquivo_log, $meli_id.";".ArrayHelper::getValue($response_item, 'body.title').";".ArrayHelper::getValue($response_item, 'body.category_id').";".ArrayHelper::getValue($response_item, 'body.price').";".ArrayHelper::getValue($response_item, 'body.permalink')."\n");
                        
                    }
                }
                
                echo "\n Scroll: ".$i++;
                $response_itens = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_itens, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 