
<?php

    use backend\models\PedidoMercadoLivreSearch;
    use yii\widgets\ListView;
    use yii\helpers\Url;

    \yii\widgets\Pjax::begin(['timeout' => 5000]);

    
?>

<div class="container">
    <div class="row">
        <div class="col-sm-13">
            <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos-mercado-livre']) ?>">
                <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <input type="text"
                           name="filtro"
                           id="main-search-product"
                           class="form-control form-control-search input-lg data-hj-whitelist"
                           placeholder="Procure por dados do pedido ..."
                           value="<?= $filtro?>">
                    <span class="input-group-btn">
                    	<button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
                    </span>
                </div>
            </form>
        </div>
    </div><br>
    <div class="row">
    	<div class="col-sm-1">
            <button class="btn btn-link" style="background-color: #1b6d85; !important; color: white" type="button" data-toggle="collapse" data-target="#collapseBaixarPedido" aria-expanded="false" aria-controls="collapseBaixarPedido">
                <span class="glyphicon glyphicon-download-alt"></span>
            </button>
        </div>
    	<div class="collapse" id="collapseBaixarPedido">
            <div class="col-sm-5">
                <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos-mercado-livre/baixar-pedido-m-l']) ?>">
                    <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input type="text"
                               name="order"
                               id="main-search-product"
                               class="form-control form-control-search input-lg data-hj-whitelist"
                               placeholder="Buscar por cpf ..."
                               value="<?= $filtro?>">
                        <span class="input-group-btn">
                        	<button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-sm-5">
                <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos-mercado-livre/baixar-pedido-m-l']) ?>">
                    <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input type="text"
                               name="numPedido"
                               id="main-search-product"
                               class="form-control form-control-search input-lg data-hj-whitelist"
                               placeholder="Buscar pedido ..."
                               value="<?= $filtro?>">
                        <span class="input-group-btn">
                        	<button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
                        </span>
                    </div>
                </form>
            </div>
        </div>    
    </div>
</div>

    
<?php
    
    
    $searchModel = new PedidoMercadoLivreSearch();
    $dataProvider = $searchModel->search(['PedidoMercadoLivreSearch'=> ['pedido_meli_id' => $filtro]]);
    //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

   

    echo  ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => 'listaPedidoMercadoLivre',
    ]);



    \yii\widgets\Pjax::end();

?>


