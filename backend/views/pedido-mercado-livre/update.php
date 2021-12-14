<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivre */

$this->title = 'Update Pedido Mercado Livre: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pedido Mercado Livres', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pedido-mercado-livre-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
