<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Comprador */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Compradors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comprador-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nome',
            'empresa_id',
            'cpf',
            'username',
            'password',
            'dt_criacao',
            'ativo:boolean',
            'dt_ultima_mudanca_senha',
            'email:email',
            'cargo',
            'nivel_acesso_id',
            'auth_key',
            'password_reset_token',
            'id_tipo_empresa',
        ],
    ]) ?>

</div>
