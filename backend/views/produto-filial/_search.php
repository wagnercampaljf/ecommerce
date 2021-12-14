<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProdutoFilialSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="produto-filial-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'produto_id') ?>

    <?= $form->field($model, 'filial_id') ?>

    <?= $form->field($model, 'quantidade') ?>

    <?= $form->field($model, 'meli_id') ?>

    <?php // echo $form->field($model, 'status_b2w')->checkbox() ?>

    <?php // echo $form->field($model, 'envio') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
