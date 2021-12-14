<?php

namespace frontend\controllers;

use common\models\Produto;
use common\models\ProdutoFilial;
use Yii;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use frontend\controllers\actions\common;

class ProductController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'get-frete' => [
                'class' => 'frontend\controllers\actions\common\FreteAction',
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Visualização do produto. O slug não influencia na execução desta action apenas
     * é enviada por padrão por questões de SEO.
     * @param $id o id do produto
     * @throws NotFoundHttpException se não existir o produto
     * @author Vinicius Schettino 03/12/2014
     */
    public function actionView($id)
    {
        $p = Produto::find()->readyToView()->andWhere(['produto.id' => $id])->one();
        $dt_ref = date('Y-m-d H:i:s');

        if ($p != null) {
            $filiais = $p->getFiliaisProduto()->join('INNER JOIN', 'valor_produto_filial',
                'valor_produto_filial.produto_filial_id =produto_filial.id')->with(
                [
                    'filial',
                    'filial.lojista',
                    'filial.enderecoFilial',
                    'filial.enderecoFilial.cidade',
                    'filial.enderecoFilial.cidade.estado'
                ]
            )
                ->andWhere('valor_produto_filial.dt_inicio <= \'' . $dt_ref . '\'')
                ->andWhere('valor_produto_filial.dt_fim >=\'' . $dt_ref . '\' OR dt_fim IS NULL')
                ->lojistaAtivo()
//                ->produtoDisponivel()
                ->all();
//            echo "<pre>";
//            var_dump($filiais);
            if (!empty($filiais)) {
                return $this->render('view', ['produto' => $p, 'filiais' => $filiais]);
            }
        }
        throw new NotFoundHttpException('Produto não encontrado');
    }

    public function actionAddToCart($id, $qte = 1, $replace = false)
    {
        $carrinho = Yii::$app->session["carrinho"];
        $pf = ProdutoFilial::find()->with(['filial', 'produto', 'valorProdutoFilials'])->andWhere(['id' => $id])->one();
        if (!is_null($pf)) {
            $qte_total = $qte;
            (isset($carrinho[$pf->id]) && !$replace) ? $qte_total += $qte : $qte_total = $qte;
            $valor = $pf->getValorProdutoFilials()->ativo()->one()->getValorFinal(Yii::$app->params['isJuridica']());
            if ($pf->quantidade < $qte) {
                $qte_total = $pf->quantidade;

                return Json::encode([
                    'error_code' => 401,
                    'msg' => 'Não possuimos ' . $qte . ' unidades deste produto',
                    'qt_max' => $pf->quantidade,
                    'valorProduto' => $valor,
                    'totalProduto' => $carrinho[$pf->id],
                    'valorProdutoTotal' => $valor * $carrinho[$pf->id],
                    'labelValorProdutoTotal' => Yii::$app->formatter->asCurrency($valor * $carrinho[$pf->id]),
                ]);
            }
            $carrinho[$pf->id] = $qte_total;
            Yii::$app->session["carrinho"] = $carrinho;

            return Json::encode(
                [
                    'error_code' => 0,
                    'carrinho_count' => count(\Yii::$app->session["carrinho"]),
                    'novo_produto' => $pf->produto->nome,
                    'msg' => 'Produto Adicionado!',
                    'valorProduto' => $valor,
                    'totalProduto' => $carrinho[$pf->id],
                    'valorProdutoTotal' => $valor * $carrinho[$pf->id],
                    'labelValorProdutoTotal' => Yii::$app->formatter->asCurrency($valor * $carrinho[$pf->id]),
                ]
            );
        } else {
            return Json::encode(['error_code' => 404, 'msg' => 'Produto Não Encontradooo']);
        }
    }

}
