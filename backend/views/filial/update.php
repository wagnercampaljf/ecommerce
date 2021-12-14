<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Filial */

$this->title = 'Atualizar Filial: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Filials', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="filial-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
