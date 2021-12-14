<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\NotaFiscalProduto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nota-fiscal-produto-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nota_fiscal_id')->textInput() ?>

    <?= $form->field($model, 'valor_produto')->textInput() ?>

    <?= $form->field($model, 'codigo_produto')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'descricao')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pa_produto')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cod_int_item')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cod_int_produto')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cod_item')->textInput() ?>

    <?= $form->field($model, 'cod_produto')->textInput() ?>

    <?= $form->field($model, 'cod_fiscal_operacao_servico')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cod_situacao_tributaria_icms')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cod_ncm')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ean')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ean_tributável')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'codigo_produto_original')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'codigo_local_estoque')->textInput() ?>

    <?= $form->field($model, 'cmc_total')->textInput() ?>

    <?= $form->field($model, 'cmc_unitario')->textInput() ?>

    <?= $form->field($model, 'aliquota_icms')->textInput() ?>

    <?= $form->field($model, 'qtd_comercial')->textInput() ?>

    <?= $form->field($model, 'qtd_tributavel')->textInput() ?>

    <?= $form->field($model, 'unid_tributavel')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'valor_desconto')->textInput() ?>

    <?= $form->field($model, 'valor_total_frete')->textInput() ?>

    <?= $form->field($model, 'valor_icms')->textInput() ?>

    <?= $form->field($model, 'outras_despesas')->textInput() ?>

    <?= $form->field($model, 'valor_unitario_tributacao')->textInput() ?>

    <?= $form->field($model, 'descricao_original')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
