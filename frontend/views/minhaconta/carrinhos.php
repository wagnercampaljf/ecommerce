<?php
/* @var $this yii\web\View */
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Meus Carrinhos';
$this->params['active'] = 'carrinhos';
$this->params['breadcrumbs'][] = ['label' => 'Minha Conta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tab-pane active col-md-9 col-sm-12" id="carrinhos">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'chave',
                    'dt_criacao',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}{delete}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-search" style="padding-right: 5px"></span>',
                                    Url::to(['minhaconta/carrinho', 'id' => $model->id]), [
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
