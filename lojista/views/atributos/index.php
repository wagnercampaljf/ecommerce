<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Características');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="caracteristica-filial-index">

    <div class="portlet light">
        <div class="portlet-title">
            <div class="actions">
                <?= Html::a('Atualizar Características', ['create'], ['class' => 'btn btn-success']); ?>
            </div>
        </div>
        <div class="portlet-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'caracteristica.nome:text:Característica',
                    'filial.nome:text:Filial',
                    'observacao:ntext',
                ],
            ]); ?>
        </div>
    </div>

</div>
