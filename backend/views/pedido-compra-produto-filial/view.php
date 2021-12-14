<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\PedidoCompraProdutoFilial */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pedidos de Compra', 'url' => ['pedido-compra/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-compra-produto-filial-view">

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
            'quantidade',
            'valor_compra',
            'valor_venda',
            'pedido_compra_id',
            'produto_filial_id',
            'observacao',
        ],
    ]) ?>

</div>