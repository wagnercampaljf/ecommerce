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
    'filterModel' => $filterModel,
    'dataProvider' => $dataProvider,
    'columns' => [
        'id:text:Nº Do Pedido',
        'canal',
        'comprador',
        'documento:text:CNPJ/CPF',
        [
            'header' => 'Status',
            'attribute' => 'status',
            'value' => function ($data) {
                return Pedido::$statusClasses[Pedido::$statusSkyhub[$data->status]]::getLabel();
            },
            'filter' => Html::activeDropDownList($filterModel, 'status', [
                '' => '-',
                'NEW' => 'Em Aberto',
                'APPROVED' => 'Confirmado',
                'SHIPPED' => 'Enviado',
                'DELIVERED' => 'Concluído',
                'CANCELED' => 'Cancelado'
            ], ['class' => 'form-control select2'])
        ],
        ['header' => 'Data de Referência',
            'value' => function ($data) {
                return explode('T', $data->dt_referencia)[0];
            },
            'format' => 'date'
        ],
        [
            'header' => 'Data Prevista',
            'value' => function ($data) {
                $dt_prevista = explode('T', $data->dt_prevista)[0];

                $now = date('Y-m-d');
                $color = ($dt_prevista < $now) ? "red" : "green";

                return Html::label('<span style="color:' . $color . '">' .
                    Yii::$app->formatter->asDate($dt_prevista,
                        'dd/MM/yyyy') . '</span> ');
            },
            'format' => 'raw'
        ],
        'valor_frete',
        [
            'header' => 'Valor Produto',
            'value' => function ($data) {
                return $data->valor_total - $data->valor_frete;
            },
            'format' => 'currency'
        ],
        'valor_total:currency:Valor Total',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $model) {
                    return Html::a(' <span class="glyphicon glyphicon-search" ></span > ',
                        Url::to(['/pedidos/skyhub-view', 'code' => $model['id']]), [
                            'title' => Yii::t('yii', 'View'),
                        ]);

                }
            ]
        ],
    ]
]);
\yii\widgets\Pjax::end();
