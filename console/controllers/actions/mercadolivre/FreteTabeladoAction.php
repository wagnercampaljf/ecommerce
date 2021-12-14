<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class FreteTabeladoAction extends Action
{
    public function run($filial_id = 72)
    {
        
        $filial = Filial::find()->andWhere(['=','id',$filial_id])->one();
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;
        
        $filial_conta_duplicada = Filial::find()->andWhere(['=','id',98])->one();
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
        $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;

        $filiais = Filial::find()   ->andWhere(['is not', 'refresh_token_meli', null])
                                    ->andWhere(['=','id',97])
                                    ->andWhere(['<>', 'id', 98])
                                    ->orderBy('id')
                                    ->all();

        foreach($filiais as $j => $filial_corrente){
            
            echo "\n\nFilial: ".$filial_corrente->nome."\n\n";

            $produtos_filial = ProdutoFilial::find()->andWhere(['=', 'filial_id', $filial_corrente->id])
                                                    //->andWhere(['=', 'meli_id', 'MLB1141699378'])
                                                    ->orderBy('id')
                                                    ->all();
            
            foreach ($produtos_filial as $k => $produto_filial){
                
                echo "\n".$k." - ".$produto_filial->id;
                
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
                
                if(($k <= 25000 && $produto_filial->filial_id == 97) || $produto_filial->filial_id == 38 || ($k <= 5500 && $produto_filial->filial_id == 43)){
                    continue;
                }
                
                /*$body = [
                    "shipping" => [
                        "mode"          => "custom",
                        "local_pick_up" => false,
                        "free_shipping" => false,
                        "methods"       => [],
                        "costs"         => [
                            [
                                "description"   => "Estado São Paulo e Rio de Janeiro",
                                "cost"          => "75"
                            ],
                            [
                                "description"   => "Estados: ES, MG",
                                "cost"          => "90"
                            ],
                            [
                                "description"   => "Estados: PR, RS, SC - Região SUL",
                                "cost"          => "100"
                            ],
                            [
                                "description"   => "Estados: DF, GO, MS, MT - Região Centro Oeste",
                                "cost"          => "109"
                            ],
                            [
                                "description"   => "Estados:  AL, BA, CE, MA, PB, PE, PI, RN, SE - Região Nordeste",
                                "cost"          => "125"
                            ],
                            [
                                "description"   => "Estados: AC, AM, AP, PA, RO, RR, TO - Região Norte",
                                "cost"          => "170"
                            ]
                        ]
                    ]
                ];*/
                
                $body = [
                    "shipping" => [
                        "mode"          => "custom",
                        "local_pick_up" => false,
                        "free_shipping" => false,
                        "methods"       => [],
                        "costs"         => [
                            [
                                "description"   => "SP, RJ, ES e MG capital",
                                "cost"          => "80"
                            ],
                            [
                                "description"   => "SP, RJ, ES e MG Interior",
                                "cost"          => "90"
                            ],
                            [
                                "description"   => "Região sul capital",
                                "cost"          => "100"
                            ],
                            [
                                "description"   => "Região sul interior",
                                "cost"          => "120"
                            ],
                            [
                                "description"   => "Região nordeste capital",
                                "cost"          => "120"
                            ],
                            [
                                "description"   => "Região nordeste interior",
                                "cost"          => "145"
                            ],
                            [
                                "description"   => "Região centro oeste capital",
                                "cost"          => "110"
                            ],
                            [
                                "description"   => "Região centro oeste interior",
                                "cost"          => "150"
                            ],
                            [
                                "description"   => "Região norte capital",
                                "cost"          => "160"
                            ],
                            [
                                "description"   => "Região norte interior",
                                "cost"          => "180"
                            ]
                        ]
                    ]
                ];
                
                $response_item = $meli->get("/items/".$produto_filial->meli_id."/?access_token=" . $meliAccessToken);
                //print_r($response_item);
                
                if($response_item["httpCode"] >= 300){
                    echo " - Produto não encontrado no ML";
                }
                else{
                    if(isset($response_item["body"]->shipping)){
                        echo " - ";print_r($response_item["body"]->shipping->mode);
                        echo " - ";print_r($response_item["body"]->permalink);
                        
                        if($response_item["body"]->shipping->mode != "me2"){
                            $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
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
                            echo " - ";print_r($response_item["body"]->permalink);
                            
                            if($response_item["body"]->shipping->mode != "me2"){
                                $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, []);
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
                            echo " - ";print_r($response_item["body"]->permalink);
                            
                            if($response_item["body"]->shipping->mode != "me2"){
                                $response = $meli->put("items/{$produto_filial->meli_id_full}?access_token=" . $meliAccessToken, $body, []);
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
                            echo " - ";print_r($response_item["body"]->permalink);
                            
                            if($response_item["body"]->shipping->mode != "me2"){
                                $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
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
                                echo " - ";print_r($response_item["body"]->permalink);
                                
                                if($response_item["body"]->shipping->mode != "me2"){
                                    $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_sem_juros}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
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
                                echo " - ";print_r($response_item["body"]->permalink);
                                
                                if($response_item["body"]->shipping->mode != "me2"){
                                    $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_full}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
