<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FilialSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="filial-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'razao') ?>

    <?= $form->field($model, 'documento') ?>

    <?= $form->field($model, 'juridica')->checkbox() ?>

    <?php // echo $form->field($model, 'lojista_id') ?>

    <?php // echo $form->field($model, 'banco_id') ?>

    <?php // echo $form->field($model, 'numero_banco') ?>

    <?php // echo $form->field($model, 'token_moip') ?>

    <?php // echo $form->field($model, 'porcentagem_venda') ?>

    <?php // echo $form->field($model, 'id_tipo_empresa') ?>

    <?php // echo $form->field($model, 'telefone') ?>

    <?php // echo $form->field($model, 'telefone_alternativo') ?>

    <?php // echo $form->field($model, 'refresh_token_meli') ?>

    <?php // echo $form->field($model, 'integrar_b2w')->checkbox() ?>

    <?php // echo $form->field($model, 'envio') ?>

    <?php // echo $form->field($model, 'mercado_livre_secundario')->checkbox() ?>

    <?php // echo $form->field($model, 'mercado_livre_logo')->checkbox() ?>

    <?php // echo $form->field($model, 'email_pedido') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
