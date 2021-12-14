<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Administrador;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MovimentacaoEstoqueMestreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Movimentacao de Estoque Mestres';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movimentacao-estoque-mestre-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Criar Movimentacao de Estoque Mestre', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id' ,
            'descricao:ntext',
            'e_autorizado:boolean',
	    'e_remessa_recebida:boolean',
            'autorizado_por',
            'salvo_em',
            //'salvo_por',           
            [

                'header' => 'Salvo Por',
                'content' => function ($dataProvider) {

                    return $dataProvider->salvo_por ? Administrador::findOne($dataProvider->salvo_por)->nome : '';
                },
            ],
            // 'filial_origem_id',
            // 'filial_destino_id',
            // 'codigo_remessa_omie',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
