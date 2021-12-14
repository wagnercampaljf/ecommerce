<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class BuscaCopiasMLFazerPerguntasAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/busca_produtos_clonados_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "PEÇA AGORA;Preço Peça Agora;GALVAOL1046;Preço Galvão\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5c4f26ef9b69e60006493768-193724256");
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            //Teste pergunta no ML
            
            /*$body = [
                "text"      => "Qual o frete para 36033315?",
                "item_id"   => "MLB1217513238"
            ];
            print_r($body);
            $response = $meli->post("/questions/"."MLB1217513238"."?access_token=".$meliAccessToken, $body, []);
            print_r($response);
            die;*/         
            
            //Teste pergunta no ML
            
            
            $x = 0;
            $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                    $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                    //print_r($response_item);
                    //die;
                    echo "\n".++$x." - MELI_ID: ".$meli_id." - "." - ".ArrayHelper::getValue($response_item, 'body.permalink')."\n";
                    
                    $response_busca = $meli->get('/sites/MLB/search?q="'.str_replace(" ", "%20", ArrayHelper::getValue($response_item, 'body.title')).'"&search_type=scan');
                    $produto_foi_encontrado = false;
                    foreach (ArrayHelper::getValue($response_busca, 'body.results') as $busca){
                        if ($busca->seller->id == 30314367 && $busca->title == ArrayHelper::getValue($response_item, 'body.title')){
                            echo "Link cópia: ".$busca->permalink;
                            $response_descricao = $meli->get("/items/".$busca->id."/description");
                            
                            if (ArrayHelper::keyExists('plain_text', $response_descricao, false)){
                                $texto = ArrayHelper::getValue($response_descricao, 'body.plain_text');
                                
                                if (strpos($texto, "PEÇA AGORA") ||strpos($texto, "Peça Agora")){
                                    fwrite($arquivo_log, ArrayHelper::getValue($response_item, 'body.permalink').";".ArrayHelper::getValue($response_item, 'body.price').";".$busca->permalink.";".$busca->price."\n");
                                    $produto_foi_encontrado = true;
                                }
                            }
                        }
                    }
                    if (!$produto_foi_encontrado) {
                        echo " - Não encontradas  cópias do produto";
                    }
                    else {
                        echo " - Produto encontrado";
                    }
                }

                $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}




 