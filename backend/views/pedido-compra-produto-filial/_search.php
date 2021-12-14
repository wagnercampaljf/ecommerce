<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PedidoCompraProdutoFilialSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pedido-compra-produto-filial-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'quantidade') ?>

    <?= $form->field($model, 'valor_compra') ?>

    <?= $form->field($model, 'valor_venda') ?>

    <?= $form->field($model, 'pedido_compra_id') ?>

    <?php // echo $form->field($model, 'produto_filial_id') ?>

    <?php // echo $form->field($model, 'observacao') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
