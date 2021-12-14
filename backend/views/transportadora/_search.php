<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TransportadoraSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transportadora-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'codigo') ?>

    <?= $form->field($model, 'filial_id') ?>

    <?= $form->field($model, 'codigo_omie') ?>

    <?php // echo $form->field($model, 'razao_social') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'cnpj') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
