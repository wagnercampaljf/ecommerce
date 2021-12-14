<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VariacaoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Variacaos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="variacao-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Variacao', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'descricao',
            'variacao_tipo_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
