<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoEstoqueMestre */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Movimentacao Estoque Mestres', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movimentacao-estoque-mestre-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Apagar', ['delete', 'id' => $model->id], [
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
            'descricao:ntext',
            'e_autorizado:boolean',
            'autorizado_por',
            'salvo_em',
            'salvo_por',
            'filial_origem_id',
            'filial_destino_id',
            'codigo_remessa_omie',
        ],
    ]) ?>

</div>
