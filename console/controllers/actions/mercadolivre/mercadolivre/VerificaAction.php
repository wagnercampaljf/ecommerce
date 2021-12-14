<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 29/06/2016
 * Time: 16:49
 */
namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class VerificaAction extends Action
{
    public function run()
    {
        echo "Verificando produtos...\n\n";
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => 96])
            ->all();

        foreach ($filials as $filial) {
            echo "\t{$filial->nome}\n\n";
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                //echo "entrou no if";
                $meliAccessToken = $response->access_token;

                $produtoFilials = $filial->getProdutoFilials()
                    ->andWhere(['IS NOT','meli_id',NULL])
                    ->andWhere(['>','quantidade',0])
                    //->andWhere(['=','id',36304])
                    ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    //echo $produtoFilial->produto->id." - ".$produtoFilial->meli_id." - ".$meliAccessToken."\n";

                    $response = $meli->get("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken);
                    print_r($response);
                    die;
                    //if($response['httpCode'] < 400){
                    //    echo ($response['body']->price)." - ".$produtoFilial->getValorMercadoLivre();
                    //}                   
                }
            }
        }
    }
}
