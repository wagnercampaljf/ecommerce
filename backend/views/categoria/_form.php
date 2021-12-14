<?php

use yii\helpers\Html;
use yii\redactor\widgets\Redactor;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Categoria */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="categoria-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'descricao')->widget(Redactor::className()) ?>

    <div class="col-xs-3 clearfix">
        <?php
        echo $form->field($model, 'meli_id')->dropDownList(
            $categorias,
            [
                'class' => 'form-control select2',
            ])->label("Categoria Mercado Livre *");
        ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Editar',
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
