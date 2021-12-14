<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class LimparEstoqueDuplicadoAction extends Action
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
                                                //->andWhere(['=', 'id', 72536])
                                                ->andWhere(['<>', 'filial_id', 98])
                                                ->orderBy(['filial_id' => SORT_ASC, 'id'=>SORT_ASC])
                                                ->all();
        
        foreach ($produtos_filial as $k => $produto_filial){
            //print_r($produto_filial);
            
            echo "\n".$k." - ".$produto_filial->filial_id." - ".$produto_filial->id;
            
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
            $body = [
                'attributes' =>[
                        [
                             'id'			=> 'UNITS_PER_PACKAGE',
                             'name'			=> null,
                             'value_id'		=> null,
                        ]
                    ]
                ];
            
            $response_item = $meli->get("/items/".$produto_filial->meli_id."/?access_token=" . $meliAccessToken);
            //print_r($response_item); die;
            
            if($response_item["httpCode"] >= 300){
                echo " - Produto não encontrado no ML";
            }
            else{
                if(isset($response_item["body"]->shipping)){
                    echo "\n";print_r($response_item["body"]->permalink); echo "\n";

                    foreach(ArrayHelper::getValue($response_item, 'body.attributes') as $atributo){
                        echo ArrayHelper::getValue($atributo, 'id')." - ";
                        if(ArrayHelper::getValue($atributo, 'id')== "UNITS_PER_PACKAGE"){
                            $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                            if($response["httpCode"] >= 300){
                                echo " - Erro (Principal)";
                            }
                            else{
                                echo " - OK (Principal)";
                            }
                        }
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
                        echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                        
                        foreach(ArrayHelper::getValue($response_item, 'body.attributes') as $atributo){
                            echo ArrayHelper::getValue($atributo, 'id')." - ";
                            if(ArrayHelper::getValue($atributo, 'id')== "UNITS_PER_PACKAGE"){
                                $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, []);
                                if($response["httpCode"] >= 300){
                                    echo " - Erro (Sem Juros)";
                                }
                                else{
                                    echo " - OK (Sem juros)";
                                }
                            }
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
                        echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                        
                        foreach(ArrayHelper::getValue($response_item, 'body.attributes') as $atributo){
                            echo ArrayHelper::getValue($atributo, 'id')." - ";
                            if(ArrayHelper::getValue($atributo, 'id')== "UNITS_PER_PACKAGE"){
                                $response = $meli->put("items/{$produto_filial->meli_id_full}?access_token=" . $meliAccessToken, $body, []);
                                if($response["httpCode"] >= 300){
                                    echo " - Erro (Full)";
                                }
                                else{
                                    echo " - OK (Full)";
                                }
                            }
                        }
                    }
                }
            }
            
            $produto_filial_conta_duplicada = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id', $produto_filial->id])->one();
            
            if($produto_filial_conta_duplicada){

                $response_item = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id.'?access_token='.$meliAccessToken_conta_duplicada);
                if ($response_item['httpCode'] >= 300) {
                    echo " - produto nao encontrado no ML(CD)";
                }
                else {
                    if(isset($response_item["body"]->shipping)){
                        echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                        
                        foreach(ArrayHelper::getValue($response_item, 'body.attributes') as $atributo){
                            echo ArrayHelper::getValue($atributo, 'id')." - ";
                            if(ArrayHelper::getValue($atributo, 'id')== "UNITS_PER_PACKAGE"){
                                $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                                if($response["httpCode"] >= 300){
                                    echo " - Erro (Principal - CD)";
                                }
                                else{
                                    echo " - OK (Principal - CD)";
                                }
                            }
                        }
                    }
                }
                
                if(!is_null($produto_filial_conta_duplicada->meli_id_sem_juros) && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                    $response_item = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id_sem_juros.'?access_token='.$meliAccessToken_conta_duplicada);
                    if($response_item["httpCode"] >= 300){
                        echo " - Produto não encontrado no ML(CD)(Sem Juros)";
                    }
                    else{
                        if(isset($response_item["body"]->shipping)){
                            echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                            
                            foreach(ArrayHelper::getValue($response_item, 'body.attributes') as $atributo){
                                echo ArrayHelper::getValue($atributo, 'id')." - ";
                                if(ArrayHelper::getValue($atributo, 'id')== "UNITS_PER_PACKAGE"){
                                    $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_sem_juros}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                                    if($response["httpCode"] >= 300){
                                        echo " - Erro (Sem Juros - CD)";
                                    }
                                    else{
                                        echo " - OK (Sem Juros - CD)";
                                    }
                                }
                            }
                        }
                    }
                }
                
                //Atualizar produto FULL, conta principal
                if(!is_null($produto_filial_conta_duplicada->meli_id_full)  && $produto_filial_conta_duplicada->meli_id_full <> ""){
                    $response_item = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id_full.'?access_token='.$meliAccessToken_conta_duplicada);
                    if ($response_item['httpCode'] >= 300) {
                        echo " - produto nao encontrado no ML, tipo de anuncio(CD)(Full)";
                    }
                    else {
                        if(isset($response_item["body"]->shipping)){
                            echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                            
                            foreach(ArrayHelper::getValue($response_item, 'body.attributes') as $atributo){
                                echo ArrayHelper::getValue($atributo, 'id')." - ";
                                if(ArrayHelper::getValue($atributo, 'id')== "UNITS_PER_PACKAGE"){
                                    $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_full}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                                    if($response["httpCode"] >= 300){
                                        echo " - Erro (Full - CD)";
                                    }
                                    else{
                                        echo " - OK (Full - CD)";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
