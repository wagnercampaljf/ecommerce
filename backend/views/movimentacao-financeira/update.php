<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoFinanceira */

$this->title = 'Update Movimentacao Financeira: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Movimentacao Financeiras', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="movimentacao-financeira-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
