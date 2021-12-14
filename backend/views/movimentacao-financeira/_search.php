<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MovimentacaoFinanceiraSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="movimentacao-financeira-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'numero') ?>

    <?= $form->field($model, 'cliente_cpf_cnpj') ?>

    <?= $form->field($model, 'cliente_nome') ?>

    <?= $form->field($model, 'data_hora') ?>

    <?php // echo $form->field($model, 'valor') ?>

    <?php // echo $form->field($model, 'valor_total') ?>

    <?php // echo $form->field($model, 'operacao_financeira_id') ?>

    <?php // echo $form->field($model, 'movimentacao_financeira_tipo_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
