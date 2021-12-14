<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class AdicionarDiasExpedicaoMGAction extends Action
{
    public function run($filial_id)
    {
        
        $filial = Filial::find()->andWhere(['=','id',$filial_id])->one();
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;
        
        $filial_conta_duplicada = Filial::find()->andWhere(['=','id',100])->one();
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
        $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;
        
        $produtos_filial = ProdutoFilial::find()//->joinWith('produto')
                                                //->andWhere(['like', 'nome', 'PELUCIA'])
                                                //->andWhere(['filial_id' => [60, 8, 84, 62, 86, 59, 69, 77, 78, 76]])
                                                //->andWhere(['=', 'meli_id', 'MLB1323864494'])//'MLB1433833683'])//'MLB1063732490'])
                                                ->andWhere(['=', 'filial_id', $filial_id])
                                                //->andWhere(['<>', 'filial_id', 98])
                                                ->orderBy('id')
                                                ->all();
        
        foreach ($produtos_filial as $k => $produto_filial){
            //print_r($produto_filial);
            
            echo "\n".$k." - ".$produto_filial->id." - ".$produto_filial->filial_id;//." - ".$produto_filial->produto->nome; 
            
            if($k < 0){
                continue;
            }
            
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
            
            $dias = 0;
            switch ($produto_filial->filial_id){
                case 60:
                    $dias = 1;
                    break;
                case 8:
                    $dias = 2;
                    break;
                case 59:
                    $dias = 2;
                    break;
                case 62:
                    $dias = 2;
                    break;
                case 84:
                    $dias = 2;
                    break;
                case 86:
                    $dias = 2;
                    break;
                case 69:
                    $dias = 3;
                    break;
                case 76:
                    $dias = 3;
                    break;
                case 77:
                    $dias = 3;
                    break;
                case 78:
                    $dias = 3;
                    break;
            }
            
            //$dias = 5;
            
            $body = [
                    //"category_id" => utf8_encode($subcategoriaMeli),
                    "sale_terms" => [[
                                    "id"            => "MANUFACTURING_TIME",
                                    "name"          => "Disponibilidade de estoque",
                                    "value_id"      => null,
                                    "value_name"    => $dias." dias",
                                    "value_struct"  =>  [[
                                                        "number"    => $dias,
                                                        "unit"      => "dias"
                                                        ]],
                                    "values"    =>  [[
                                                    "id"        => null,
                                                    "name"      => $dias." dias",
                                                    "struct"    =>  [
                                                                    "number"    => $dias,
                                                                    "unit"      => "dias"
                                                                    ]
                                                    ]]
                                    ]]
                    ];
            
            /*$response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
            if($response["httpCode"] >= 300){
                echo "\nError (Principal)";
                print_r($response);
            }
            else{
                echo "\nOK (Principal)";
                echo " - "; print_r($response["body"]->status);
                echo "\n"; print_r($response["body"]->permalink);
            }
            
            if(!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> ""){
                $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, []);
                if($response["httpCode"] >= 300){
                    echo "\nError (Sem Juros)";
                }
                else{
                    echo "\nOK (Sem juros)";
                    echo " - "; print_r($response["body"]->status);
                    echo "\n"; print_r($response["body"]->permalink);
                }
            }
        
            //Atualizar produto FULL, conta principal
            if(!is_null($produto_filial->meli_id_full)  && $produto_filial->meli_id_full <> ""){
                $response = $meli->put("items/{$produto_filial->meli_id_full}?access_token=" . $meliAccessToken, $body, []);
                if($response["httpCode"] >= 300){
                    echo "\nError (Full)";
                }
                else{
                    echo "\nOK (Full)";
                    echo " - "; print_r($response["body"]->status); 
                    echo "\n"; print_r($response["body"]->permalink);
                }
            }*/
        
            $produto_filial_conta_duplicada = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id', $produto_filial->id])->one();
            
            if($produto_filial_conta_duplicada){
                $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                
                while ($response['httpCode'] == 429) {
                    echo " - ERRO";
                    $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                }
                
                if($response["httpCode"] >= 300){
                    echo "\nError (Principal - CD)";
                }
                else{
                    echo "\nOK (Principal - CD)";
                    echo " - "; print_r($response["body"]->status);
                    echo "\n"; print_r($response["body"]->permalink);
                }
                
                if(!is_null($produto_filial_conta_duplicada->meli_id_sem_juros) && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                    $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_sem_juros}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                    
                    while ($response['httpCode'] == 429) {
                        echo " - ERRO";
                        $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_sem_juros}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                    }
                    
                    if($response["httpCode"] >= 300){
                        echo "\nError (Sem Juros - CD)";
                    }
                    else{
                        echo "\nOK (Sem Juros - CD)";
                        echo " - "; print_r($response["body"]->status);
                        echo "\n"; print_r($response["body"]->permalink);
                    }
                }
                
                //Atualizar produto FULL, conta principal
                if(!is_null($produto_filial_conta_duplicada->meli_id_full)  && $produto_filial_conta_duplicada->meli_id_full <> ""){
                    $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_full}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                    
                    while ($response['httpCode'] == 429) {
                        echo " - ERRO";
                        $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_full}?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                    }
                    
                    if($response["httpCode"] >= 300){
                        echo "\nError (Full - CD)";
                    }
                    else{
                        echo "\nOK (Full - CD)";
                        echo " - "; print_r($response["body"]->status);
                        echo "\n"; print_r($response["body"]->permalink);
                    }
                }
            }
            
            echo "\n";
            
            //die;
        }
    }
}
