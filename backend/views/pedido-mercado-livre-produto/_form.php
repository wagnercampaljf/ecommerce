<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivreProduto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pedido-mercado-livre-produto-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pedido_mercado_livre_id')->textInput() ?>

    <?= $form->field($model, 'produto_filial_id')->textInput() ?>

    <?= $form->field($model, 'produto_meli_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'categoria_meli_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'condition')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'unit_price')->textInput() ?>

    <?= $form->field($model, 'full_unit_price')->textInput() ?>

    <?= $form->field($model, 'sale_fee')->textInput() ?>

    <?= $form->field($model, 'listing_type_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
