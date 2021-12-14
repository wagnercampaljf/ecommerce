<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MovimentacaoEstoqueDetalheSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="movimentacao-estoque-detalhe-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'descricao') ?>

    <?= $form->field($model, 'produto_id') ?>

    <?= $form->field($model, 'salvo_em') ?>

    <?= $form->field($model, 'salvo_por') ?>

    <?php // echo $form->field($model, 'quantidade') ?>

    <?php // echo $form->field($model, 'id_ajuste_omie_entrada') ?>

    <?php // echo $form->field($model, 'id_ajuste_omie_saida') ?>

    <?php // echo $form->field($model, 'movimentacao_estoque_mestre_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
