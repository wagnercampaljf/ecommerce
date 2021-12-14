<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MovimentacaoEstoqueMestreSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="movimentacao-estoque-mestre-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'descricao') ?>

    <?= $form->field($model, 'e_autorizado')->checkbox() ?>

    <?= $form->field($model, 'autorizado_por') ?>

    <?= $form->field($model, 'salvo_em') ?>

    <?php // echo $form->field($model, 'salvo_por') ?>

    <?php // echo $form->field($model, 'filial_origem_id') ?>

    <?php // echo $form->field($model, 'filial_destino_id') ?>

    <?php // echo $form->field($model, 'codigo_remessa_omie') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
