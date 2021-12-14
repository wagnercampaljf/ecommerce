<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoFinanceira */

$this->title = 'Create Movimentacao Financeira';
$this->params['breadcrumbs'][] = ['label' => 'Movimentacao Financeiras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movimentacao-financeira-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
