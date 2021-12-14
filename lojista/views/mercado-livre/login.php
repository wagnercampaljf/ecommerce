<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Conta Mercado Livre');
?>
<div class="filial-transportadora-index">
    <div class="portlet light">
        <div class="portlet-title">

        </div>
        <div class="portlet-body">
            <span>
                <p>Você já possui uma conta no Mercado Livre?</p>
                <p>Faça agora a integração do Mercado Livre com o PeçaAgora!</p>
                <?= Html::a(Yii::t('app', 'Vamos lá!'), ['login'],
                    ['class' => 'btn btn-success']) ?>
            </span>
        </div>
    </div>
</div>
