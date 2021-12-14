<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Lojista */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lojista-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'contrato_correios')->textInput(['maxlength' => 150]) ?>

    <?= $form->field($model, 'senha_correios')->passwordInput() ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
