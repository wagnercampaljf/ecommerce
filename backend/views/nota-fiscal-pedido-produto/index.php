<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\NotaFiscalPedidoProdutoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Nota Fiscal Pedido Produtos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nota-fiscal-pedido-produto-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Nota Fiscal Pedido Produto', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nota_fiscal_produto_id',
            'pedido_mercado_livre_produto_produto_filial_id',
            'pedido_produto_filial_cotacao_id',
            'pedido_compras_produto_filial_id',
            // 'e_validado:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
