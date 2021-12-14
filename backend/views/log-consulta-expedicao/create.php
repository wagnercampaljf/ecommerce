<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\common\models\LogConsultaExpedicao */

$this->title = 'Create Log Consulta Expedicao';
$this->params['breadcrumbs'][] = ['label' => 'Log Consulta Expedicaos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-consulta-expedicao-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
