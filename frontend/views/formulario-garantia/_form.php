<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FormularioGarantia */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="formulario-garantia-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'nome')->textInput(['maxlength' => true, 'required'=>true]) ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'type' => 'email']) ?>

                <?= $form->field($model, 'data_compra')->textInput([ 'required'=>true,'type' => 'date',]) ?>

                <?= $form->field($model, 'razao_social')->textInput([ 'required'=>true]) ?>

                <?= $form->field($model, 'nr_nf_compra')->textInput([ 'required'=>true]) ?>

                <?= $form->field($model, 'codigo_peca_seis_digitos')->textInput([ 'required'=>true, 'maxlength' => 6,]) ?>

                <?= $form->field($model, 'modelo_do_veiculo')->textInput([ 'required'=>true]) ?>

                <?= $form->field($model, 'ano')->textInput([ 'required'=>true, 'type' => 'number']) ?>
            </div>
            <div class="col-sm-6">


                <?= $form->field($model, 'chassi')->textInput([ 'required'=>true]) ?>

                <?= $form->field($model, 'numero_de_serie_do_motor')->textInput([ 'required'=>true]) ?>

                <?= $form->field($model, 'data_aplicacao')->textInput([ 'required'=>true, 'type' => 'date']) ?>

                <?= $form->field($model, 'km_montagem')->textInput([ 'required'=>true, 'type' => 'number']) ?>

                <?= $form->field($model, 'km_defeito')->textInput([ 'required'=>true, 'type' => 'number']) ?>

                <?= $form->field($model, 'contato')->textInput([ 'required'=>true]) ?>

                <?= $form->field($model, 'telefone')->textInput([ 'required'=>true, 'type' => 'number']) ?>

                <?= $form->field($model, 'descricao_do_defeito_apresentado')->textarea([ 'required'=>true]) ?>
            </div>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Enviar' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


