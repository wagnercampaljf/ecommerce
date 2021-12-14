<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProdutoFilialVariacao */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="produto-filial-variacao-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'produto_filial_id')->textInput() ?>

    <?= $form->field($model, 'variacao_id')->textInput() ?>

    <?= $form->field($model, 'meli_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
