<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Comprador */

$this->title = 'Update Comprador: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Compradors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="comprador-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
