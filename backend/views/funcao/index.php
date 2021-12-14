<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\FuncaoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Funções';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="funcao-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Criar Função', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nome',
            'funcao_nome',
            'caminho',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
