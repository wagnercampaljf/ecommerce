<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\NotaFiscalPedidoProdutoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nota-fiscal-pedido-produto-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nota_fiscal_produto_id') ?>

    <?= $form->field($model, 'pedido_mercado_livre_produto_produto_filial_id') ?>

    <?= $form->field($model, 'pedido_produto_filial_cotacao_id') ?>

    <?= $form->field($model, 'pedido_compras_produto_filial_id') ?>

    <?php // echo $form->field($model, 'e_validado')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
