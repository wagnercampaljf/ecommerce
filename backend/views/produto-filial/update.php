<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProdutoFilial */

$this->title = 'Atualizar Estoque: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Produto Filials', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Atualizar';
?>
<div class="produto-filial-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
        if (isset($erro)){
            echo $erro;
        }
        if (isset($mensagem)){
            echo $mensagem;
	}
        if (isset($link_mercado_livre)){
            echo $link_mercado_livre;
        }
	/*echo "<pre>"; print_r($erro); echo "</pre>";
	echo "<pre>"; print_r($mensagem); echo "</pre>";
	echo "<pre>"; print_r($link_mercado_livre); echo "</pre>";*/
	//print_r($erro);
        //print_r($mensagem);
        //print_r($link_mercado_livre);
    ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
