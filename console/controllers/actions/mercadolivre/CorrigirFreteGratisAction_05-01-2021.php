<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\ValorProdutoFilial;

class CorrigirFreteGratisAction extends Action
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
                                                //->andWhere(['=', 'meli_id', 'MLB1514761390'])
                                                ->andWhere(['<>', 'filial_id', 98])
                                                ->orderBy(['filial_id' => SORT_ASC, 'id'=>SORT_ASC])
                                                ->all();
        
        foreach ($produtos_filial as $k => $produto_filial){
            //print_r($produto_filial);
            
            echo "\n".$k." - ".$produto_filial->filial_id." - ".$produto_filial->id;
            
            $valor_produto_filial = ValorProdutoFilial::find()  ->andWhere(['=','produto_filial_id', $produto_filial->id])
                                                                ->orderBy(['dt_inicio' => SORT_DESC])
                                                                ->one();
            
            $preco = round($valor_produto_filial->valor, 2);
            echo " - ".$preco;
            if($preco > 120){
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
            
            $body = ["category_id" => "MLB191833"];
            
            $response_item = $meli->get("/items/".$produto_filial->meli_id."/?access_token=" . $meliAccessToken);
            //print_r($response_item); die;
            
            if($response_item["httpCode"] >= 300){
                echo " - Produto não encontrado no ML";
            }
            else{
                if(isset($response_item["body"]->shipping)){
                    echo " - ";print_r($response_item["body"]->shipping->free_shipping);
                    echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                    
                    if($response_item["body"]->shipping->free_shipping == 1){
                        echo " - FREE";
                        $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                    }
                    else{
                        echo " - NOT FREE";
                    }
                }
            }
            
            if(!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> ""){
                $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial->meli_id_sem_juros.'?access_token='.$meliAccessToken);
                if($response_item["httpCode"] >= 300){
                    echo " - Produto não encontrado no ML(Sem Juros)";
                }
                else{
                    if(isset($response_item["body"]->shipping)){
                        echo " - ";print_r($response_item["body"]->shipping->mode);
                        echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                        
                        if($response_item["body"]->shipping->free_shipping == 1){
                            echo " - FREE";
                            $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, []);
                        }
                        else{
                            echo " - NOT FREE";
                        }
                    }
                }
            }
            
            //Atualizar produto FULL, conta principal
            if(!is_null($produto_filial->meli_id_full)  && $produto_filial->meli_id_full <> ""){
                $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial->meli_id_full.'?access_token='.$meliAccessToken);
                if ($response_tipo_anuncio['httpCode'] >= 300) {
                    echo " - produto nao encontrado no ML, tipo de anuncio(Full)";
                }
                else {
                    if(isset($response_item["body"]->shipping)){
                        echo " - ";print_r($response_item["body"]->shipping->mode);
                        echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                        
                        if($response_item["body"]->shipping->free_shipping == 1){
                            echo " - FREE";
                            $response = $meli->put("items/{$produto_filial->meli_id_full}?access_token=" . $meliAccessToken, $body, []);
                        }
                        else{
                            echo " - NOT FREE";
                        }
                    }
                }
            }
            
            $produto_filial_conta_duplicada = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id', $produto_filial->id])->one();
            
            if($produto_filial_conta_duplicada){
                
                $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id.'?access_token='.$meliAccessToken_conta_duplicada);
                if ($response_tipo_anuncio['httpCode'] >= 300) {
                    echo " - produto nao encontrado no ML(CD)";
                }
                else {
                    if(isset($response_item["body"]->shipping)){
                        echo " - ";print_r($response_item["body"]->shipping->mode);
                        echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                        
                        if($response_item["body"]->shipping->free_shipping == 1){
                            echo " - FREE";
                            $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                        }
                        else{
                            echo " - NOT FREE";
                        }
                    }
                }
                
                if(!is_null($produto_filial_conta_duplicada->meli_id_sem_juros) && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                    $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id_sem_juros.'?access_token='.$meliAccessToken_conta_duplicada);
                    if($response_item["httpCode"] >= 300){
                        echo " - Produto não encontrado no ML(CD)(Sem Juros)";
                    }
                    else{
                        if(isset($response_item["body"]->shipping)){
                            echo " - ";print_r($response_item["body"]->shipping->mode);
                            echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                            
                            if($response_item["body"]->shipping->free_shipping == 1){
                                echo " - FREE";
                                $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_sem_juros}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                            }
                            else{
                                echo " - NOT FREE";
                            }
                        }
                    }
                }
                
                //Atualizar produto FULL, conta principal
                if(!is_null($produto_filial_conta_duplicada->meli_id_full)  && $produto_filial_conta_duplicada->meli_id_full <> ""){
                    $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id_full.'?access_token='.$meliAccessToken_conta_duplicada);
                    if ($response_tipo_anuncio['httpCode'] >= 300) {
                        echo " - produto nao encontrado no ML, tipo de anuncio(CD)(Full)";
                    }
                    else {
                        if(isset($response_item["body"]->shipping)){
                            echo " - ";print_r($response_item["body"]->shipping->mode);
                            echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                            
                            if($response_item["body"]->shipping->free_shipping == 1){
                                echo " - FREE";
                                $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_full}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                            }
                            else{
                                echo " - NOT FREE";
                            }
                        }
                    }
                }
            }
            
            /*$response_item = $meli->get("/items/".$produto_filial->meli_id."/?access_token=" . $meliAccessToken);
            //print_r($response_item);
            //die;
            echo " - ".$produto_filial->meli_id;
            
            $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
            if($response_item["httpCode"] >= 300){
                echo " - Erro (Principal)";
            }
            else{
                print_r($response);
                echo " - OK (Principal)";
                echo "\n";print_r($response_item["body"]->permalink); echo "\n";
            }
            
            if(!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> ""){
                $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, []);
                if($response_item["httpCode"] >= 300){
                    echo " - Erro (Sem Juros)";
                }
                else{
                    echo " - OK (Sem juros)";
                    echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                }
            }
            
            //Atualizar produto FULL, conta principal
            if(!is_null($produto_filial->meli_id_full)  && $produto_filial->meli_id_full <> ""){
                $response = $meli->put("items/{$produto_filial->meli_id_full}?access_token=" . $meliAccessToken, $body, []);
                if($response_item["httpCode"] >= 300){
                    echo " - Erro (Full)";
                }
                else{
                    echo " - OK (Full)";
                    echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                }
            }
        
            $produto_filial_conta_duplicada = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id', $produto_filial->id])->one();
            
            if($produto_filial_conta_duplicada){
                $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                if($response_item["httpCode"] >= 300){
                    echo " - Erro (Principal - CD)";
                }
                else{
                    echo " - OK (Principal - CD)";
                    echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                }

                if(!is_null($produto_filial_conta_duplicada->meli_id_sem_juros) && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                    $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_sem_juros}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                    if($response_item["httpCode"] >= 300){
                        echo " - Erro (Sem juros - CD)";
                    }
                    else{
                        echo " - OK (Sem Juros - CD)";
                        echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                    }
                }
                
                //Atualizar produto FULL, conta principal
                if(!is_null($produto_filial_conta_duplicada->meli_id_full)  && $produto_filial_conta_duplicada->meli_id_full <> ""){
                    $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_full}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                    if($response_item["httpCode"] >= 300){
                        echo " - Erro (Full - CD)";
                    }
                    else{
                        echo " - OK (Full - CD)";
                        echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                    }
                }
            }*/
        }
    }
}
