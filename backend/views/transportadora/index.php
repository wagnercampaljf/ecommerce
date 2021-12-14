<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TransportadoraSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Transportadoras';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transportadora-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Criar Transportadora', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nome',
            'codigo',
            'filial_id',
            'codigo_omie',
            // 'razao_social',
            // 'email:email',
            // 'cnpj',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
