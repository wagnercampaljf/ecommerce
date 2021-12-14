<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Filial;

class VerificarLentesAction extends Action
{

    public function run($cliente = 1){
        
        echo "INÍCIO\n\n";
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/verificar_lentes_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "nome;categoria_nome;categoria_meli_id;link_categoria_recomendada");
        
        $filiais    = Filial::find()    ->andWhere(["<>", "id", 96])
                                        ->andWhere(["<>", "id", 98])
                                        ->andWhere(["<>", "id", 100])
                                        ->andWhere(["is not", "refresh_token_meli", null])
                                        ->orderBy(["id" => SORT_ASC])
                                        ->all();

        foreach($filiais as $i => $filial){
            
            echo "\n\nFILIAL: ".$filial->nome."\n\n";
            
            if($filial->id <= 38){
                echo "\n\nPULAR FILIAL\n\n";
                continue;
            }

            $user     = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            $meliAccessToken = $response->access_token;
            
            $produtos_filiais = ProdutoFilial::find()->join("LEFT JOIN", "produto", "produto.id = produto_filial.produto_id")
                                                     ->andWhere(["like", "nome", "LENTE"])
                                                     ->andWhere(["=", "filial_id", $filial->id])
                                                     //->andWhere(["=", "id", 325340])
                                                     //->andWhere([">", "quantidade", 0])
                                                     ->orderBy(["id" => SORT_ASC])
                                                     ->all();
                                                     
            foreach($produtos_filiais as $k => $produto_filial){
                
                echo "\n".$k." - ".$produto_filial->filial_id." - ".$produto_filial->id." - ".$produto_filial->produto->nome;
                
                $nome_array         = explode(" ", $produto_filial->produto->nome);
                $nome               =   $nome_array[0]
                                        .((array_key_exists(1,$nome_array)) ? "%20".$nome_array[1] : "")
                                        .((array_key_exists(2,$nome_array)) ? "%20".$nome_array[2] : "");

                $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=".$nome);
                //print_r($response_categoria_recomendada);
                if ($response_categoria_recomendada['httpCode'] >= 300) {
                    echo " - ERRO Categoria Recomendada";
                    
                    //print_r($response_categoria_recomendada);
                    
                    $categoria_meli_id = "MLB191833";
                }
                else {
                    
                    echo " - OK Categoria Recomendada";
                    $categoria_meli_id      = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_id');
                    $categoria_meli_nome    = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_name');
                    echo " - ".$categoria_meli_id.' - '.$categoria_meli_nome;
                    
                    fwrite($arquivo_log, "\n".$produto_filial->produto->nome.";".$categoria_meli_nome.";".$categoria_meli_id.";https://api.mercadolibre.com/sites/MLB/domain_discovery/search?q=".$nome);
                    
                }
                
                continue;
                
                if($k < 0){
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
 