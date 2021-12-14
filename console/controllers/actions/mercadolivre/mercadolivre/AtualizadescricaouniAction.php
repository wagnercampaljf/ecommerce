<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 27/06/2016
 * Time: 18:54
 */
/* SELECT id from produto_filial where produto_id = (SELECT id from produto WHERE codigo_global='242337'); */
namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class AtualizaCategoriaAction extends Action
{
    public function run($global_id)
    {
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['id' => 72])
            ->all();

        /* @var $filial Filial */
        foreach ($filials as $filial) {


            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
           
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                
                $produtoFilials = $filial->getProdutoFilials()
                    ->andWhere(['is not','meli_id',null])
                    ->andWhere(['>','quantidade',0])
		            ->andWhere(['=','meli_id',$global_id])
//                   ->andWhere(['=','filial_id',86])
                    ->all();
                /* @var $produtoFilial ProdutoFilial */
                foreach ($produtoFilials as $produtoFilial) {
         
                    $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',
                        ['produto' => $produtoFilial]);
                    
                    //Update Descrição
                    //$body = ['text' => $page];
                    $body = ['plain_text' => $page];
                    
                    $response = $meli->put(
                        "items/{$produtoFilial->meli_id}/description?access_token=" . $meliAccessToken,
                        $body,
                        []
                    );
                    print_r($response);
                    /*Yii::info($response, 'mercado_livre_update');
                    if ($response['httpCode'] >= 300) {
                        Yii::error($response['body'], 'mercado_livre_update');
                    }*/
                }
            }
        }
    }
}

