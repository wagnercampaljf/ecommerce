<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lojistas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lojista-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lojista', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'razao',
            'dt_criacao',
            'imagem',
            'documento',
            // 'juridica:boolean',
            // 'aprovado:boolean',
            // 'motivo_veredito',
            // 'nome',
            // 'pagina_inicial:boolean',
            // 'ativo:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
