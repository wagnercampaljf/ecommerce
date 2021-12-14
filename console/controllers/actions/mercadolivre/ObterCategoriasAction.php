<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Subcategoria;

class ObterCategoriasAction extends Action
{
    
    
    public function run($cliente = 1){
        
        echo "INÍCIO\n\n";
        
        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_subcategorias_10-02-2020.csv", "a");
        //Escreve no log
        fwrite($arquivo_log, "id;nome;meli_id;cat_meli_nome;status\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken('TG-5e2efe08144ef6000642cdb6-193724256');
        $response = ArrayHelper::getValue($user, 'body');
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            //$response_order = $meli->get("/users/193724256/shipping_options/free?dimensions=31x47x69,2800");
            //print_r($response_order);die;
            
            $x = 0;
            //$response_order = $meli->get("/users/193724256/items/search?limit=100&access_token=" . $meliAccessToken);
            //$response_order = $meli->get("/items/MLB1166102475/shipping_options");
            //$response_order = $meli->get("/users/193724256/shipping_options/free?dimensions=45x25x230,500");
            //$response_order = $meli->get("/categories/MLB191833/shipping");
            //print_r($response_order);
            
            $subcategorias = Subcategoria::find()->orderBy('nome')->All();
            
            foreach ($subcategorias as $k => $subcategoria){
                $response_order = $meli->get("/categories/".$subcategoria->meli_id);
                fwrite($arquivo_log, "\n".$subcategoria->id.";".$subcategoria->nome.";".$subcategoria->meli_id.";".$subcategoria->meli_cat_nome.";");
                //print_r($response_order); continue;
                echo "\n".$k." - ".$subcategoria->nome;
                if (ArrayHelper::getValue($response_order, 'httpCode') != 404 && ArrayHelper::getValue($response_order, 'httpCode') != 400){
                    //print_r($response_order);
                    fwrite($arquivo_log, "OK");
                } else {
                    fwrite($arquivo_log, "ERRO");
                }               
            }
            
            
            die;
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                print_r($response_order);
                die;
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                    /*$produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id])->one();
                    //echo "PRODUTO_FILIAL: ";var_dump($produto_filial);
                    if ($produto_filial == NULL){
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        if (ArrayHelper::getValue($response_item, 'body.status') <> "closed" and ArrayHelper::getValue($response_item, 'body.status') <> "paused"){
                            echo ++$x." - MELI_ID: ".$meli_id." - ".ArrayHelper::getValue($response_item, 'body.start_time')." - ".ArrayHelper::getValue($response_item, 'body.status')." - ".ArrayHelper::getValue($response_item, 'body.permalink')."\n";
                        }
                    }*/
                }
                $response_order = $meli->get("/users/193724256/items/search?scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
        }
        
        echo "\n\nFIM!\n\n";
    }
}
