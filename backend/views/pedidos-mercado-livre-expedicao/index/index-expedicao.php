<?php

use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Pedidos para Expedição';

?>

<div class="pedido-index">

	<div class="container">
		<div class="row">
			<h1><b><?= $status?></b></h1>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-12" style="padding-top: 6px">  
			<form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos-mercado-livre-expedicao/expedicao']) ?>">
				<div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left: 0px !important;padding-right: 0px !important;">
					<input type="text"
                           name="chave"
                           id="chave" class="form-control form-control-search input-lg data-hj-whitelist"
                           placeholder="Chave da nota ..."
                           autofocus="true">
                    <span class="input-group-btn">
						<button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
	                </span>
				</div>
			</form>
		</div>
		</div>
    </div><br>

    <div class="container">
      
		<?php 

		echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'pedido_meli_id',
            //'date_created',
            //'date_closed',
            //'last_updated',
            // 'total_amount',
            // 'paid_amount',
            // 'shipping_id',
            // 'status',
            // 'buyer_id',
            // 'buyer_nickname',
            // 'buyer_email:email',
            'buyer_first_name',
            'buyer_last_name',
            'buyer_doc_type',
            'buyer_doc_number',
            // 'shipping_base_cost',
            'shipping_status',
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
            //'e_pedido_enviado',

        ],
    ]); ?>
      
    </div>




</div>
