<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\NotaFiscalPedidoProduto */

$this->title = 'Update Nota Fiscal Pedido Produto: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Nota Fiscal Pedido Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="nota-fiscal-pedido-produto-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
