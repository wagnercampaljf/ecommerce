<?php

namespace console\controllers\actions\mercadolivre;

use Livepixel\MercadoLivre\Meli;
use common\models\Filial;
use common\models\ProdutoFilial;
use yii\helpers\ArrayHelper;

class PuxarFichasTecnicasAction extends Action
{
    
    public function run($cliente = 1){
        
        echo "INÍCIO\n\n";
        
        $filial     = Filial::find()->andWhere(["=", "id", 94])->one();
        $meli       = new Meli(static::APP_ID, static::SECRET_KEY);
        $user       = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response   = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $filial_principal           = Filial::find()->andWhere(["=", "id", 72])->one();
            $user_principal             = $meli->refreshAccessToken($filial_principal->refresh_token_meli);
            $response_principal         = ArrayHelper::getValue($user_principal, 'body');
            $meliAccessToken_principal  = $response_principal->access_token;
            
            $produtos_filiais_mg = ProdutoFilial::find()->andWhere(["=", "filial_id", $filial->id])
                                                        ->andWhere(["is not", "meli_id", null])
                                                        ->all();
            
            foreach($produtos_filiais_mg as $k => $produto_filial_mg){
                
                echo "\n".$k." - ".$produto_filial_mg->id;
                
                $produto_filial_principal = ProdutoFilial::find()   ->andWhere(["=", "produto_id", $produto_filial_mg->produto_id])
                                                                    ->andWhere(["<>", "filial_id", 98])
                                                                    ->andWhere(["<>", "filial_id", $filial->id])
                                                                    ->one();
                                                                                    
                if($produto_filial_principal){
                    $response_item = $meli->get("/items/".$produto_filial_principal->meli_id."?access_token=" . $meliAccessToken);
                    //print_r($response_item["body"]->attributes); die;
                
                    $body = array();
                    
                    if(!empty($response_item["body"]->attributes)){
                        foreach($response_item["body"]->attributes as $atributo){
                            //print_r($atributo);
                            $body['attributes'][] = [
                                'id'                    => $atributo->id,
                                'value_name'            => $atributo->value_name,
                            ];
                            
                        }
                        
                        //print_r($body);
                        
                        $response = $meli->put("items/{$produto_filial_mg->meli_id}?access_token=" . $meliAccessToken, $body, []);
                        if ($response['httpCode'] >= 300) {
                            //print_r($response);
                            echo " - ERRO Ficha Técnica";
                        } else {
                            echo " - OK Ficha Técnica";
                        }
                    }

                    //die;
                }
            }
        }
        
        echo "\n\nFIM!\n\n";
    }
}