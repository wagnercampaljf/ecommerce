<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class BuscaCopiasMLAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5c4f26ef9b69e60006493768-193724256");
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            /*$response_order = $meli->get('/sites/MLA/search?q="cabine%20ford"&search_type=scan');//&access_token=' . $meliAccessToken);
            print_r($response_order); 
            $response_order = $meli->get('/sites/MLA/search?q="cabine%20ford"&search_type=scan&scroll_id=2');//&access_token=' . $meliAccessToken);
            print_r($response_order); 
            die;*/
            
            /*$response_busca = $meli->get('/sites/MLB/search?q="'.str_replace(" ", "%20", "Botao Farol E Ventilador Scania 94 114 124 P93 R113 T113 R14").'"&search_type=scan');
            $produtos_encontrados = array();
            foreach (ArrayHelper::getValue($response_busca, 'body.results') as $busca){
                //print_r($busca);
                echo "\nID do vendedor:".$busca->seller->id;
                //if ($busca->seller->id == 3031436 || $busca->seller->id == 193724256){
                if ($busca->seller->id == 30314367 && $busca->title == "Botao Farol E Ventilador Scania 94 114 124 P93 R113 T113 R14"){
                    $produtos_encontrados[] = $busca->permalink;
                }
            }
            print_r($produtos_encontrados);
            die;*/
            
            $x = 0;
            $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            $produtos_encontrados = array();
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                    /*$produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id])->one();
                    //echo "PRODUTO_FILIAL: ";var_dump($produto_filial);
                    if ($produto_filial == NULL){
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        if (ArrayHelper::getValue($response_item, 'body.status') <> "closed" and ArrayHelper::getValue($response_item, 'body.status') <> "paused"){
                            echo "\n".++$x." - MELI_ID: ".$meli_id." - ".ArrayHelper::getValue($response_item, 'body.start_time')." - ".ArrayHelper::getValue($response_item, 'body.status')." - ".ArrayHelper::getValue($response_item, 'body.permalink')."\n";
                        }
                    }*/
                    $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                    echo "\n".++$x." - MELI_ID: ".$meli_id." - "." - ".ArrayHelper::getValue($response_item, 'body.permalink')."\n";
                    
                    $response_busca = $meli->get('/sites/MLB/search?q="'.str_replace(" ", "%20", ArrayHelper::getValue($response_item, 'body.title')).'"&search_type=scan');
                    foreach (ArrayHelper::getValue($response_busca, 'body.results') as $busca){
                        if ($busca->seller->id == 30314367 && $busca->title == ArrayHelper::getValue($response_item, 'body.title')){
                            echo "Link cópia: ".$busca->permalink."\n";
                            $produtos_encontrados[] = $busca->permalink;
                        }
                    }
                    if (count($produtos_encontrados) == 0) {
                        echo "Não encontradas  cópias do produto";
                    }
                    //print_r($response_order);
                }
                print_r($produtos_encontrados);
                die;
                $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
            
            print_r($produtos_encontrados);
            
        }
        
        echo "\n\nFIM!\n\n";
    }
}