<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class ProductAdsAction extends Action
{
    public function run()
    {
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['id' => 60])
            ->all();

        foreach ($filials as $filial) {
            
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                
                $meliAccessToken = $response->access_token;
                //echo " ( ".$meliAccessToken." ) "; die;
                
                $body = [/*"available_quantity" => $produtoFilial->quantidade,*/];
                $response = $meli->get("/advertising/product_ads_2/campaigns/search?user_id=193724256&access_token=" . $meliAccessToken, $body, []);
                print_r($response);
                
                die;
                
                $produtoFilials = ProdutoFilial::find()//$filial->getProdutoFilials()
                    ->andWhere(['=','filial_id',$filial->id])
                    ->select(['filial_id', 'produto_id'])
                    ->where(" filial_id||'-'||produto_id in (select filial_id||'-'||produto_id from (select produto_filial.filial_id, produto_id, count(*) as quantidade from produto_filial where filial_id <> 43 group by produto_id, filial_id) as foo where foo.quantidade >=2) ")
                    ->groupBy(['filial_id', 'produto_id'])
                    //->andWhere(['is not', 'meli_id', null])
                    //->andWhere(['>', 'quantidade', 0])
		            //->andWhere(['=','meli_id', $global_id])
                    //->andWhere(['=','filial_id', 86])
                    ->all();
        
                //print_r($produtoFilials);
        
                foreach ($produtoFilials as $k => $produtoFilial) {

                    echo "\n".$k." - ".$produtoFilial->produto->nome;
                    
                    $produtos_filial_analise = ProdutoFilial::find()->andWhere(['=','produto_id',$produtoFilial->produto_id])
                                                                    ->andWhere(['=','filial_id',$produtoFilial->filial_id])
                                                                    ->all();
                    
                    $estoque = array();
                    foreach($produtos_filial_analise as $i => $produto_filial_analise){
                        echo "\n  ".$i." - ".$produto_filial_analise->id;
                        $estoque[$i] = $produto_filial_analise;
                    }
                    
                    
                    echo "((("; print_r($estoque[0]->id); echo ")))";
                    
                    /*//Update Item
                    $body = [
                        "available_quantity" => $produtoFilial->quantidade,
                    ];
                    $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                    
                    Yii::info($response, 'mercado_livre_update');
                    if ($response['httpCode'] >= 300) {
                        echo " - Erro";
                    }*/
                }
            }
        }
    }
}
