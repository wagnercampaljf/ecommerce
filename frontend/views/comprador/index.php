<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Compradors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comprador-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Comprador', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nome',
            'empresa_id',
            'cpf',
            'username',
            // 'password',
            // 'dt_criacao',
            // 'ativo:boolean',
            // 'dt_ultima_mudanca_senha',
            // 'email:email',
            // 'cargo',
            // 'nivel_acesso_id',
            // 'auth_key',
            // 'password_reset_token',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
