<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProdutoFilialVariacao */

$this->title = 'Create Produto Filial Variacao';
$this->params['breadcrumbs'][] = ['label' => 'Produto Filial Variacaos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-filial-variacao-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
