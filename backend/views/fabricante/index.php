<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\FabricanteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fabricantes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fabricante-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Criar Novo Fabricante', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nome',
            'sac',
            //'pagina_inicial:boolean',
            //'imagem',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
