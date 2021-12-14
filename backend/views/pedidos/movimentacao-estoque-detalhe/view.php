<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoEstoqueDetalhe */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Movimentacao Estoque Detalhes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movimentacao-estoque-detalhe-view">

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
            'descricao:ntext',
            'produto_id',
            'salvo_em',
            'salvo_por',
            'quantidade',
            'id_ajuste_omie_entrada',
            'id_ajuste_omie_saida',
            'movimentacao_estoque_mestre_id',
        ],
    ]) ?>

</div>
