<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 30/06/2016
 * Time: 10:39
 */

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class DeleteAction extends Action
{
    public function run()
    {
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()->andWhere(['IS NOT', 'refresh_token_meli', null])->all();

        foreach ($filials as $filial) {
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;

                $produtoFilials = $filial->getProdutoFilials()	->andWhere(['is not', 'meli_id', null])
								->andWhere(['=', 'id', 112344])
								->all();
                /* @var $produtoFilial ProdutoFilial */
                foreach ($produtoFilials as $produtoFilial) {
                    $body = [
                        "status" => 'closed',
                    ];

                    $response = $meli->put(
                        "items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,
                        $body,
                        []
                    );

                    Yii::info($response, 'mercado_livre_closed');
                    if ($response['httpCode'] >= 300) {
                        echo "Error_closed";
                        Yii::error($response['body'], 'mercado_livre_closed');
                    }

                    $body = [
                        "status" => 'deleted',
                    ];

                    $response = $meli->put(
                        "items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,
                        $body,
                        []
                    );

                    Yii::info($response, 'mercado_livre_deleted');
                    if ($response['httpCode'] >= 300) {
                        echo "Error_deleted";
                        Yii::error($response['body'], 'mercado_livre_deleted');
                    }
		    else{
			echo "Deleted";
		    }

                    $produtoFilial->meli_id = null;
                    if (!$produtoFilial->save()) {
                        Yii::error($produtoFilial->errors, 'error_yii');
                    }

                }
            }
        }
    }
}
