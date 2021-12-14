<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Processamento */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="processamento-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'funcao_id')->textInput() ?>

    <?= $form->field($model, 'data_hora_inicial')->textInput() ?>

    <?= $form->field($model, 'data_hora_final')->textInput() ?>

    <?= $form->field($model, 'observacao')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
