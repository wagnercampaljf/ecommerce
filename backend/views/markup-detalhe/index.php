<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\MarkupDetalheSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Markup Detalhes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="markup-detalhe-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Markup Detalhe', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'markup_mestre_id',
            'e_margem_absoluta:boolean',
            'valor_minimo',
            'valor_maximo',
            // 'margem',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
