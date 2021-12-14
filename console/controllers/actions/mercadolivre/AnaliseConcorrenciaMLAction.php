<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Produto;

class AnaliseConcorrenciaMLAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/analise_concorrente_ALGOMAISPECAS_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "PEÇA AGORA Nome;ALGOMAISPECAS Nome;Peça Agora Preço;ALGOMAISPECAS Preço;Peça Agora Data Criação;ALGOMAISPECAS Data Criação;Peça Agora Quantidade Vendida;ALGOMAISPECAS Quantidade Vendidas;Peça Agora URL;ALGOMAISPECAS URL\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5d3ee920d98a8e0006998e2b-193724256");
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $y = 0;
            
            //360447035 -> ALGOMAISPECAS
            for($x=0;$x<=10000;$x+=50){
                echo "\n".$x;
                $response_order = $meli->get("sites/MLB/search?seller_id=360447035&search_type=scan&offset=".$x."&access_token=" . $meliAccessToken);
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_itens){
                    echo "\n".$y++." - ";
                    $response_itens = $meli->get("/items/".ArrayHelper::getValue($meli_itens, 'id'));
                    foreach(ArrayHelper::getValue($response_itens, 'body.attributes') as $atributos){
                        if (ArrayHelper::getValue($atributos, 'id') == "PART_NUMBER"){
                            print_r(ArrayHelper::getValue($atributos, 'value_name'));
                            
                            $produto = Produto::find()  ->andWhere(['like', 'codigo_global', ArrayHelper::getValue($atributos, 'value_name')])
                                                        ->one();
                            if ($produto){
                                $produto_filial = ProdutoFilial::find() ->andWhere(['=','produto_id', $produto->id])
                                                                        ->andWhere(['is not','meli_id',null])
                                                                        ->one();
                                if($produto_filial){
                                    $response_itens_peca_agora = $meli->get("/items/".$produto_filial->meli_id);
                                    if (ArrayHelper::getValue($response_itens_peca_agora, 'httpCode') < 300 && array_key_exists('price', $response_itens_peca_agora['body'])){
                                        echo " - CONCORRENTE";
                                        fwrite($arquivo_log,ArrayHelper::getValue($response_itens_peca_agora, 'body.title').";".
                                                            ArrayHelper::getValue($response_itens, 'body.title').";".
                                                            ArrayHelper::getValue($response_itens_peca_agora, 'body.price').";".
                                                            ArrayHelper::getValue($response_itens, 'body.price').";".
                                                            ArrayHelper::getValue($response_itens_peca_agora, 'body.start_time').";".
                                                            ArrayHelper::getValue($response_itens, 'body.start_time').";".
                                                            ArrayHelper::getValue($response_itens_peca_agora, 'body.sold_quantity').";".
                                                            ArrayHelper::getValue($response_itens, 'body.sold_quantity').";".   
                                                            ArrayHelper::getValue($response_itens_peca_agora, 'body.permalink').";".
                                                            ArrayHelper::getValue($response_itens, 'body.permalink').";".
                                                            "\n");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}



