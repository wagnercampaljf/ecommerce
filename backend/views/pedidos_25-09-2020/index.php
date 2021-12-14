<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\PedidoMercadoLivreSearch;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $skyhubDataProvider yii\data\ActiveDataProvider */
/* @var $filterModel \common\models\PedidoSearch */
/* @var $skyhubFilterModel \common\models\PedidoSkyhubSearch */

$this->title = 'Pedidos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-index">

    <div class="container">
        <h2>Dynamic Tabs</h2>
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home">Peça Agora</a></li>
            <li><a data-toggle="tab" href="#menu1">Mercado Livre(Principal)</a></li>
            <li><a data-toggle="tab" href="#menu2">B2W</a></li>
        </ul>

        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
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
            <div id="menu1" class="tab-pane fade">
                <?php  echo $this->render('index/_mercado-livre-principal', [ ]) ?>            </div>
            <div id="menu2" class="tab-pane fade">
                <?php  echo $this->render('index/_b2w', ['dataProvider' => $skyhubDataProvider ]) ?>
            </div>
        </div>
    </div>




</div>
