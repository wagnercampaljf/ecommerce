<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Produto */

$this->title = 'Editar Produto: ' . ' ' . $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nome, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="produto-update">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php 
        /*if (isset($erro))
        {
            echo $erro;
        } 
        elseif (isset($mensagem))
        {
            echo $mensagem;
            if (isset($link_mercado_livre)){
                echo $link_mercado_livre;
            }
        }*/
    
	    if (isset($erro)){
	            echo $erro;
	        }
	        if (isset($mensagem)){
	            echo $mensagem;
	        }
	        if (isset($link_mercado_livre)){
	            echo $link_mercado_livre;
	        }
	?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
