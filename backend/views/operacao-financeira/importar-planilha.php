<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OperacaoFinanceira */

$this->title = 'Create Operacao Financeira';
$this->params['breadcrumbs'][] = ['label' => 'Operacao Financeiras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operacao-financeira-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_importar_arquivo', [
        'model' => $model,
    ]) ?>

</div>
