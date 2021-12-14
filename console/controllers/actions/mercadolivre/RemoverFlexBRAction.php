<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Filial;

class RemoverFlexBRAction extends Action
{

    public function run($cliente = 1){
        
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/remover_flex_br_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;produto_filial_duplicado_id;tipo_meli_id;meli_id;e_flex;status;link\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filial = Filial::find()->andWhere(["=", "id", 98])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 300) {
            $meliAccessToken = $response->access_token;
            
            //echo "\n\n".$meliAccessToken."\n\n";
            
            $produtos_filiais = ProdutoFilial::find()->andWhere(["=", "filial_id", 72])
                                                     //->andWhere(["=", "id", 325340])
                                                     //->andWhere([">", "quantidade", 0])
                                                     ->orderBy(["id" => SORT_ASC])
                                                     ->all();
            
            foreach($produtos_filiais as $k => $produto_filial){
                echo "\n".$k." - ".$produto_filial->id." - ".$produto_filial->produto->codigo_fabricante;
                
                if($k < 7400){
                    echo " - pular";
                    continue;
                }
                
                $produto_filial_duplicado = ProdutoFilial::find()->andWhere(["=", "produto_filial_origem_id", $produto_filial->id])
                                                                 ->andWhere(["=", "filial_id", 98])
                                                                 ->one();
                
                if($produto_filial_duplicado){
                    echo " - produto duplicado encontrado";
                     
                    if(!is_null($produto_filial_duplicado->meli_id) && $produto_filial_duplicado->meli_id != ""){
                        $response_item = $meli->get("/items/".$produto_filial_duplicado->meli_id."?access_token=" . $meliAccessToken);
                        //print_r($response_item); 
                        
                        echo "\nMELI ID";
                        echo "\nPermalink: ";print_r(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : "");
                        echo "\nQuantidade vendida: ";print_r($response_item["body"]->sold_quantity);
                        
                        $e_flex = false;
                        foreach($response_item["body"]->shipping->tags as $tag){
                            //echo "\nTag: ".$tag;
                            if($tag == "self_service_out"){
                                $e_flex = true;
                                break;
                            }
                        }
                        
                        if($e_flex){
                            echo "\nAnúncio Flex";
                            $response = $meli->delete("/sites/MLB/shipping/selfservice/items/{$produto_filial_duplicado->meli_id}?access_token=" . $meliAccessToken, [] );
                            print_r($response["httpCode"]);
                            fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial_duplicado->id.";meli_id_sem_juros;".$produto_filial_duplicado->meli_id.";sim;".$response["httpCode"].";".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                        }
                        else{
                            echo "\nAnúncio fora Flex";
                            fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial_duplicado->id.";meli_id_sem_juros;".$produto_filial_duplicado->meli_id.";não;".";".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                        }
                    }
                    if(!is_null($produto_filial_duplicado->meli_id_sem_juros) && $produto_filial_duplicado->meli_id_sem_juros != ""){
                        $response_item = $meli->get("/items/".$produto_filial_duplicado->meli_id_sem_juros."?access_token=" . $meliAccessToken);
                        //print_r($response_item);
                        
                        echo "\nMELI ID SEM JUROS";
                        echo "\nPermalink: ";print_r(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : "");
                        echo "Quantidade vendida: ";print_r($response_item["body"]->sold_quantity);
                        
                        $e_flex = false;
                        foreach($response_item["body"]->shipping->tags as $tag){
                            //echo "\nTag: ".$tag;
                            if($tag == "self_service_out"){
                                $e_flex = true;
                                break;
                            }
                        }
                        
                        if($e_flex){
                            echo "\nAnúncio Flex";
                            $response = $meli->delete("/sites/MLB/shipping/selfservice/items/{$produto_filial_duplicado->meli_id_sem_juros}?access_token=" . $meliAccessToken, [] );
                            print_r($response["httpCode"]);
                            fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial_duplicado->id.";meli_id_sem_juros;".$produto_filial_duplicado->meli_id_sem_juros.";sim;".$response["httpCode"].";".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                        }
                        else{
                            echo "\nAnúncio fora Flex";
                            fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial_duplicado->id.";meli_id_sem_juros;".$produto_filial_duplicado->meli_id_sem_juros.";não;".";".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                        }
                    }
                    if(!is_null($produto_filial_duplicado->meli_id_flex) && $produto_filial_duplicado->meli_id_flex != ""){
                        $response_item = $meli->get("/items/".$produto_filial_duplicado->meli_id_flex."?access_token=" . $meliAccessToken);
                        //print_r($response_item);
                        
                        echo "\nMELI ID SEM FLEX";
                        echo "\nPermalink: ";print_r(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : "");
                        echo "Quantidade vendida: ";print_r($response_item["body"]->sold_quantity);
                        
                        $e_flex = false;
                        foreach($response_item["body"]->shipping->tags as $tag){
                            //echo "\nTag: ".$tag;
                            if($tag == "self_service_out"){
                                $e_flex = true;
                                break;
                            }
                        }
                        
                        if($e_flex){
                            echo "\nAnúncio Flex";
                            $response = $meli->delete("/sites/MLB/shipping/selfservice/items/{$produto_filial_duplicado->meli_id_flex}?access_token=" . $meliAccessToken, [] );
                            print_r($response["httpCode"]);
                            fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial_duplicado->id.";meli_id_sem_juros;".$produto_filial_duplicado->meli_id_flex.";sim;".$response["httpCode"].";".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                        }
                        else{
                            echo "\nAnúncio fora Flex";
                            fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial_duplicado->id.";meli_id_sem_juros;".$produto_filial_duplicado->meli_id_flex.";não;".";".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                        }
                    }
                    //die;
                }
                else{
                    echo " - produto não duplicado encontrado";
                }
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 