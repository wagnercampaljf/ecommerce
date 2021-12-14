<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sub Categorias';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-categoria-index">

    <p>
        <?= Html::a('Criar Sub Categoria', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'nome',
            'id',
            'categoria_id',
            'ativo:boolean',
            // 'slug',
            'meli_id',
            'meli_cat_nome',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
