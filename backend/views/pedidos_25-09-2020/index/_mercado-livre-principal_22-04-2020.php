<?php

    use common\models\Pedido;
    use yii\grid\GridView;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use backend\models\PedidoMercadoLivreSearch;
    
    \yii\widgets\Pjax::begin(['timeout' => 5000]);
    
    $searchModel = new PedidoMercadoLivreSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                        
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
    
            'id',
            'pedido_meli_id',
            'buyer_first_name',
            'buyer_last_name',
            'total_amount',
            'receiver_phone',
            'status',
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
            [
                'label'     => 'Autorizado?',
                'attribute' => 'e_pedido_autorizado',
                'value'     => function($model) {
                                    if($model->e_pedido_autorizado){
                                        return "Sim";
                                    }
                                    else{
                                        return "Não";
                                    }
                               },
                ],
            [
                'label'     => 'Faturado??',
                'attribute' => 'e_pedido_faturado',
                'value'     => function($model) {
                if($model->e_pedido_faturado){
                    return "Sim";
                }
                else{
                    return "Não";
                }
                },
                ],
            [
                'label'     => 'NF Anexada?',
                'attribute' => 'e_nota_fiscal_anexada',
                'value'     => function($model) {
                if($model->e_nota_fiscal_anexada){
                    return "Sim";
                }
                else{
                    return "Não";
                }
                },
                ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                    return Html::a(' <span class="glyphicon glyphicon-search" ></span > ',
                        Url::to(['/pedidos/mercado-livre-view', 'id' => $model['id']]), [
                            'title' => Yii::t('yii', 'View'),
                        ]);
                    
                    }
                ]
            ],
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); 
    
    \yii\widgets\Pjax::end();

?>
