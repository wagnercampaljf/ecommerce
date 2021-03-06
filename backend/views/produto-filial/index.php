<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProdutoFilialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Estoque';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-filial-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Criar Estoque', ['create'], ['class' => 'btn btn-success']) ?>        
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
	    [
                'label'     => 'Origem',
                'attribute' => 'produto_filial_origem_id',
                'value'     => 'produto_filial_origem_id',
            ],
            [
                'label'     => 'Filial',
                'attribute' => 'filial_nome',
                'value'     => 'filial.nome',
            ],
            [
                'label'     => 'Produto',
                'attribute' => 'produto_nome',
                'value'     => 'produto.nome'
            ],
            [
                'label'     => 'Código Global',
                'attribute' => 'codigo_global',
                'value'     => 'produto.codigo_global',

            ],
            'quantidade',
            'estoque_minimo',
            'meli_id',
            'meli_id_sem_juros',
	        'meli_id_full',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
