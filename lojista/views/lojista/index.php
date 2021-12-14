<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Contrato Correios');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="filial-transportadora-index">
    <div class="portlet light">
        <div class="portlet-title">
            <div class="actions">
                <span>
                    <?= Html::a(Yii::t('app', 'Atualizar Contrato'), ['update'],
                        ['class' => 'btn btn-success']) ?>
                </span>
            </div>
        </div>
        <div class="portlet-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'contrato_correios:text:Contrato Correios',
                ],
            ]); ?>
        </div>
    </div>
</div>
