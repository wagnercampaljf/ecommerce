<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProdutoCondicao */

$this->title = 'Update Produto Condicao: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Produto Condicaos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="produto-condicao-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
