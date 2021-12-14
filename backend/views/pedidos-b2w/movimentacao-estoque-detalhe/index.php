<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MovimentacaoEstoqueDetalheSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Movimentacao Estoque Detalhes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movimentacao-estoque-detalhe-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Criar Movimentacao de Estoque Detalhe', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'descricao:ntext',
            'produto_id',
            'salvo_em',
            'salvo_por',
            // 'quantidade',
            // 'id_ajuste_omie_entrada',
            // 'id_ajuste_omie_saida',
            // 'movimentacao_estoque_mestre_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
