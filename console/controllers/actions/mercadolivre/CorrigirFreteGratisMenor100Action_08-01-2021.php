<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\ValorProdutoFilial;

class CorrigirFreteGratisMenor100Action extends Action
{
    public function run()
    {
        
        $filial = Filial::find()->andWhere(['=','id',72])->one();
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;
        
        $filial_conta_duplicada = Filial::find()->andWhere(['=','id',98])->one();
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
        $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;
        
        $produtos_filial = ProdutoFilial::find()//->andWhere(['=', 'filial_id', $filial->id])
                                                ->andWhere(['is not','meli_id',null])
                                                ->andWhere(['<>','meli_id',""])
                                                ->andWhere(['=', 'meli_id', 'MLB1454158784'])
                                                ->andWhere(['<>', 'filial_id', 98])
                                                ->orderBy(['filial_id' => SORT_ASC, 'id'=>SORT_ASC])
                                                ->all();
        
        foreach ($produtos_filial as $k => $produto_filial){
            //print_r($produto_filial);
            
            echo "\n".$k." - ".$produto_filial->filial_id." - ".$produto_filial->id;
            
            /*if($k<=50783){
                echo" - pular";
                continue;
            }*/

            
            
            $valor_produto_filial = ValorProdutoFilial::find()  ->andWhere(['=','produto_filial_id', $produto_filial->id])
                                                                ->orderBy(['dt_inicio' => SORT_DESC])
                                                                ->one();
            
            $preco = round($valor_produto_filial->valor, 2);
            echo " - ".$preco;
            if($preco > 90){
                continue;
            }
            else{
                echo " - processar!";
            }
            
            //continue;
            
            if($k%5000==0){
                $user = $meli->refreshAccessToken($filial->refresh_token_meli);
                $response = ArrayHelper::getValue($user, 'body');
                $meliAccessToken = $response->access_token;
                
                $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
                $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
                $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;
                
                echo "\n\nTOKEN PRINCIPAL:" . $meliAccessToken;
                echo "\nTOKEN DUPLICADA:" . $meliAccessToken_conta_duplicada;
            }
            
            //$body = ["category_id" => "MLB191833"];
            $body = ["shipping" => [
                //"mode" => "me1",
                //"local_pick_up" => true,
                "free_shipping" => false,
                //"free_methods" => [],
                ]
            ];
            
            //$response_item = $meli->get("/items/".$produto_filial->meli_id."/?access_token=" . $meliAccessToken);
            //print_r($response_item); die;
            
            $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
            if($response["httpCode"] >= 300){
                echo " - Erro (Principal)";
                print_r($response);
            }
            else{
                echo " - OK (Principal)";
                echo "\n";print_r($response["body"]->permalink); echo "\n";
            }
            
            if(!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> ""){
                $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, []);
                if($response["httpCode"] >= 300){
                    echo " - Erro (Principal - Sem Juros)";
                    print_r($response);
                }
                else{
                    echo " - OK (Principal - Sem Juros)";
                    echo "\n";print_r($response["body"]->permalink); echo "\n";
                }
            }
            
            //Atualizar produto FULL, conta principal
            if(!is_null($produto_filial->meli_id_full)  && $produto_filial->meli_id_full <> ""){
                $response = $meli->put("items/{$produto_filial->meli_id_full}?access_token=" . $meliAccessToken, $body, []);
                if($response["httpCode"] >= 300){
                    echo " - Erro (Principal - Full)";
                    print_r($response);
                }
                else{
                    echo " - OK (Principal - Full)";
                    echo "\n";print_r($response["body"]->permalink); echo "\n";
                }
            }
            
            $produto_filial_conta_duplicada = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id', $produto_filial->id])->one();
            
            if($produto_filial_conta_duplicada){
                $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                if($response["httpCode"] >= 300){
                    echo " - Erro (Conta Duplicada)";
                    print_r($response);
                }
                else{
                    echo " - OK (Conta Duplicada)";
                    echo "\n";print_r($response["body"]->permalink); echo "\n";
                }
                                
                if(!is_null($produto_filial_conta_duplicada->meli_id_sem_juros) && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                    $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_sem_juros}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                    if($response["httpCode"] >= 300){
                        echo " - Erro (Conta Duplicada - Sem Juros)";
                        print_r($response);
                    }
                    else{
                        echo " - OK (Conta Duplicada - Sem Juros)";
                        echo "\n";print_r($response["body"]->permalink); echo "\n";
                    }
                }
                
                //Atualizar produto FULL, conta principal
                if(!is_null($produto_filial_conta_duplicada->meli_id_full)  && $produto_filial_conta_duplicada->meli_id_full <> ""){
                    $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_full}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                    if($response["httpCode"] >= 300){
                        echo " - Erro (Conta Duplicada - Full)";
                        print_r($response);
                    }
                    else{
                        echo " - OK (Conta Duplicada - Full)";
                        echo "\n";print_r($response["body"]->permalink); echo "\n";
                    }
                }
            }
        }
    }
}
