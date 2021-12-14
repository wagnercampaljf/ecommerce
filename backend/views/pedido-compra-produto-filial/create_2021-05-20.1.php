<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use common\models\ProdutoFilial;
use common\models\Produto;
use backend\models\PedidoCompra;


/* @var $this yii\web\View */
/* @var $model backend\models\PedidoCompraProdutoFilial */

$this->title = 'Itens do pedido ';
$this->params['breadcrumbs'][] = ['label' => 'Pedidos de Compra', 'url' => ['pedido-compra/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-compra-produto-filial-create">

    <h1><?= Html::encode('Pedido: ' . $modelCompra->id) ?></h1>


    <?= $this->render('_form', [
        'modelCompra' => $modelCompra,
        'modelProduto' => $modelProduto,

    ]) ?>

    <?= GridView::widget([
        'id' => 'kv-grid-demo',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'bordered' => true,
        'striped' => false,
        'responsive'=>false,
        'responsiveWrap'=>false,
        'floatHeader'=>false,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'persistResize'=>false,
        'showFooter' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'header' => 'Id',
                'contentOptions' => ['style' => 'min-width: 20px;']
            ],

            [
                'attribute' => 'valor_venda',
                'header' => 'Valor Venda',
                'contentOptions' => ['style' => 'min-width: 20px;']
            ],



            'valor_markup',
            'quantidade',
            'valor_compra',
            [
                'attribute' =>   '',
                'header' => 'Valor Total',
                'content' => function ($dataProvider) {
                    $valor = $dataProvider->valor_compra * $dataProvider->quantidade;
                    return $valor;
                },
                'contentOptions' => ['style' => 'min-width: 40px;'],
                'filter' => false,
                'footer' => 'Total Pedido: '. PedidoCompra::findOne($modelCompra->id)->valor_total_pedido,
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
                'header' => 'Cód. Global',
                'content' => function ($dataProvider) {
                    $filial = ProdutoFilial::find()->andWhere(['=', 'id', $dataProvider->produto_filial_id])->one();
                    $produto = Produto::find()->andWhere(['=', 'id', $filial->produto_id])->one();
                    return $produto['codigo_global'];
                }
            ],
            [
                'attribute' =>   '',
                'header' => 'Produto',
                'contentOptions' => ['style' => 'min-width: 450px;'],
                'content' => function ($dataProvider) {
                    $filial = ProdutoFilial::find()->andWhere(['=', 'id', $dataProvider->produto_filial_id])->one();
                    $produto = Produto::find()->andWhere(['=', 'id', $filial->produto_id])->one();
                    return $produto['nome'];
                }
            ],
            [

                'class' => 'yii\grid\CheckboxColumn',
                'header' => 'Site',
                'checkboxOptions' => function ($dataProvider) {
                    $checked = $dataProvider->e_atualizar_site ? true : false;

                    return ['checked' => $checked];
                },

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