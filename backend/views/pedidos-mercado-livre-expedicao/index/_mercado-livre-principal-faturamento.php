
<?php

    use backend\models\PedidoMercadoLivreSearch;
    use yii\widgets\ListView;
    use yii\helpers\Url;

    \yii\widgets\Pjax::begin(['timeout' => 5000]);

    
?>

	<div class="container">
    	<form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos-mercado-livre-expedicao/faturamento']) ?>">
			<div class="row">
                <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <input type="text" 
                    	   name="filtro"
                           id="main-search-product" 
                           class="form-control form-control-search input-lg data-hj-whitelist"
                           placeholder="Procure por dados do pedido ..."
                           value="<?= $filtro?>"
                    >
                    <span class="input-group-btn">
                    	<button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
                    </span>
                </div>
            </div>
            <div class="row">
		<div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
    		    <div class="form-check">
        	        <input type="checkbox" class="form-check-input" id="e_mercado_livre_principal" name="e_mercado_livre_principal" <?= ($e_mercado_livre_principal) ? "checked" : "" ?>>
    	          	<label class="form-check-label" for="e_mercado_livre_principal">Mercado Livre Principal</label>
                    </div>
                    <div class="form-check">
        	        <input type="checkbox" class="form-check-input" id="e_mercado_livre_filial" name="e_mercado_livre_filial" <?= ($e_mercado_livre_filial) ? "checked" : "" ?>>
    	                <label class="form-check-label" for="e_mercado_livre_filial">Mercado Livre Filial</label>
                    </div>
		    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="e_mercado_livre_mg4" name="e_mercado_livre_mg4" <?= ($e_mercado_livre_mg4) ? "checked" : "" ?>>
                        <label class="form-check-label" for="e_mercado_livre_mg4">Mercado Livre MG4</label>
                    </div>
                </div>
			</div>
    	</form>
    </div>
    
<?php
    
    
    $searchModel = new PedidoMercadoLivreSearch();
    $dataProvider = $searchModel->search(['PedidoMercadoLivreSearch'=> [
	'pedido_meli_id' => $filtro, 
	'e_xml_subido' => false, 
	"e_pedido_autorizado" => true, 
	"e_pedido_cancelado" => false, 
	"filtro_status_pedido_enviado" => false, 
	"filtro_status_pedido_nao_enviado" => true, 
	"e_mercado_livre_principal" => $e_mercado_livre_principal, 
	"e_mercado_livre_filial" => $e_mercado_livre_filial, 
	"e_mercado_livre_mg4" => $e_mercado_livre_mg4,
	"e_pedido_mercado_envios" => true]]);
    //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

   

    echo  ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => 'listaPedidoMercadoLivreFaturamento',
    ]);



    \yii\widgets\Pjax::end();

?>


