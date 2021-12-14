<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Processamento */

$this->title = 'Update Processamento: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Processamentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="processamento-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
