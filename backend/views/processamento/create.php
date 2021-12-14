<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Processamento */

$this->title = 'Create Processamento';
$this->params['breadcrumbs'][] = ['label' => 'Processamentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="processamento-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
