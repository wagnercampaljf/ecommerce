<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivre */

$this->title = 'Create Pedido Mercado Livre';
$this->params['breadcrumbs'][] = ['label' => 'Pedido Mercado Livres', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-mercado-livre-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
