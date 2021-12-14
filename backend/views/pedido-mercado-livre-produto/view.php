<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivreProduto */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Pedido Mercado Livre Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-mercado-livre-produto-view">

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
            'pedido_mercado_livre_id',
            'produto_filial_id',
            'produto_meli_id',
            'title',
            'categoria_meli_id',
            'condition',
            'quantity',
            'unit_price',
            'full_unit_price',
            'sale_fee',
            'listing_type_id',
        ],
    ]) ?>

</div>
