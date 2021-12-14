<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\Imagens;

class CorrigirAnunciosComErrosAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/produtos_com_categoria_recomendada_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;meli_id;nome;status;categoria cadastrada meli_id;categoria cadastrada nome;categoria recomendada meli_id;categoria recomendada nome\n");
        
        //$filial     = Filial::find()->andWhere(['=', 'id', 72])->one();
        $filial     = Filial::find()->andWhere(['=', 'id', 98])->one();
        $meli       = new Meli(static::APP_ID, static::SECRET_KEY);
        $user       = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response   = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            //echo "\n\n".$meliAccessToken."\n\n"; die;
            
            $x = 0;
            $i = 0;
            //$response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            $response_order = $meli->get("/users/435343067/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                
                if ($i >= 0){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                        //break;
                        
                        echo "\n".++$x." - MELI_ID: ".$meli_id." - ";
                        
                        if($x <= 0){
                            echo " - pular";
                            continue;
                        }
                        
                        //$meli_id = "MLB1928937418";
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        //print_r($response_item); die;
                        print_r($response_item["body"]->status); 
                        if($response_item["body"]->status == "under_review"){
                            foreach ($response_item["body"]->sub_status as $substatus){
                                echo " - "; print_r($substatus); 
                                
                                fwrite($arquivo_log, "\n".$meli_id.';"'.ArrayHelper::getValue($response_item, 'body.title').'";"'.ArrayHelper::getValue($response_item, 'body.status').'";"'.ArrayHelper::getValue($response_item, 'body.category_id').'"');
                                
                                //Obter dados da categoria recomendada
                                $categoria_meli_id              = "";
                                $nome                           = str_replace(" ","%20",ArrayHelper::getValue($response_item, 'body.title'));
                                $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=".$nome);
                                if ($response_categoria_recomendada['httpCode'] >= 300) {
                                    echo " - ERRO Categoria Recomendada";
                                    fwrite($arquivo_log, ";;Categoria recomendada não encontrada");
                                    continue;
                                }
                                else {
                                    echo " - OK Categoria Recomendada";

                                    $response_categoria_dimensoes = $meli->get("categories/".$categoria_meli_id."/shipping");
                                    if ($response_categoria_dimensoes['httpCode'] >= 300) {
                                        echo " - ERRO Dimensoes";
                                        fwrite($arquivo_log, ";");
                                    } else {
                                        echo " - OK Dimensoes";

                                        $response_categoria_frete = $meli->get("/users/435343067/shipping_options/free?dimensions=".ArrayHelper::getValue($response_categoria_dimensoes, 'body.height')."x".ArrayHelper::getValue($response_categoria_dimensoes, 'body.width')."x".ArrayHelper::getValue($response_categoria_dimensoes, 'body.length').",".ArrayHelper::getValue($response_categoria_dimensoes, 'body.weight'));
                                        if ($response_categoria_frete['httpCode'] >= 300) {
                                            echo " - ERRO Frete";
                                            fwrite($arquivo_log, ";");
                                        } else {
                                            echo " - OK Frete";
                                            fwrite($arquivo_log, ';"'.ArrayHelper::getValue($response_categoria_frete, 'body.coverage.all_country.list_cost').'"');
                                        }
                                    }
                                    
                                    $categoria_meli_id      = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_id');
                                    $categoria_meli_nome    = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_name');
                                    fwrite($arquivo_log, ';"'.$categoria_meli_id.'";"'.$categoria_meli_nome.'"');
                                }
                                
                                $body = ["category_id" => $categoria_meli_id];
                                $response = $meli->put("items/{$meli_id}?access_token=" . $meliAccessToken, $body, [] );
                                //print_r($response);
                                if ($response['httpCode'] < 300) {
                                    fwrite($arquivo_log, ";Anúncio corrigido;".$response["body"]->permalink);
                                    echo " - Anúncio corrigido";
                                    echo "\n".$response["body"]->permalink."\n";
                                }
                                else {
                                    fwrite($arquivo_log, ";Anúncio não corrigido");
                                    echo " - Anúncio não corrigido";
                                }
                                
                                //die;
                            }
                        }
                    }
                }
                
                echo "\n Scroll: ".$i++;
                $response_order = $meli->get("/users/435343067/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
                //print_r($response_order);
            }
        }


        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }


}
 