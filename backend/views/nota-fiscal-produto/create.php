<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\NotaFiscalProduto */

$this->title = 'Create Nota Fiscal Produto';
$this->params['breadcrumbs'][] = ['label' => 'Nota Fiscal Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nota-fiscal-produto-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
