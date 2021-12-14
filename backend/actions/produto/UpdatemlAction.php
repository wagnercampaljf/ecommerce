<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 19/10/2015
 * Time: 13:39
 */

namespace backend\actions\produto;


use common\models\AnoModelo;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\Filial;
use common\models\ProdutoAnoModelo;
use console\controllers\MercadoLivreController;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class UpdatemlAction extends Action
{
    public function run($id,$acao)
    {
        $model = $this->controller->findModel($id);

	//echo $acao.": o id digitado foi ".$model->nome;
	//console\controllers\

	$filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
//            ->andWhere(['id' => 62])
            ->all();

        /* @var $filial Filial */
        foreach ($filials as $filial) {
                $produtoFilials = $filial->getProdutoFilials()
                    ->andWhere([
                        'is not',
                        'meli_id',
                        null
                    ])
                    ->andWhere([
                        '>',
                        'quantidade',
                        0
                    ])
                    ->andWhere([
                       '=',
                       'produto_id',
                       $model->id
                    ])
                    ->all();
		$i=0;
		$valor="";
                /* @var $produtoFilial ProdutoFilial */
                foreach ($produtoFilials as $produtoFilial) {

			if($i==0){
			$valor=$produtoFilial->meli_id;
			}
			$i++;

                }
                $produtoFilial->envio= $acao;
                $produtoFilial->save();


            $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
            if (!isset($subcategoriaMeli)) {
                continue;
            }
            if (is_null($produtoFilial->valorMaisRecente)) {
                Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não possui valor",
                    'error_yii');
                continue;
            }
            $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produto.php',
                ['produto' => $produtoFilial]);

            $title = Yii::t('app', '{nome} ({cod})', [
                'cod' => $produtoFilial->produto->codigo_global,
                'nome' => $produtoFilial->produto->nome
            ]);
            //Update Item
            $body = [
                "title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                "price" => round($produtoFilial->getValorMercadoLivre(), 2),
                "available_quantity" => $produtoFilial->quantidade,
                'attributes' =>[
                    [
                        'id' => 'PART_NUMBER',
                        'name' => 'Número da peça',
                        'value_id' => NULL,
                        'value_name' => $produtoFilial->produto->codigo_global,
                        'value_struct' => NULL,
                        'attribute_group_id' => 'DFLT',
                        'attribute_group_name' => 'Outros',
                    ],
                    [
                        "id"=> "BRAND",
                        "name"=> "Marca",
                        "value_id"=> null,
                        "value_name"=> $produtoFilial->produto->fabricante->nome,
                        "value_struct"=> null,
                        "attribute_group_id"=> "OTHERS",
                        "attribute_group_name"=> "Outros"
                    ],

                ]

            ];
            $response = $meli->put(
                "items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,
                $body,
                []
            );
            Yii::info($response, 'mercado_livre_update');
            if ($response['httpCode'] >= 300) {
                Yii::error($response['body'], 'mercado_livre_update');
            }

            //Update Descrição
            $body = [
                'text' => $page
            ];
            $response = $meli->put(
                "items/{$produtoFilial->meli_id}/description?access_token=" . $meliAccessToken,
                $body,
                []
            );
            Yii::info($response, 'mercado_livre_update');
            if ($response['httpCode'] >= 300) {
                Yii::error($response['body'], 'mercado_livre_update');
            }
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

            //Update Imagem
            $body = [
                "pictures" => $produtoFilial->produto->getUrlImagesML(),
                "shipping" => [
                    "mode"=> $modo,
                    "local_pick_up" => true,
                    "free_shipping" => false,
                    "free_methods" => [],
                ],
                "warranty" => "6 meses",
            ];

            $response = $meli->put(
                "items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,
                $body,
                []
            );
            Yii::info($response, 'mercado_livre_update');
            if ($response['httpCode'] >= 300) {
                Yii::error($response['body'], 'mercado_livre_update');
            }


		$url="https://www.pecaagora.com/backend/web/produto/update?id=".$model->id;

		return Yii::$app->getResponse()->redirect($url)->send();



        }

    }



    private function slugify(&$model)
    {
        $text = $model->nome . ' ' . $model->codigo_global;

        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        $model->slug = $text;
    }

}
