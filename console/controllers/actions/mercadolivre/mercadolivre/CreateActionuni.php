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

class CreateuniAction extends Action
{
    public function run($global_id)
    {
        echo "Criando produtos...\n\n";
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
//            ->andWhere(['id' => 60])
            ->all();

        foreach ($filials as $filial) {
            echo "\t{$filial->nome}\n\n";
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

                $meliAccessToken = $response->access_token;

                $produtoFilials = $filial->getProdutoFilials()
                    ->andWhere([
                        'IS',
                        'meli_id',
                        NULL
                    ])
                    ->andWhere([
                        '>',
                        'quantidade',
                        0
                    ])
		    ->andWhere([
                        '=',
                        'id',
                       $global_id
                    ])
                    ->all();

//                echo 'ioi';
                /* @var $produtoFilial ProdutoFilial */
                foreach ($produtoFilials as $produtoFilial) {
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

                    $title = Yii::t('app', '{nome} ({code})', [
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
                        echo "ok\n\n";
                    }
                }
            }
        }
    }
}
