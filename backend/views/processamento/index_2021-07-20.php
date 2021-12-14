<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProcessamentoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Processamentos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="processamento-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Criar Processamento', ['criar-processamento'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'funcao_id',
            'data_hora_inicial',
            'data_hora_final',
            'observacao:ntext',
             'status:ntext',
            'coluna_codigo_fabricante',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
