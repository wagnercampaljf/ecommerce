<?php
/**
 * Created by Gnome-Builder.
 * User: andre
 * Date: 30/01/2018
 * Time: 11:06
 */

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class ResetAction extends Action
{
    public function run($global_id)
    {
       	echo "Produto resetado \n".$global_id."\n";
	$meli = new Meli(static::APP_ID, static::SECRET_KEY);
	$filials = Filial::find()->andWhere(['IS NOT', 'refresh_token_meli', null])->all();
	foreach ($filials as $filial) {
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                $produtoFilials = $filial->getProdutoFilials()
                //->andWhere([
            //     '=',
               //     'produto_id',
               //     "(SELECT id from produto WHERE codigo_global='".$global_id."')"
               // ])
               // ->andWhere([
               //     'is not',
               //     'meli_id',
               //     null
               // ])
                ->andWhere([
                    '=',
                    'meli_id',
                    ''.$global_id.''
                ])->all();

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

                    $produtoFilial->meli_id = null;
                    if (!$produtoFilial->save()) {
                        Yii::error($produtoFilial->errors, 'error_yii');
                    }
                    echo $produtoFilial->produto->id."\n";
                    $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                    if (!isset($subcategoriaMeli)) {
                        Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não categoria",
                            'error_yii');
                        echo "1\n";
                        continue;
                    }
                    if (is_null($produtoFilial->valorMaisRecente)) {
                        Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não possui valor",
                            'error_yii');
                        echo "2\n";
                        continue;
                    }
                    $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produto.php',
                        ['produto' => $produtoFilial]);

                    $title = Yii::t('app', '{nome}', [
                        'cod' => $produtoFilial->produto->codigo_global,
                        'nome' => $produtoFilial->produto->nome
                    ]);
                    switch ($produtoFilial->envio) {
                        case 1:
                            $modo = "me2";
                            break;
                        case 2:
                            $modo = "not_specified";
                            break;
                        case 3:
                            $modo = "custom";
                            break;
                    }
                    $body = [
                        "title" => utf8_encode((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                        "category_id" => utf8_encode($subcategoriaMeli),
                        "listing_type_id" => "bronze",
                        "currency_id" => "BRL",
                        "price" => utf8_encode(round($produtoFilial->getValorMercadoLivre(), 2)),
                        "available_quantity" => utf8_encode($produtoFilial->quantidade),
                        "seller_custom_field" => utf8_encode($produtoFilial->id),
                        "condition" => "new",
                        "description" => utf8_encode($page),
                        "pictures" => [
                            ["source" => utf8_encode($produtoFilial->produto->getUrlImageML())],
                        ],
                        "shipping" => [
                            "mode" => $modo,
                            "local_pick_up" => true,
                            "free_shipping" => false,
                            "free_methods" => [],
                        ],
                        "warranty" => "6 meses",
                    ];

                    $response = $meli->post(
                        "items?access_token=" . $meliAccessToken,
                        $body
                    );
                    Yii::info(ArrayHelper::merge($response, ['request' => $body]), 'mercado_livre_create');
                    if ($response['httpCode'] >= 300) {
                        Yii::error($response['body'], 'mercado_livre_create');
                        var_dump($response['body']);
                        echo "\n";
                    } else {
                        $produtoFilial->meli_id = $response['body']->id;
                        if (!$produtoFilial->save()) {
                            Yii::error($produtoFilial->getErrors(), 'error_yii');
                        }
                        echo "Novo Anúncio criado\n\n";
                        echo "id novo: ".$response['body']->id."\n";
                    }

                }

            }
        }
    }
}
