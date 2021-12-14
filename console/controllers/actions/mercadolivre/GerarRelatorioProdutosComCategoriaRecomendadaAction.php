<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\Imagens;

class GerarRelatorioProdutosComCategoriaRecomendadaAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/produtos_com_categoria_recomendada_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;meli_id;nome;status;categoria cadastrada meli_id;categoria cadastrada nome;categoria recomendada meli_id;categoria recomendada nome\n");
        
        $filial     = Filial::find()->andWhere(['=', 'id', 72])->one();
        $meli       = new Meli(static::APP_ID, static::SECRET_KEY);
        $user       = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response   = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            //echo "\n\n".$meliAccessToken."\n\n";
            
            $x = 0;
            $i = 0;
            $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                
                if ($i >= 0){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                        //break;
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);

                        $produto_filial_id  = 0;
                        $produto_filial     = ProdutoFilial::find()->andWhere(['=', 'meli_id', $meli_id])->one();
                        if($produto_filial){
                            $produto_filial_id = $produto_filial->id;
                        }
                        
                        fwrite($arquivo_log, "\n".$produto_filial_id.';"'.$meli_id.'";"'.ArrayHelper::getValue($response_item, 'body.title').'";"'.ArrayHelper::getValue($response_item, 'body.status').'";"'.ArrayHelper::getValue($response_item, 'body.category_id').'"');
                        
                        echo "\n".++$x." - MELI_ID: ".$meli_id;
                        
                        //Obter dados da categoria cadastrada no produto
                        $response_categoria_cadastrada = $meli->get("categories/".ArrayHelper::getValue($response_item, 'body.category_id'));
                        
                        if ($response_categoria_cadastrada['httpCode'] >= 300) {
                            echo " - ERRO Categoria Cadastrada";
                            fwrite($arquivo_log, ";");
                        } else {
                            echo " - OK Categoria Cadastrada";
                            fwrite($arquivo_log, ';"'.ArrayHelper::getValue($response_categoria_cadastrada, 'body.name').'"');
                        }
                        
                        //Obter dados da categoria recomendada
                        $nome = str_replace(" ","%20",ArrayHelper::getValue($response_item, 'body.title'));
                        $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=".$nome);
                        if ($response_categoria_recomendada['httpCode'] >= 300) {
                            echo " - ERRO Categoria Recomendada";
                            fwrite($arquivo_log, ";;");
                        } 
                        else {
                            echo " - OK Categoria Recomendada";
                            
                            $categoria_meli_id      = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_id');
                            $categoria_meli_nome    = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_name');
                            fwrite($arquivo_log, ';"'.$categoria_meli_id.'";"'.$categoria_meli_nome.'"');
                        }
                    }
                }
                
                echo "\n Scroll: ".$i++;
                $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
                print_r($response_order);
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 