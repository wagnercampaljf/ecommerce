<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Filial;

class VerificarFlexAction extends Action
{

    public function run($cliente = 1){
        
        echo "INÍCIO\n\n";
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/verificar_flex_ci_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;produto_filial_duplicado_id;tipo_meli_id;meli_id;e_flex;status;link\n");
        
        $filiais    = Filial::find()    ->andWhere(["<>", "id", 96])
                                        ->andWhere(["<>", "id", 98])
                                        ->andWhere(["<>", "id", 100])
                                        ->andWhere(["is not", "refresh_token_meli", null])
                                        ->orderBy(["id" => SORT_ASC])
                                        ->all();

        foreach($filiais as $i => $filial){
            
            echo "\n\nFILIAL: ".$filial->nome."\n\n";
            
            if($filial->id < 72){
                echo "\n\nPULAR FILIAL\n\n";
                continue;
            }

            $user     = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            $meliAccessToken = $response->access_token;
            
            $produtos_filiais = ProdutoFilial::find()->andWhere(["=", "filial_id", $filial->id])
                                                     //->andWhere(["=", "id", 325340])
                                                     //->andWhere([">", "quantidade", 0])
                                                     ->orderBy(["id" => SORT_ASC])
                                                     ->all();
                                                     
            foreach($produtos_filiais as $k => $produto_filial){
                
                echo "\n".$k." - ".$produto_filial->filial_id." - ".$produto_filial->id." - ".$produto_filial->produto->codigo_fabricante;
                
                if($k < 24610 && $produto_filial->produto->filial_id = 72){
                    echo " - pular";
                    continue;
                }
                
                if(!is_null($produto_filial->meli_id) && $produto_filial->meli_id != ""){
                    $response_item = $meli->get("/items/".$produto_filial->meli_id."?access_token=" . $meliAccessToken);
                    //print_r($response_item);
                    
                    echo " - MELI ID";
                    //echo "\nPermalink: ";print_r(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : "");
                    //echo "\nQuantidade vendida: ";print_r($response_item["body"]->sold_quantity);
                    
                    $e_flex = false;
                    if(array_key_exists("shipping", $response_item["body"])){
                        foreach($response_item["body"]->shipping->tags as $tag){
                            //echo "\nTag: ".$tag;
                            if($tag == "self_service_in"){
                                $e_flex = true;
                                break;
                            }
                        }
                    }
                    
                    if($e_flex){
                        echo " - Anúncio Flex";
                        $response = $meli->delete("/sites/MLB/shipping/selfservice/items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, [] );
                        print_r($response);
                        fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial->id.";meli_id_sem_juros;".$produto_filial->meli_id.";sim;;".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                    }
                    else{
                        echo " - Anúncio fora Flex";
                        fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial->id.";meli_id_sem_juros;".$produto_filial->meli_id.";não;".";".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                    }
                }
                if(!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros != ""){
                    $response_item = $meli->get("/items/".$produto_filial->meli_id_sem_juros."?access_token=" . $meliAccessToken);
                    //print_r($response_item);
                    
                    echo " - MELI ID SEM JUROS";
                    //echo "\nPermalink: ";print_r(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : "");
                    //echo "Quantidade vendida: ";print_r($response_item["body"]->sold_quantity);
                    
                    $e_flex = false;
                    if(array_key_exists("shipping", $response_item["body"])){
                        foreach($response_item["body"]->shipping->tags as $tag){
                            //echo "\nTag: ".$tag;
                            if($tag == "self_service_in"){
                                $e_flex = true;
                                break;
                            }
                        }
                    }
                    
                    if($e_flex){
                        echo " - Anúncio Flex";
                        $response = $meli->delete("/sites/MLB/shipping/selfservice/items/{$produto_filial->meli_id_sem_juros}?access_token=" . $meliAccessToken, [] );
                        print_r($response["httpCode"]);
                        fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial->id.";meli_id_sem_juros;".$produto_filial->meli_id_sem_juros.";sim;;".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                    }
                    else{
                        echo " - Anúncio fora Flex";
                        fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial->id.";meli_id_sem_juros;".$produto_filial->meli_id_sem_juros.";não;".";".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                    }
                }
                if(!is_null($produto_filial->meli_id_flex) && $produto_filial->meli_id_flex != ""){
                    $response_item = $meli->get("/items/".$produto_filial->meli_id_flex."?access_token=" . $meliAccessToken);
                    //print_r($response_item);
                    
                    echo " - MELI ID SEM FLEX";
                    //echo "\nPermalink: ";print_r(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : "");
                    //echo "Quantidade vendida: ";print_r($response_item["body"]->sold_quantity);
                    
                    $e_flex = false;
                    if(array_key_exists("shipping", $response_item["body"])){
                        foreach($response_item["body"]->shipping->tags as $tag){
                            //echo "\nTag: ".$tag;
                            if($tag == "self_service_in"){
                                $e_flex = true;
                                break;
                            }
                        }
                    }
                    
                    if($e_flex){
                        echo " - Anúncio Flex";
                        $response = $meli->delete("/sites/MLB/shipping/selfservice/items/{$produto_filial->meli_id_flex}?access_token=" . $meliAccessToken, [] );
                        print_r($response["httpCode"]);
                        fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial->id.";meli_id_sem_juros;".$produto_filial->meli_id_flex.";sim;;".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                    }
                    else{
                        echo " - Anúncio fora Flex";
                        fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial->id.";meli_id_sem_juros;".$produto_filial->meli_id_flex.";não;".";".(property_exists($response_item["body"], "permalink") ? $response_item["body"]->permalink : ""));
                    }
                }
                //die;
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 