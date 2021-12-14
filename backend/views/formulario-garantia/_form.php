<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FormularioGarantia */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="formulario-garantia-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'data_compra')->textInput() ?>

    <?= $form->field($model, 'razao_social')->textInput() ?>

    <?= $form->field($model, 'nr_nf_compra')->textInput() ?>

    <?= $form->field($model, 'codigo_peca_seis_digitos')->textInput() ?>

    <?= $form->field($model, 'modelo_do_veiculo')->textInput() ?>

    <?= $form->field($model, 'ano')->textInput() ?>

    <?= $form->field($model, 'chassi')->textInput() ?>

    <?= $form->field($model, 'numero_de_serie_do_motor')->textInput() ?>

    <?= $form->field($model, 'data_aplicacao')->textInput() ?>

    <?= $form->field($model, 'km_montagem')->textInput() ?>

    <?= $form->field($model, 'km_defeito')->textInput() ?>

    <?= $form->field($model, 'contato')->textInput() ?>

    <?= $form->field($model, 'telefone')->textInput() ?>

    <?= $form->field($model, 'descricao_do_defeito_apresentado')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
