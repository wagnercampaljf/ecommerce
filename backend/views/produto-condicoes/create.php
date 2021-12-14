<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProdutoCondicao */

$this->title = 'Create Produto Condicao';
$this->params['breadcrumbs'][] = ['label' => 'Produto Condicaos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-condicao-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
