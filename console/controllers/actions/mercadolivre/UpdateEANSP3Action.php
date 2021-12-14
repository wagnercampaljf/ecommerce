<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateEANSP3Action extends Action
{
    public function run()
    {
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        
        echo "Começo da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;
        
        $filials = Filial::find()
        ->andWhere(['IS NOT', 'refresh_token_meli', null])
        //->andWhere(['id' => [84]])
        ->andWhere(['id' => [98]])
        //->andWhere(['<>','id', 98])
        ->orderBy('id')
        ->all();
        
        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";
            
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                
                $meliAccessToken = $response->access_token;
                $produtoFilials = $filial   ->getProdutoFilials()
                ->orWhere(['is not','meli_id',null])
                ->orWhere(['is not','meli_id_sem_juros',null])
                ->orWhere(['is not','meli_id_full',null])
                ->orWhere(['is not','meli_id_flex',null])
                //->andWhere(['=','meli_id','MLB2020062363'])
                //->andWhere(['produto_filial.meli_id_sem_juros' => ['MLB1450584964']])
                //->andWhere(['produto_filial.id' => [34637]])
                ->orderBy('produto_filial.id')
                ->all();
                
                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n".$k ." - ". $produtoFilial->filial_id." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id;
                    
                    if($k < 51607){
                        continue;
                    }
                    
                    if(($k%5000) == 0){
                        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
                        $response = ArrayHelper::getValue($user, 'body');
                        $meliAccessToken = $response->access_token;
                    }
                    
                    $body = [
                        //'sku'           => 'PA'.$produtoFilial->produto->id,
                        'attributes' 	=>[
                            [
                                'id'                    => 'EAN',
                                'name'                  => 'EAN',
                                'value_id'              => null,
                                'value_name'            => null,
                            ],
                            [
                                'id'                    => 'GTIN',
                                'name'                  => 'GTIN',
                                'value_id'              => null,
                                'value_name'            => null,
                            ],
                        ]
                    ];
                    
                    $meli_ids = [];
                    
                    if(!is_null($produtoFilial->meli_id)){
                        $meli_ids[] = $produtoFilial->meli_id;
                    }
                    if(!is_null($produtoFilial->meli_id_sem_juros)){
                        $meli_ids[] = $produtoFilial->meli_id_sem_juros;
                    }
                    if(!is_null($produtoFilial->meli_id_full)){
                        $meli_ids[] = $produtoFilial->meli_id_full;
                    }
                    if(!is_null($produtoFilial->meli_id_flex)){
                        $meli_ids[] = $produtoFilial->meli_id_flex;
                    }
                    
                    foreach($meli_ids as $k => $meli_id){
                        $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                        while ($response['httpCode'] == 429) {
                            echo " - ERRO";
                            $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                        }
                        if ($response['httpCode'] >= 300) {
                            echo " - ERROR EAN";
                            
                        }
                        else {
                            echo " - OK EAN";
                            echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                        }
                    }
                }
            }
            
            echo "Fim da filial: " . $filial->nome . "\n";
        }
        
        echo "Fim da rotina de atualização dos produtos no ML";
    }
}

