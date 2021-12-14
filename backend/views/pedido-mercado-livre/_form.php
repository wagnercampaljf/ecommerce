<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivre */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pedido-mercado-livre-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pedido_meli_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date_created')->textInput() ?>

    <?= $form->field($model, 'date_closed')->textInput() ?>

    <?= $form->field($model, 'last_updated')->textInput() ?>

    <?= $form->field($model, 'total_amount')->textInput() ?>

    <?= $form->field($model, 'paid_amount')->textInput() ?>

    <?= $form->field($model, 'shipping_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'buyer_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'buyer_nickname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'buyer_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'buyer_first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'buyer_last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'buyer_doc_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'buyer_doc_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_base_cost')->textInput() ?>

    <?= $form->field($model, 'shipping_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_substatus')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_date_created')->textInput() ?>

    <?= $form->field($model, 'shipping_last_updated')->textInput() ?>

    <?= $form->field($model, 'shipping_tracking_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_tracking_method')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_service_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_address_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_address_line')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_street_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_street_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_zip_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_city_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_city_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_state_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_state_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_country_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_country_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_neighborhood_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_neighborhood_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_municipality_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_municipality_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_delivery_preference')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_option_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_option_shipping_method_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_option_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_option_list_cost')->textInput() ?>

    <?= $form->field($model, 'shipping_option_cost')->textInput() ?>

    <?= $form->field($model, 'shipping_option_delivery_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
