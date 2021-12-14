<?php

use common\models\Usuario;
use kartik\date\DatePicker;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel \lojista\models\PedidoSearch */
/* @var $skyhubSearchModel \common\models\PedidoSkyhubSearch */
/* @var $skyhubDataProvider yii\data\ActiveDataProvider */

$this->title = 'Pedidos';
/* @var $usuario Usuario */
$usuario = Yii::$app->user->getIdentity();
$filial = $usuario->filial;
?>
<div class="pedido-index row">
    <div class="portlet light">
        <div class="portlet-title">
            <div class="actions">
                <span>
                    <?= Html::a(Yii::t('app', 'Integrar com B2W'), ['lojista/integrar-b2w'],
                        ['class' => 'btn btn-success', 'disabled' => $usuario->filial->integrar_b2w ? true : false]) ?>
                </span>
            </div>
        </div>
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
                        'filterModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'columns' => [
//            'id:integer:Nº do Pedido',
                            [
                                'attribute' => 'id',
                                'label' => 'Nº Pedido',
                                'value' => 'id',

                            ],
                            [
                                'attribute' => 'Comprador',
                                'label' => 'Nome do Comprador',
                                'value' => 'comprador.empresa.nome',
                            ],
                            [
                                'attribute' => 'documento',
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return $data->comprador->empresa->getDocumentoLabel();
                                },
                            ],
                            [
                                'attribute' => 'status',
                                'label' => 'Status',
                                'value' => 'statusAtual.tipoStatus.nome'
                            ],
                            [
                                'label' => 'Status Correios',
                                'value' => 'statusCorreios'
                            ],
                            [
                                'attribute' => 'dt_referencia',
                                'format' => 'date',
                                'filter' => DatePicker::widget([
                                    'name' => Html::getInputName($searchModel, 'dt_referencia'),
                                    'type' => DatePicker::TYPE_INPUT,
                                    'value' => $searchModel->dt_referencia,
                                    'pluginOptions' => [
                                        'format' => 'dd/mm/yyyy',
                                        'autoclose' => true,
                                        'clearBtn' => true
                                    ]
                                ])
                            ],
                            [
                                'attribute' => 'data_prevista',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $now = date('Y-m-d');
                                    $color = ($model->data_prevista < $now) ? "red" : "green";

                                    return Html::a(' <font color=' . $color . '>' . Yii::$app->formatter->asDate($model->data_prevista) . '</font> ');
                                },
                                'filter' => DatePicker::widget([
                                    'name' => Html::getInputName($searchModel, 'data_prevista'),
                                    'type' => DatePicker::TYPE_INPUT,
                                    'value' => $searchModel->data_prevista,
                                    'pluginOptions' => [
                                        'format' => 'dd/mm/yyyy',
                                        'autoclose' => true,
                                        'clearBtn' => true
                                    ]
                                ])
                            ],
                            'valor_frete:currency:Valor Frete(R$)',
                            [
                                'attribute' => 'valor_total',
                                'label' => 'Valor Produto(R$)',
                                'format' => 'currency',
                            ],
                            [
                                'attribute' => 'Valor Total(R$)',
                                'format' => 'currency',
                                'value' => function ($model) {
                                    return $model->valor_total + $model->valor_frete;
                                },
                            ],
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
                    <?= $this->render('index/_b2w', [
                        'dataProvider' => $skyhubDataProvider,
                        'filterModel' => $skyhubSearchModel
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>