<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MarkupMestreSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="markup-mestre-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'data_inicio') ?>

    <?= $form->field($model, 'observacao') ?>

    <?= $form->field($model, 'e_margem_absoluta_padrao')->checkbox() ?>

    <?= $form->field($model, 'e_markup_padrao')->checkbox() ?>

    <?php // echo $form->field($model, 'margem_padrao') ?>

    <?php // echo $form->field($model, 'descricao') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
