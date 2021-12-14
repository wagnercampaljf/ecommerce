
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
            <div class="col-sm-6">
                <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos-mercado-livre/baixar-pedido-m-l']) ?>">
                    <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input type="text"
                               name="order"
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


<form  id="subscribeNews"  action="<?= Url::to(['/pedidos-mercado-livre/' ]) ?>" >
    <button type="submit" name="filtro_status" value = "paid" class="btn btn-primary" >Pago</button>
    <button type="submit" name="filtro_status"  value = "cancelled" class="btn btn-danger">Cancelado</button>
    <button type="submit" name="filtro_status_envio" value = "delivered" class="btn btn-secondary" style="background-color: rgba(36,138,169,0.67)">Entregue</button>
    <button type="submit" name="filtro_status_envio"  value = "not_delivered" class="btn btn-dark" style="background-color: #2f323e; color: white">Não Entregue</button>


    <button type="submit" name="filtro_status_envio" value = "ready_to_ship" class="btn btn-success">Pronto para enviar</button>


    <button type="submit" name="filtro_status_envio"  value = "shipped" class="btn btn-info">Enviado</button>

</form>

    
<?php



$searchModel = new PedidoMercadoLivreSearch();


$dataProvider = $searchModel->search(['PedidoMercadoLivreSearch'=> ['pedido_meli_id' => $filtro]]);




$dataProvider = $searchModel->filtro_status(['PedidoMercadoLivreSearch'=> ['status' => $filtro_status]] );

if ($filtro_status== 'paid'){

    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedido Pago" ."</p><br>";

}elseif ($filtro_status== 'cancelled'){
    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedido Cancelado" ."</p><br>";

}



$dataProvider = $searchModel->filtro_status_envio(['PedidoMercadoLivreSearch'=> ['shipping_status' => $filtro_status_envio]] );

if ($filtro_status_envio== 'delivered'){

    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedido Entregue" ."</p><br>";

}elseif ($filtro_status_envio== 'not_delivered'){
    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedido Não Entregue" ."</p><br>";

}elseif ($filtro_status_envio== 'ready_to_ship'){
    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedido Pronto para enviar" ."</p><br>";

}elseif ($filtro_status_envio== 'shipped'){
    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedido Enviado" ."</p><br>";

}



//$dataProvider = $searchModel->search(Yii::$app->request->queryParams);


echo  ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => 'listaPedidoMercadoLivre',
    ]);



    \yii\widgets\Pjax::end();

?>


