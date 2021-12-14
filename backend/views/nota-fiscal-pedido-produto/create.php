<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\NotaFiscalPedidoProduto */

$this->title = 'Create Nota Fiscal Pedido Produto';
$this->params['breadcrumbs'][] = ['label' => 'Nota Fiscal Pedido Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nota-fiscal-pedido-produto-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
