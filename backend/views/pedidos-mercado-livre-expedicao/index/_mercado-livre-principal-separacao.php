
<?php

    use backend\models\PedidoMercadoLivreSearch;
    use yii\widgets\ListView;
    use yii\helpers\Url;

    \yii\widgets\Pjax::begin(['timeout' => 5000]);

    
?>

	<div class="container">
    	<form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos-mercado-livre-expedicao/separacao']) ?>">
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
        	            <input type="checkbox" class="form-check-input" id="e_apenas_nao_impressos" name="e_apenas_nao_impressos" <?= ($e_apenas_nao_impressos) ? "checked" : "" ?>>
    	                <label class="form-check-label" for="e_mercado_livre_principal">Exibir apenas Não Impressos</label>
                    </div>
                </div>
	    </div>
    	</form>
    </div>
    
<?php
    
    
    $searchModel = new PedidoMercadoLivreSearch();
    $dataProvider = $searchModel->search(['PedidoMercadoLivreSearch'=> [
        'pedido_meli_id' => $filtro, 
        "e_pedido_autorizado" => true, 
        "e_pedido_cancelado" => false, 
        "e_pedido_enviado" => false, 
	"e_mail" => $e_mail,
	"e_apenas_nao_impressos" => $e_apenas_nao_impressos
    ]]);
    //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $dataProvider->pagination = ['pageSize' => 6];

    echo  ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => 'listaPedidoMercadoLivreSeparacao',
    ]);



    \yii\widgets\Pjax::end();

?>


