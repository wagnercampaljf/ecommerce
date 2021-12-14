<?php

use yii\helpers\Html;
use common\models\ProdutoFilial;
use common\models\PedidoMercadoLivreProduto;


/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivreProdutoProdutoFilial */

$this->title = 'Create Pedido Mercado Livre Produto Produto Filial';
$this->params['breadcrumbs'][] = ['label' => 'Pedido Mercado Livre Produto Produto Filials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-mercado-livre-produto-produto-filial-create">

	<h1><b>Cadastrar produto no pedido</b></h1>
	
	<?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
