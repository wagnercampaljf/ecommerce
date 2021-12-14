<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProdutoFilialVariacaoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Produto Filial Variacaos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-filial-variacao-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Produto Filial Variacao', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'produto_filial_id',
            'variacao_id',
            'meli_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
