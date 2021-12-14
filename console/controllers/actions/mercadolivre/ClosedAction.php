<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 28/06/2016
 * Time: 12:48
 */

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class ClosedAction extends Action
{
    public function run()
    {
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
//            ->andWhere(['id' => '62'])
            ->all();

        foreach ($filials as $filial) {
            echo "\t{$filial->nome}\n\n";
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                $produtoFilials = $filial->getProdutoFilials()->andWhere([
                    'is not',
                    'meli_id',
                    null

                ])->andWhere([
                    'quantidade' => 0
                ])->all();
                /* @var $produtoFilial ProdutoFilial */
//                $cont = 0;
                foreach ($produtoFilials as $produtoFilial) {
//                    $cont++;
//                    echo "\t{$cont}" . "-" . "\t{$produtoFilial->produto_id}\n\n";
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
                        Yii::error($response['body'], 'mercado_livre_closed');
                    }
                }
            }
        }
    }
}