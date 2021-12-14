<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivreProduto */

$this->title = 'Create Pedido Mercado Livre Produto';
$this->params['breadcrumbs'][] = ['label' => 'Pedido Mercado Livre Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-mercado-livre-produto-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
