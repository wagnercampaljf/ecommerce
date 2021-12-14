<?php

use common\models\ValorProdutoMenorMaior;
use yii\helpers\Html;
?>

<div class="row separador_carrinho">
	<div class="col-lg-2 text-center">
		<div class="produto-search-img text-center margin-bottom-10">
			<a href="<?= $model->produto->getUrl() ?>">
				<?php
				$alt = $model->produto->getLabel();
				echo $model->produto->getImage(['class' => "text-left''", "height" => "auto", 'width' => '70', 'alt' => $alt, 'title' => $alt, 'itemprop' => 'image']);
				$ValorProdutoMenorMaior   = ValorProdutoMenorMaior::find()->andWhere(['=', 'produto_id', $model->produto->id])->one();
				?>
			</a>
		</div>
	</div>
	<div class="col-lg-5 text-center">
		<?= $model->produto->nome ?>
	</div>
	<div class="col-lg-1 text-center">
		<small>
			<small>
				<del><?= (($model->produto->id) % 2 == 1) ? number_format(($ValorProdutoMenorMaior['menor_valor'] / 0.82), 2, ',', '') : number_format(($ValorProdutoMenorMaior['menor_valor'] / 0.89), 2, ',', '') ?></del>
			</small>
		</small>
		<br />
		<meta itemprop="priceCurrency" content="BRL" />
		</span>
	</div>
	<div class="col-lg-2 text-center">
		<form class="form-inline">
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-addon hidden-print"><i style="cursor:pointer;" onclick="return soma('#quantidade-field-<?= $model->id ?>', -1, 1)" class="fa fa-minus"></i></div>
					<input type="text" data-id="<?= $model->id ?>" class="form-control quantidade-field text-center" id="quantidade-field-<?= $model->id ?>" value=" <?= Yii::$app->session['carrinho'][$model->id] ?> ">
					<div class="input-group-addon hidden-print"><i style="cursor:pointer;" onclick="return soma('#quantidade-field-<?= $model->id ?>', 1, 1)" class="fa fa-plus"></i></div>
				</div>
			</div>
		</form>
	</div>
	<div class="col-lg-1 text-center">
		<?php
		// $valorTotal = 0;
		// $juridica = Yii::$app->params['isJuridica']();
		// $valor = $model->getValorProdutoFilials()->ativo()->one()->getValorFinal($juridica) * Yii::$app->session['carrinho'][$model->id];
		// $valorTotal += $valor;
		?>

		<span data-id="<?= $model->id ?>" id="total_produto_<?= $model->id ?>"><?= $ValorProdutoMenorMaior['menor_valor'] ?></span>
	</div>
	<div class="col-lg-1 text-center">
		<div class="visible-xs"><br></div>
		<?= Html::a('<span class="glyphicon glyphicon-trash" onclick="removerProduto(' . $model->id . ')" style="padding-right: 5px; cursor:pointer"></span>', null, ['title' => Yii::t('yii', 'Delete'),]) ?>
	</div>
	<br>
</div>