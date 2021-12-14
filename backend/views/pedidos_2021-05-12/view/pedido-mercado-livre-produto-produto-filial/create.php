<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivreProdutoProdutoFilial */

$this->title = 'Create Pedido Mercado Livre Produto Produto Filial';
$this->params['breadcrumbs'][] = ['label' => 'Pedido Mercado Livre Produto Produto Filials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-mercado-livre-produto-produto-filial-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
