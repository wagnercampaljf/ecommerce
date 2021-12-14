<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 27/06/2016
 * Time: 18:52
 */

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class RelistAction extends Action
{
    public function run($iid=NULL)
    {
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()->andWhere(['IS NOT', 'refresh_token_meli', null])->all();

        /* @var $filial Filial */
        foreach ($filials as $filial) {
            echo "\t{$filial->nome}\n\n";
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                if($iid){
			echo "id: ".$id;
		}else{
			$produtoFilials = $filial->getProdutoFilials()->andWhere([
                    	'is not',
                    	'meli_id',
                    	null
                	])->andWhere([
                    	'>',
                    	'quantidade',
                    	0
                ])->all();
		}
                /* @var $produtoFilial ProdutoFilial */
                foreach ($produtoFilials as $produtoFilial) {
                    if (is_null($produtoFilial->valorMaisRecente)) {
                        Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não possui valor",
                            'error_yii');
                        continue;
                    }

                    $body = [
                        'price' => $produtoFilial->valor,
                        'quantity' => $produtoFilial->quantidade,
                        "listing_type_id" => "bronze",
                    ];

                    $response = $meli->post(
                        "items/{$produtoFilial->meli_id}/relist?access_token=" . $meliAccessToken,
                        $body
                    );

                    Yii::info($response, 'mercado_livre_relist');
                    if ($response['httpCode'] >= 300) {
                        Yii::error($response['body'], 'mercado_livre_relist');
                    } else {
                        $produtoFilial->meli_id = $response['body']->id;
                        if (!$produtoFilial->save()) {
                            Yii::error($produtoFilial->getErrors(), 'error_yii');
                        }
                    }
                }
            }
        }
    }
}
