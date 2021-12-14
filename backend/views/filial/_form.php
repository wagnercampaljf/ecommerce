<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Filial */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="filial-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome')->textInput() ?>

    <?= $form->field($model, 'razao')->textInput() ?>

    <?= $form->field($model, 'documento')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'juridica')->checkbox() ?>

    <?= $form->field($model, 'lojista_id')->textInput() ?>

    <?= $form->field($model, 'banco_id')->textInput() ?>

    <?= $form->field($model, 'numero_banco')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'token_moip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'porcentagem_venda')->textInput() ?>

    <?= $form->field($model, 'id_tipo_empresa')->textInput() ?>

    <?= $form->field($model, 'telefone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telefone_alternativo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'refresh_token_meli')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mercado_livre_secundario')->checkbox() ?>

    <?= $form->field($model, 'mercado_livre_logo')->checkbox() ?>

    <?= $form->field($model, 'email_pedido')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
