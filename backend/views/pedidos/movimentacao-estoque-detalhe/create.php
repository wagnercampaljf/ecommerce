<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoEstoqueDetalhe */

$this->title = 'Create Movimentacao Estoque Detalhe';
$this->params['breadcrumbs'][] = ['label' => 'Movimentacao Estoque Detalhes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movimentacao-estoque-detalhe-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
