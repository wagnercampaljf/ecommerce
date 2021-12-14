<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Transportadora */

$this->title = 'Create Transportadora';
$this->params['breadcrumbs'][] = ['label' => 'Transportadoras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transportadora-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
