<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ValorProdutoFilial */

$this->title = 'Create Valor Produto Filial';
$this->params['breadcrumbs'][] = ['label' => 'Valor Produto Filials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if (isset($mensagem)) {
    echo '<div class="text-primary" style="font-size: 20px; color: #1E90FF">' . strtoupper($mensagem) . '</div>';
}

?>
<div class="valor-produto-filial-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>