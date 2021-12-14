<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $skyhubDataProvider yii\data\ActiveDataProvider */
/* @var $filterModel \common\models\PedidoSearch */
/* @var $skyhubFilterModel \common\models\PedidoSkyhubSearch */

$this->title = 'Pedidos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-index">
    <div class="portlet light">
        <div class="portlet-body">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#geral" data-toggle="tab">Geral</a>
                </li>
                <li><a href="#b2w" data-toggle="tab">B2W</a>
                </li>
            </ul>
            <div class="tab-content clearfix">
                <div class="tab-pane fade in active" id="geral">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $filterModel,
                        'columns' => [
                            //['class' => 'yii\grid\SerialColumn'],

                            [
                                'attribute' => 'id',
                                'label' => 'Nº Do Pedido',
                                'value' => 'id'
                            ],
                            [
                                'attribute' => 'Vendedor',
                                'value' => 'filial.nome',
                            ],
                            [
                                'attribute' => 'Comprador',
                                'value' => 'comprador.empresa.nome',
                            ],
                            [
                                'attribute' => 'documento',
                                'format' => 'raw',
                                'header' => 'CNPJ / CPF',
                                'value' => function ($data) {
                                    return $data->comprador->empresa->getDocumentoLabel();
                                },
                            ],
                            [
                                'attribute' => 'status',
                                'value' => 'statusAtual.tipoStatus.nome',
                            ],
                            'dt_referencia:date',
                            [
                                'attribute' => 'Data Prevista',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $now = date('Y-m-d');
                                    $color = ($model->data_prevista < $now) ? "red" : "green";

                                    return Html::label(' <font color=' . $color . '>' . Yii::$app->formatter->asDate($model->data_prevista,
                                            'dd/MM/yyyy') . '</font> ');
                                },
                            ],
                            'valor_frete:currency',
                            [
                                'attribute' => 'valor_produto',
                                'format' => 'currency',
                                'value' => 'valor_total',
                            ],
                            [
                                'attribute' => 'Valor Total',
                                'format' => 'currency',
                                'value' => function ($model) {
                                    return $model->valor_total + $model->valor_frete;
                                },
                            ],
                            // 'transportadora_id',
                            // 'forma_pagamento_id',
                            // 'token_moip',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view}',
                                'buttons' => [
                                    'view' => function ($url, $model) {
                                        return Html::a(' <span class="glyphicon glyphicon-search" ></span > ',
                                            Url::to(['/pedidos/view', 'id' => $model->id]), [
                                                'title' => Yii::t('yii', 'View'),
                                            ]);

                                    }
                                ]
                            ],
                        ],
                    ]); ?>
                </div>
                <div class="tab-panel fade" id="b2w">
                    <?php  echo $this->render('index/_b2w', ['dataProvider' => $skyhubDataProvider ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>