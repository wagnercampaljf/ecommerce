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
            <p>Você já está autorizado no Mercado Livre.</p>

            <p>Seu código de acesso é: <?= $accessToken ?></p>
        </div>
    </div>
</div>
