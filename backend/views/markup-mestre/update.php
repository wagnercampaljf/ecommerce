<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\common\models\MarkupMestre */

$this->title = 'Atualizar Markup Mestre';
$this->params['breadcrumbs'][] = ['label' => 'Markup Mestres', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="markup-mestre-update">

    <h1><?= Html::encode('Markup: '.$model->descricao) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'searchModelDetalhe' => $searchModelDetalhe,
        'dataProviderDetalhe' => $dataProviderDetalhe,
    ]) ?>
</div>
