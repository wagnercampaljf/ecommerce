<?php

/* @var $this yii\web\View */
/* @var $model common\models\Banner */

$this->title = 'Editar Banner: ' . ' ' . $model->nome;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nome, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Editar');
?>
<div class="banner-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
