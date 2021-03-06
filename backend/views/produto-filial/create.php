<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProdutoFilial */

$this->title = 'Criar Estoque';
$this->params['breadcrumbs'][] = ['label' => 'Produto Filials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-filial-create">

    <?php if (isset($error)) {
        echo  '<div class="text-primary h4" style="font-size: 20px; color: #1E90FF">' . $error . '</div>';
    }
    ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>