<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Produto;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ImagensSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Imagens';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="imagens-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Cadastrar Imagens', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    //    echo "<pre>";
    //    var_dump($dataProvider);die;
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            ['attribute' => 'produto_id',
                'contentOptions' => ['style' => 'width: 60px !important;']
            ],
            'produto.nome',
            [
                'attribute' => 'imagem',
                'value' => function ($model) {
                    return Html::a($model->getImg($model, [
                        'class' => 'img-responsive thumbnail',
                        'style' => 'width:100%'
                    ]));
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 150px !important;']
            ],
//            [
//                'attribute' => 'imagem',
//                'format' => 'html',
//                'value' => Html::img("data:image/jpeg;base64,imagem.imagem")
//            ],
//    'imagem.imagem',
            ['attribute' => 'ordem',
                'contentOptions' => ['style' => 'width: 20px !important;']
            ],

            ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width: 70px !important;']],
        ],
    ]); ?>

</div>
