<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OperacaoFinanceira */

$this->title = 'Update Operacao Financeira: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Operacao Financeiras', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="operacao-financeira-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
