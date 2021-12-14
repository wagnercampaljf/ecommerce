<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\Imagens;
use common\models\Filial;

class ExcluirProdutosPorNomeAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/produtos_por_categoria".date("Y-m-d_H-i-s").".csv", "a");
        //fwrite($arquivo_log, "Conta Duplicada\n\nmeli_id;nome;categoria\n");
        fwrite($arquivo_log, "Conta Principal\n\nmeli_id;nome;categoria\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filial = Filial::find()->andWhere(['=', 'id', 72])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response_token = ArrayHelper::getValue($user, 'body');
        
        $filial_conta_duplicada = Filial::find()->andWhere(['=', 'id', 98])->one();
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');

        if (is_object($response_token) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response_token->access_token;

            $produtos_filiais = ProdutoFilial::find()   //->joinWith('produto')
                                                        //->andWhere(['like', 'upper(produto.nome)', "LENTE"])
                                                        //->andWhere(['is not', 'meli_id', null])
                                                        //->andWhere(['produto_filial.id' => [206940, 302495]])
                                                        ->andWhere(['produto_id' => [250070]])
                                                        ->orderBy('produto_filial.id')
                                                        ->all();
            
            foreach($produtos_filiais as $k => $produto_filial){
                echo "\n".$k." - ".$produto_filial->id." - ".$produto_filial->meli_id." - Filial: ".$produto_filial->filial_id;
                
                $meliAccessToken = $response_token->access_token;
                if($produto_filial->filial_id == 98){
                    $meliAccessToken = $response_conta_duplicada->access_token;
                }
                
                $response_item = $meli->get("/items/".$produto_filial->meli_id."?access_token=" . $meliAccessToken);
                
                if(isset($response_item["body"]->permalink)){
                    echo "\n".$response_item["body"]->permalink;
                }
                
                //continue;
                
                if(isset($response_item["body"]->category_id)){
                    /*if($response_item["body"]->category_id != "MLB180293"){
                        echo " - pular";
                        continue;
                    }*/
                    
                    echo "\n";
                    
                    $body = ["status" => 'closed',];
                    $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                    
                    if ($response['httpCode'] >= 300) {
                        echo " - Error_closed";
                    }
                    else{
                        echo " - OK Closed";
                    }
                    
                    $body = ["status" => 'deleted',];
                    $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                    
                    if ($response['httpCode'] >= 300) {
                        echo " - Error_deleted\n\n";
                    }           
                    else{
                        echo " - OK Deletado\n\n";
                    }

                }
            }
            
            die;
            
            $x = 0;
            $i = 0;
            
            //$response_order = $meli->get("/users/435343067/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                
                if ($i >= 0){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                        
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        //$response_item = $meli->get("/items/MLB1378297584?access_token=" . $meliAccessToken);

                        echo "\n".++$x." - MELI_ID: ".$meli_id;
                        
                        if(!isset($response_item["body"]->category_id)){
                            echo " - sem categoria";
                            continue;
                        }
                        
                        if($response_item["body"]->category_id != "MLB180293"){
                            continue;
                        }
                        
                        fwrite($arquivo_log, $meli_id.";".ArrayHelper::getValue($response_item, 'body.title').";".ArrayHelper::getValue($response_item, 'body.category_id'));
                        
                        $body = [
                            "category_id" => utf8_encode("MLB191833"),
                            //"category_id" => utf8_encode("MLB63851"),
                        ];
                        $response = $meli->put("items/{$meli_id}?access_token=" . $meliAccessToken, $body, []);
                        print_r($response);
                        
                        if ($response['httpCode'] >= 300) {
                            echo " - Categoria não atualizada no Mercado Livre";
                            fwrite($arquivo_log, ";Categoria não atualizada no Mercado Livre\n");
                        }
                        else {
                            echo "Categoria atualizada no Mercado Livre";
                            fwrite($arquivo_log, ";Categoria atualizada no Mercado Livre\n");
                        }
                        die;
                        
                    }
                }
                
                echo "\n Scroll: ".$i++;
                //$response_order = $meli->get("/users/435343067/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
                $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 