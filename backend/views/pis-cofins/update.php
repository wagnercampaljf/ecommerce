<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PisCofins */

$this->title = 'Update Pis Cofins: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pis Cofins', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="pis-cofins-update">

            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>