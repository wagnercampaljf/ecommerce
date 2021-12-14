<?php
/* @var $this yii\web\View */
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Meus Pedidos';
$this->params['active'] = 'pedidos';
$this->params['breadcrumbs'][] = ['label' => 'Minha Conta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tab-pane active col-md-9 col-sm-12" id="pedidos">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'id' => ['label' => '#', 'value' => 'id'],

                    [
                        'attribute' => 'Forma Pagamento',
                        'value' => 'formaPagamento.nome',
                    ],
                    [
                        'attribute' => 'Data',
                        'value' => function ($data) {
                            return Yii::$app->formatter->asDate($data['dt_referencia']);
                        },
                    ],
                    [
                        'format' => 'text',
                        'header' => 'Status',
                        'value' => function ($data) {
                            return $data['statusAtual']['tipoStatus']['nome'];
                        },
                    ],
                    [
                        'attribute' => 'Valor',
                        'value' => function ($data) {
                            return Yii::$app->formatter->asCurrency($data['valor_total']);
                        },
                    ],
                    [
                        'attribute' => 'Valor Frete',
                        'value' => function ($data) {
                            return Yii::$app->formatter->asCurrency($data['valor_frete']);
                        },
                    ],
                    [
                        'attribute' => 'Valor Total',
                        'value' => function ($data) {
                            return Yii::$app->formatter->asCurrency($data['valor_total'] + $data['valor_frete']);
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-search"></span>',
                                    Url::to(['/minhaconta/pedido?id=' . $model->id]), [
                                        'title' => Yii::t('yii', 'View'),
                                    ]);
                            }
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
