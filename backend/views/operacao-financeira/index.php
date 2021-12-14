<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OperacaoFinanceiraSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Operacao Financeiras';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operacao-financeira-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Operacao Financeira', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'numero',
            'filial_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
