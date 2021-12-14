<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PisCofinsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pis Cofins';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pis-cofins-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Criar Pis Cofins', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'pis_cofins',
            'ncm',
            'data_registro',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
