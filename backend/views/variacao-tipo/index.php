<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VariacaoTipoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Variacao Tipos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="variacao-tipo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Variacao Tipo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'descricao',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
