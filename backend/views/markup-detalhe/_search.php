<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MarkupDetalheSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="markup-detalhe-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'markup_mestre_id') ?>

    <?= $form->field($model, 'e_margem_absoluta')->checkbox() ?>

    <?= $form->field($model, 'valor_minimo') ?>

    <?= $form->field($model, 'valor_maximo') ?>

    <?php // echo $form->field($model, 'margem') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
