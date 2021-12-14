<?php
/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 12/09/17
 * Time: 17:07
 */

use common\models\Pedido;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php

/** @var \yii\data\ArrayDataProvider $dataProvider */
/** @var \common\models\PedidoSkyhubSearch $filterModel */
\yii\widgets\Pjax::begin(['timeout' => 5000]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'code:text:Nº Do Pedido',
        'channel:text:Canal',
        'customer.name:text:Comprador',
        'customer.vat_number:text:CNPJ/CPF',
        [
            'header' => 'Status',
            'value' => function ($data){
                return Pedido::$statusClasses[Pedido::$statusSkyhub[$data['status']['type']]]::getLabel();
            }
        ],
        [
            'header' => 'Data de Referência',
            'value' => function ($data) {
                return explode('T', $data['placed_at'])[0];
            },
            'format' => 'date'
        ],
        [
            'header' => 'Data Prevista',
            'value' => function ($data) {
                $dt_prevista = explode('T', $data['estimated_delivery'])[0];

                $now = date('Y-m-d');
                $color = ($dt_prevista < $now) ? "red" : "green";

                return Html::label('<span style="color:' . $color . '">' .
                    Yii::$app->formatter->asDate($dt_prevista,
                        'dd/MM/yyyy') . '</span> ');
            },
            'format' => 'raw'
        ],
        'shipping_cost:currency:Valor Frete',
        [
            'header' => 'Valor Produto',
            'value' => function ($data) {
                return $data['total_ordered'] - $data['shipping_cost'];
            },
            'format' => 'currency'
        ],
        'total_ordered:currency:Valor Total',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $model) {
                    return Html::a(' <span class="glyphicon glyphicon-search" ></span > ',
                        Url::to(['/pedidos-b2w/skyhub-view', 'code' => $model['code']]), [
                            'title' => Yii::t('yii', 'View'),
                        ]);

                }
            ]
        ],
    ]
]);
\yii\widgets\Pjax::end();
