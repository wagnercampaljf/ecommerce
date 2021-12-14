<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PedidoMercadoLivreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pedido Mercado Livres';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-mercado-livre-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Pedido Mercado Livre', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'pedido_meli_id',
            'date_created',
            'date_closed',
            'last_updated',
            // 'total_amount',
            // 'paid_amount',
            // 'shipping_id',
            // 'status',
            // 'buyer_id',
            // 'buyer_nickname',
            // 'buyer_email:email',
            // 'buyer_first_name',
            // 'buyer_last_name',
            // 'buyer_doc_type',
            // 'buyer_doc_number',
            // 'shipping_base_cost',
            // 'shipping_status',
            // 'shipping_substatus',
            // 'shipping_date_created',
            // 'shipping_last_updated',
            // 'shipping_tracking_number',
            // 'shipping_tracking_method',
            // 'shipping_service_id',
            // 'receiver_id',
            // 'receiver_address_id',
            // 'receiver_address_line',
            // 'receiver_street_name',
            // 'receiver_street_number',
            // 'receiver_comment',
            // 'receiver_zip_code',
            // 'receiver_city_id',
            // 'receiver_city_name',
            // 'receiver_state_id',
            // 'receiver_state_name',
            // 'receiver_country_id',
            // 'receiver_country_name',
            // 'receiver_neighborhood_id',
            // 'receiver_neighborhood_name',
            // 'receiver_municipality_id',
            // 'receiver_municipality_name',
            // 'receiver_delivery_preference',
            // 'receiver_name',
            // 'receiver_phone',
            // 'shipping_option_id',
            // 'shipping_option_shipping_method_id',
            // 'shipping_option_name',
            // 'shipping_option_list_cost',
            // 'shipping_option_cost',
            // 'shipping_option_delivery_type',
            // 'user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
