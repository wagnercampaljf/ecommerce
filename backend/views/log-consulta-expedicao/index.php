<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LogConsultaExpedicaoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Log Consulta Expedicaos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-consulta-expedicao-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Log Consulta Expedicao', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'descricao',
            'salvo_em',
            'salvo_por',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
