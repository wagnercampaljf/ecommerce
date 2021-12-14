<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Administrador */

$this->title = 'Update Administrador: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Administradors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="administrador-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
