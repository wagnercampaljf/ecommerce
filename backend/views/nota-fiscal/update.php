<?php

use backend\models\NotaFiscal;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\NotaFiscal */

$this->title = 'Update Nota Fiscal: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Nota Fiscals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="nota-fiscal-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>