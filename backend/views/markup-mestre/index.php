<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\MarkupMestreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Markup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="markup-mestre-index">

    <h1><?= Html::encode('Markup Mestre') ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Criar Markup Mestre', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'data_inicio',
            'observacao:ntext',
            'e_margem_absoluta_padrao:boolean',
            'descricao',
            'e_markup_padrao:boolean',

            ['class' => 'yii\grid\ActionColumn','template' => '{delete} {update} ']
        ],
    ]); ?>

</div>
