<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class DeleteContaDuplicadaAction extends Action
{
    public function run()
    {
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()->andWhere(['IS NOT', 'refresh_token_meli', null])
				 ->andWhere(['=', 'id', 74])
				 ->all();


        foreach ($filials as $filial) {

	    $filialCopia = Filial::find()->andWhere(['=','id', 43])->one();
            $user = $meli->refreshAccessToken($filialCopia->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                $produtoFilials = $filial->getProdutoFilials()->andWhere([ 'is not', 'meli_id', null ])->all();
                /* @var $produtoFilial ProdutoFilial */
                foreach ($produtoFilials as $k => $produtoFilial) {

		    echo $k." - ".$produtoFilial->id;

                    $body = ["status" => 'closed', ];

		    $produto_filial_copia = ProdutoFilial::find()->andWhere(['=', 'produto_filial_origem_id', $produtoFilial->id])->one();

                    $response = $meli->put("items/{$produto_filial_copia->meli_id}?access_token=" . $meliAccessToken,$body, [] );

                    Yii::info($response, 'mercado_livre_closed');
                    if ($response['httpCode'] >= 300) {
                        echo " - Error_closed";
                        Yii::error($response['body'], 'mercado_livre_closed');
                    } else{
			echo " -  OK";
		    }

                    $body = ["status" => 'deleted',];

                    $response = $meli->put("items/{$produto_filial_copia->meli_id}?access_token=" . $meliAccessToken, $body, [] );

                    Yii::info($response, 'mercado_livre_deleted');
                    if ($response['httpCode'] >= 300) {
                        echo " - Error_deleted";
                        Yii::error($response['body'], 'mercado_livre_deleted');
                    }else {
			echo " - OK";
		    }

                    $produto_filial_copia->meli_id = null;
                    if (!$produto_filial_copia->save()) {
                        Yii::error($produto_filial_copia->errors, 'error_yii');
                    }
		    echo "\n";
                }
            }
        }
    }
}


