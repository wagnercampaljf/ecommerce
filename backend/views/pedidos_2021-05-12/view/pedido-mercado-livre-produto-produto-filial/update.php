<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivreProdutoProdutoFilial */

$this->title = 'Update Pedido Mercado Livre Produto Produto Filial: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pedido Mercado Livre Produto Produto Filials', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pedido-mercado-livre-produto-produto-filial-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
