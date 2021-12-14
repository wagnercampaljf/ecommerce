<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivreProduto */

$this->title = 'Update Pedido Mercado Livre Produto: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Pedido Mercado Livre Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pedido-mercado-livre-produto-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
