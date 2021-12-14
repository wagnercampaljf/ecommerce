<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Imagens */

$this->title = 'Atualizar Imagens: ' . ' ' . $model->produto;
$this->params['breadcrumbs'][] = ['label' => 'Imagens', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->produto, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Atualizar';
?>
<div class="imagens-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
        if (isset($erro))
        {
            echo $erro;
        }
        elseif (isset($mensagem))
        {
            echo $mensagem;
            if (isset($link_mercado_livre)){
                echo $link_mercado_livre;
            }
        }
    ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
