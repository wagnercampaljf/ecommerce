<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PedidoCompraSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pedido-compra-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'descricao') ?>

    <?= $form->field($model, 'data') ?>

    <?= $form->field($model, 'observacao') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'assunto_email') ?>

    <?php // echo $form->field($model, 'corpo_email') ?>

    <?php // echo $form->field($model, 'filial_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
