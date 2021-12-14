<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\NotaFiscalSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nota-fiscal-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'chave_nf') ?>

    <?= $form->field($model, 'valor_nf') ?>

    <?= $form->field($model, 'data_nf') ?>

    <?= $form->field($model, 'id_nf') ?>

    <?php // echo $form->field($model, 'id_pedido') ?>

    <?php // echo $form->field($model, 'numero_nf') ?>

    <?php // echo $form->field($model, 'modo_frete') ?>

    <?php // echo $form->field($model, 'id_recebimento') ?>

    <?php // echo $form->field($model, 'id_transportadora') ?>

    <?php // echo $form->field($model, 'data_cancelamento') ?>

    <?php // echo $form->field($model, 'data_emissao') ?>

    <?php // echo $form->field($model, 'data_inutilizacao') ?>

    <?php // echo $form->field($model, 'data_registro') ?>

    <?php // echo $form->field($model, 'data_saida') ?>

    <?php // echo $form->field($model, 'finalizade_emissao') ?>

    <?php // echo $form->field($model, 'tipo_nf') ?>

    <?php // echo $form->field($model, 'tipo_ambiente') ?>

    <?php // echo $form->field($model, 'serie') ?>

    <?php // echo $form->field($model, 'codigo_modelo') ?>

    <?php // echo $form->field($model, 'indice_pagamento') ?>

    <?php // echo $form->field($model, 'h_saida_entrada_nf') ?>

    <?php // echo $form->field($model, 'h_emissao') ?>

    <?php // echo $form->field($model, 'cod_int_empresa') ?>

    <?php // echo $form->field($model, 'cod_empresa') ?>

    <?php // echo $form->field($model, 'cod_int_cliente_fornecedor') ?>

    <?php // echo $form->field($model, 'cod_cliente') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
