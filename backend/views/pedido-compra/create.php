<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PedidoCompra */

$this->title = 'Criar Pedido de Compra';
$this->params['breadcrumbs'][] = ['label' => 'Pedido Compras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-compra-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
