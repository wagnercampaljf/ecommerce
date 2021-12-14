<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Transportadoras');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="filial-transportadora-index">
    <div class="portlet light">
        <div class="portlet-title">
            <div class="actions">
                <span>
                    <?= Html::a(Yii::t('app', 'Atualizar Transportadoras'), ['create'],
                        ['class' => 'btn btn-success']) ?>
                </span>
            </div>
        </div>
        <div class="portlet-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'transportadora.nome:text:Transportadora',
                    'dias_postagem',
                ],
            ]); ?>
        </div>
    </div>
</div>
