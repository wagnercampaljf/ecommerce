<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Variacao */

$this->title = 'Create Variacao';
$this->params['breadcrumbs'][] = ['label' => 'Variacaos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="variacao-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
