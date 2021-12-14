<?php 

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class AdicionarDiasExpedicaoMG4Action extends Action
{

    public function run()
    {

        $filial_conta_duplicada = Filial::find()->andWhere(['=','id',100])->one();
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
        $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;

        $produtos_filial = ProdutoFilial::find()  ->andWhere(['filial_id' => [38, 43, 60, 72]])                                               
                                                  ->orderBy('id')
                                                  ->all();

        foreach ($produtos_filial as $k => $produto_filial){

            echo "\n".$k." - ".$produto_filial->id." - ".$produto_filial->filial_id;
            
            if($k < 0){
                continue;
            }
            
            if($k%5000==0){
                $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
                $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
                $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;
                
               // echo "\n\nTOKEN PRINCIPAL:" . $meliAccessToken;
                echo "\nTOKEN DUPLICADA:" . $meliAccessToken_conta_duplicada;
            }

            $dias = 1;

            $body = [
                
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
            
                $produto_filial_conta_duplicada = ProdutoFilial::find() ->andWhere(['=','produto_filial_origem_id', $produto_filial->id])
                                                                        ->andWhere(['=','filial_id', 100])
                                                                        ->andWhere(['is not','meli_id', null])
                                                                        ->one();
            
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
                }                               
            }
    }

}
?>
