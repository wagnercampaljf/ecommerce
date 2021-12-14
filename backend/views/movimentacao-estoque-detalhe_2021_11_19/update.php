<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoEstoqueDetalhe */

$this->title = 'Atualizar Movimentacao de  Estoque Detalhe: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Movimentacao Estoque Detalhes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="movimentacao-estoque-detalhe-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
