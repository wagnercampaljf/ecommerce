<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\Produto;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class LimparProdutoDuplicadoVannucciAction extends Action
{
    public function run()
    {
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['id' => 96])
            ->all();

        foreach ($filials as $filial) {
            
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                
                $meliAccessToken = $response->access_token;
                echo " ( ".$meliAccessToken." ) "; //die;
                $produtoFilials = ProdutoFilial::find()->andWhere(['=','filial_id',$filial->id])->all();
                    //->joinWith('produto')
                    //->andWhere(['like','upper(produto.nome)','PESTANA'])
                    //->andWhere(['is not', 'meli_id', null])
                    //->andWhere(['>', 'quantidade', 0])
		            //->andWhere(['=','meli_id', $global_id])
                    
                foreach ($produtoFilials as $k => $produtoFilial) {

                    echo "\n".$k." - ".$produtoFilial->produto->codigo_fabricante." - ".$produtoFilial->produto->nome;
                    
                    $produto_filial_duplicado = ProdutoFilial::find()   ->joinWith('produto')
                                                                        ->andWhere(['<>','produto_id',$produtoFilial->produto_id])
                                                                        ->andWhere(['like','produto.codigo_fabricante',$produtoFilial->produto->codigo_fabricante])
                                                                        ->one();
                    
                    if($produto_filial_duplicado){
                        print_r($produto_filial_duplicado);
                        die;
                    }
                    /*$estoque = array();
                    foreach($produtos_filial_analise as $i => $produto_filial_analise){
                        echo "\n  ".$i." - ".$produto_filial_analise->id;
                        $estoque[$i] = $produto_filial_analise;
                    }
                    
                    
                    echo "((("; print_r($estoque[0]->id); echo ")))";*/
                    
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
