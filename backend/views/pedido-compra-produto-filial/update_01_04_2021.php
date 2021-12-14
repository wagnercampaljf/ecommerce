<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\ProdutoFilial;
use common\models\Produto;

/* @var $this yii\web\View */
/* @var $model backend\models\PedidoCompraProdutoFilial */

$this->title = 'Atualização Pedido de Compra';
$this->params['breadcrumbs'][] = ['label' => 'Pedidos de Compra', 'url' => ['pedido-compra/index']];
$this->params['breadcrumbs'][] = ['label' => $modelCompra->id, 'url' => ['view', 'id' => $modelCompra->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pedido-compra-produto-filial-update">

    <h1><?= Html::encode('Pedido: ' . $modelCompra->id) ?></h1>

    <?= $this->render('_form', [
        'modelCompra' => $modelCompra,
        'modelProduto' => $modelProduto,
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'footerRowOptions' => ['style' => 'font-weight:bold;text-decoration: underline;'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'quantidade',
            'valor_compra',
            'valor_venda',
            [
                'attribute' =>   '',
                'header' => 'Valor Total',
                'content' => function ($dataProvider) {
                    $valor = $dataProvider->valor_compra * $dataProvider->quantidade;
                    return $valor;
                }
            ],
            'observacao',
            [
                'attribute' =>   '',
                'header' => 'PA',
                'content' => function ($dataProvider) {
                    $filial = ProdutoFilial::find()->andWhere(['=', 'id', $dataProvider->produto_filial_id])->one();
                    $produto = Produto::find()->andWhere(['=', 'id', $filial->produto_id])->one();
                    return 'PA' . $produto['id'];
                }
            ],
            [
                'attribute' =>   '',
                'header' => 'Produto',
                'content' => function ($dataProvider) {
                    $filial = ProdutoFilial::find()->andWhere(['=', 'id', $dataProvider->produto_filial_id])->one();
                    $produto = Produto::find()->andWhere(['=', 'id', $filial->produto_id])->one();
                    return $produto['nome'];
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}{delete}',
                'buttons' => [
                    'update' => function ($url, $dataProvider) {
                        $url = "../pedido-compra-produto-filial/update?id=" . $dataProvider->pedido_compra_id . '&idProduto=' . $dataProvider->id;
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>  ', $url, ['title' => Yii::t('yii', 'Alterar'),]);
                    },
                    'delete' => function ($url, $dataProvider) {
                        $url = "../pedido-compra-produto-filial/delete?id=" . $dataProvider->id;
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('yii', 'Excluir'), 'data-confirm' => 'Confirma a exclusão deste item?', 'data-method' => 'post']);
                    },
                ],
            ],
        ],
    ]);
    ?>
    <div class="col-md-12">
        <div class="form-group">
            <?= Html::a('Enviar Pedido', '../pedido-compra-produto-filial/autorizar-email?id=' . $modelCompra->id, ['class' => 'btn btn-success']); ?>
        </div>
    </div>

</div>