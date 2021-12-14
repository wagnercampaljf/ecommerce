<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Transportadora */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Transportadoras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transportadora-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Apagar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Você realmente deseja apagar esse item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nome',
            'codigo',
            'filial_id',
            'codigo_omie',
            'razao_social',
            'email:email',
            'cnpj',
        ],
    ]) ?>

</div>
