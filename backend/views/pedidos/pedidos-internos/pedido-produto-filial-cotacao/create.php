<?php

use yii\helpers\Html;
use common\models\ProdutoFilial;
use common\models\PedidoMercadoLivreProduto;


/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivreProdutoProdutoFilial */

$this->title = 'Criar Pedido Produto Filial Cotação';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-pedido-produto-filial-cotação-create">

	<h1><b>Cadastrar produto no pedido</b></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>