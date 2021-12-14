<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PedidoMercadoLivreSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pedido-mercado-livre-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'pedido_meli_id') ?>

    <?= $form->field($model, 'date_created') ?>

    <?= $form->field($model, 'date_closed') ?>

    <?= $form->field($model, 'last_updated') ?>

    <?php // echo $form->field($model, 'total_amount') ?>

    <?php // echo $form->field($model, 'paid_amount') ?>

    <?php // echo $form->field($model, 'shipping_id') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'buyer_id') ?>

    <?php // echo $form->field($model, 'buyer_nickname') ?>

    <?php // echo $form->field($model, 'buyer_email') ?>

    <?php // echo $form->field($model, 'buyer_first_name') ?>

    <?php // echo $form->field($model, 'buyer_last_name') ?>

    <?php // echo $form->field($model, 'buyer_doc_type') ?>

    <?php // echo $form->field($model, 'buyer_doc_number') ?>

    <?php // echo $form->field($model, 'shipping_base_cost') ?>

    <?php // echo $form->field($model, 'shipping_status') ?>

    <?php // echo $form->field($model, 'shipping_substatus') ?>

    <?php // echo $form->field($model, 'shipping_date_created') ?>

    <?php // echo $form->field($model, 'shipping_last_updated') ?>

    <?php // echo $form->field($model, 'shipping_tracking_number') ?>

    <?php // echo $form->field($model, 'shipping_tracking_method') ?>

    <?php // echo $form->field($model, 'shipping_service_id') ?>

    <?php // echo $form->field($model, 'receiver_id') ?>

    <?php // echo $form->field($model, 'receiver_address_id') ?>

    <?php // echo $form->field($model, 'receiver_address_line') ?>

    <?php // echo $form->field($model, 'receiver_street_name') ?>

    <?php // echo $form->field($model, 'receiver_street_number') ?>

    <?php // echo $form->field($model, 'receiver_comment') ?>

    <?php // echo $form->field($model, 'receiver_zip_code') ?>

    <?php // echo $form->field($model, 'receiver_city_id') ?>

    <?php // echo $form->field($model, 'receiver_city_name') ?>

    <?php // echo $form->field($model, 'receiver_state_id') ?>

    <?php // echo $form->field($model, 'receiver_state_name') ?>

    <?php // echo $form->field($model, 'receiver_country_id') ?>

    <?php // echo $form->field($model, 'receiver_country_name') ?>

    <?php // echo $form->field($model, 'receiver_neighborhood_id') ?>

    <?php // echo $form->field($model, 'receiver_neighborhood_name') ?>

    <?php // echo $form->field($model, 'receiver_municipality_id') ?>

    <?php // echo $form->field($model, 'receiver_municipality_name') ?>

    <?php // echo $form->field($model, 'receiver_delivery_preference') ?>

    <?php // echo $form->field($model, 'receiver_name') ?>

    <?php // echo $form->field($model, 'receiver_phone') ?>

    <?php // echo $form->field($model, 'shipping_option_id') ?>

    <?php // echo $form->field($model, 'shipping_option_shipping_method_id') ?>

    <?php // echo $form->field($model, 'shipping_option_name') ?>

    <?php // echo $form->field($model, 'shipping_option_list_cost') ?>

    <?php // echo $form->field($model, 'shipping_option_cost') ?>

    <?php // echo $form->field($model, 'shipping_option_delivery_type') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
