<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoEstoqueMestre */

$this->title = 'Atualizar Movimentacao de Estoque Mestre: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Movimentacao Estoque Mestres', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="movimentacao-estoque-mestre-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
	     
        'filterModelDetalhe' => $searchModelDetalhe,
        'dataProviderDetalhe' => $dataProviderDetalhe,

    ]) ?>

</div>
