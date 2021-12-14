<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\NotaFiscalProdutoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nota-fiscal-produto-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nota_fiscal_id') ?>

    <?= $form->field($model, 'valor_produto') ?>

    <?= $form->field($model, 'codigo_produto') ?>

    <?= $form->field($model, 'descricao') ?>

    <?php // echo $form->field($model, 'pa_produto') ?>

    <?php // echo $form->field($model, 'cod_int_item') ?>

    <?php // echo $form->field($model, 'cod_int_produto') ?>

    <?php // echo $form->field($model, 'cod_item') ?>

    <?php // echo $form->field($model, 'cod_produto') ?>

    <?php // echo $form->field($model, 'cod_fiscal_operacao_servico') ?>

    <?php // echo $form->field($model, 'cod_situacao_tributaria_icms') ?>

    <?php // echo $form->field($model, 'cod_ncm') ?>

    <?php // echo $form->field($model, 'ean') ?>

    <?php // echo $form->field($model, 'ean_tributável') ?>

    <?php // echo $form->field($model, 'codigo_produto_original') ?>

    <?php // echo $form->field($model, 'codigo_local_estoque') ?>

    <?php // echo $form->field($model, 'cmc_total') ?>

    <?php // echo $form->field($model, 'cmc_unitario') ?>

    <?php // echo $form->field($model, 'aliquota_icms') ?>

    <?php // echo $form->field($model, 'qtd_comercial') ?>

    <?php // echo $form->field($model, 'qtd_tributavel') ?>

    <?php // echo $form->field($model, 'unid_tributavel') ?>

    <?php // echo $form->field($model, 'valor_desconto') ?>

    <?php // echo $form->field($model, 'valor_total_frete') ?>

    <?php // echo $form->field($model, 'valor_icms') ?>

    <?php // echo $form->field($model, 'outras_despesas') ?>

    <?php // echo $form->field($model, 'valor_unitario_tributacao') ?>

    <?php // echo $form->field($model, 'descricao_original') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
