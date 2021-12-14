<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoFinanceira */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="movimentacao-financeira-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'numero')->textInput() ?>

    <?= $form->field($model, 'cliente_cpf_cnpj')->textInput() ?>

    <?= $form->field($model, 'cliente_nome')->textInput() ?>

    <?= $form->field($model, 'data_hora')->textInput() ?>

    <?= $form->field($model, 'valor')->textInput() ?>

    <?= $form->field($model, 'valor_total')->textInput() ?>

    <?= $form->field($model, 'operacao_financeira_id')->textInput() ?>

    <?= $form->field($model, 'movimentacao_financeira_tipo_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
