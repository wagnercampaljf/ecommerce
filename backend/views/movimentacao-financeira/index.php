<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MovimentacaoFinanceiraSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Movimentacao Financeiras';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movimentacao-financeira-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Movimentacao Financeira', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'numero',
            'cliente_cpf_cnpj',
            'cliente_nome',
            'data_hora',
            // 'valor',
            // 'valor_total',
            // 'operacao_financeira_id',
            // 'movimentacao_financeira_tipo_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
