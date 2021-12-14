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
                <?= $form->field($model, 'nome')->textInput(['maxlength' => true, 'required' => true ]) ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'required' => true ]) ?>

                <?= $form->field($model, 'data_compra')->textInput(['required' => true]) ?>

                <?= $form->field($model, 'razao_social')->textInput(['required' => true]) ?>

                <?= $form->field($model, 'nr_nf_compra')->textInput(['required' => true]) ?>

                <?= $form->field($model, 'codigo_peça_seis_digitos')->textInput(['required' => true]) ?>

                <?= $form->field($model, 'modelo_do_veiculo')->textInput(['required' => true]) ?>

                <?= $form->field($model, 'ano')->textInput(['required' => true]) ?>
            </div>
            <div class="col-sm-6">


                <?= $form->field($model, 'chassi')->textInput(['required' => true]) ?>

                <?= $form->field($model, 'numero_de_serie_do_motor')->textInput(['required' => true]) ?>

                <?= $form->field($model, 'data_aplicação')->textInput(['required' => true]) ?>

                <?= $form->field($model, 'km_montagem')->textInput(['required' => true]) ?>

                <?= $form->field($model, 'km_defeito')->textInput(['required' => true]) ?>

                <?= $form->field($model, 'contato')->textInput(['required' => true]) ?>

                <?= $form->field($model, 'telefone')->textInput(['required' => true]) ?>

                <?= $form->field($model, 'descrição_do_defeito_apresentado')->textarea(['required' => true]) ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Enviar' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
