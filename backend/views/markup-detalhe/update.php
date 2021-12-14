<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\common\models\MarkupDetalhe */

$this->title = 'Atualizar Markup Detalhe: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Markup Detalhes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="markup-detalhe-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
