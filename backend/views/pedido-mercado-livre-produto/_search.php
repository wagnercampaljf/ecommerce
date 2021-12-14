<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PedidoMercadoLivreProdutoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pedido-mercado-livre-produto-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'pedido_mercado_livre_id') ?>

    <?= $form->field($model, 'produto_filial_id') ?>

    <?= $form->field($model, 'produto_meli_id') ?>

    <?= $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'categoria_meli_id') ?>

    <?php // echo $form->field($model, 'condition') ?>

    <?php // echo $form->field($model, 'quantity') ?>

    <?php // echo $form->field($model, 'unit_price') ?>

    <?php // echo $form->field($model, 'full_unit_price') ?>

    <?php // echo $form->field($model, 'sale_fee') ?>

    <?php // echo $form->field($model, 'listing_type_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
