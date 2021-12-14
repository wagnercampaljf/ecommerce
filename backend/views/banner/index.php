<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Banners');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="portlet light banner-index">
    <div class="portlet-title">
        <div class="actions">
            <span>
                <?= Html::a(Yii::t('app', 'Criar Banner'), ['create'], ['class' => 'btn btn-success']) ?>
            </span>
        </div>
    </div>
    <div class="portlet-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                //'id',
                'nome',
                'data_inicio',
                'data_fim',
                'cidade.nome:text:Cidade',
                'produto.nome:text:Produto',
                //'pdf',
                //'link',
                //'imagem',
                // 'qt_cliques',
                //'fabricante_id',
                // 'categoria_banner_id',
                // 'posicao_id',
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
