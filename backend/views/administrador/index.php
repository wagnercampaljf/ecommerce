<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AdministradorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Administradors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="administrador-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Administrador', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            'nome',
            'cpf',
            'email:email',
            // 'password',
            // 'dt_criacao',
            // 'ativo:boolean',
            // 'dt_ultima_mudanca_senha',
            // 'auth_key',
            // 'password_reset_token',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
