<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoEstoqueDetalhe */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="movimentacao-estoque-detalhe-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'descricao')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'produto_id')->textInput() ?>

    <?= $form->field($model, 'salvo_em')->textInput() ?>

    <?= $form->field($model, 'salvo_por')->textInput() ?>

    <?= $form->field($model, 'quantidade')->textInput() ?>

    <?= $form->field($model, 'id_ajuste_omie_entrada')->textInput() ?>

    <?= $form->field($model, 'id_ajuste_omie_saida')->textInput() ?>

    <?= $form->field($model, 'movimentacao_estoque_mestre_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
