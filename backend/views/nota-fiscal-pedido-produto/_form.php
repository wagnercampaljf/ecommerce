<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\NotaFiscalPedidoProduto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nota-fiscal-pedido-produto-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nota_fiscal_produto_id')->textInput() ?>

    <?= $form->field($model, 'pedido_mercado_livre_produto_produto_filial_id')->textInput() ?>

    <?= $form->field($model, 'pedido_produto_filial_cotacao_id')->textInput() ?>

    <?= $form->field($model, 'pedido_compras_produto_filial_id')->textInput() ?>

    <?= $form->field($model, 'e_validado')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
