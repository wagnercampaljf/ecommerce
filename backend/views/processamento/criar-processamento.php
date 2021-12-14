<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Processamento */

$this->title = 'Criar Processamento';
$this->params['breadcrumbs'][] = ['label' => 'Processamentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="processamento-create">

    <?= $this->render('_form-criar-processamento', [
        'model' => $model,
    ]) ?>

</div>
