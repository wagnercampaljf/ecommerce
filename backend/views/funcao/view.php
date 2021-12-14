<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Funcao */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Funcaos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="funcao-view">

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
            'funcao_nome',
            'caminho',
        ],
    ]) ?>

</div>
