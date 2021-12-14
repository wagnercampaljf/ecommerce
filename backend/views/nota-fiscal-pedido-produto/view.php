<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\NotaFiscalPedidoProduto */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Nota Fiscal Pedido Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nota-fiscal-pedido-produto-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nota_fiscal_produto_id',
            'pedido_mercado_livre_produto_produto_filial_id',
            'pedido_produto_filial_cotacao_id',
            'pedido_compras_produto_filial_id',
            'e_validado:boolean',
        ],
    ]) ?>

</div>
