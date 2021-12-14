
<?php

use common\models\PedidoSearch;
use yii\widgets\ListView;
use yii\helpers\Url;

\yii\widgets\Pjax::begin(['timeout' => 5000]);


?>

<div class="container">
    <div class="row">
        <div class="col-sm-13">
            <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos']) ?>">
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
</div>

<form  id="subscribeNews"  action="<?= Url::to(['/pedidos/' ]) ?>" >
    <button type="submit" name="" value = "" class="btn-outline default"><span>Limpar filtro</span></button>
    <button type="submit" name="filtro_status" value = "Aberto" class="btn btn-primary" >Aberto</button>
    <button type="submit" name="filtro_status"  value = "Confirmado" class="btn btn-success">Confirmado</button>
    <button type="submit" name="filtro_status" value = "Enviado" class="btn btn-secondary" style="background-color: rgba(36,138,169,0.67)">Enviado</button>
    <button type="submit" name="filtro_status"  value = "Concluído" class="btn btn-dark" style="background-color: #2f323e; color: white">Concluído</button>
    <button type="submit" name="filtro_status" value = "Cancelado" class="btn btn-danger">Cancelado</button>
    <button type="submit" name="filtro_status"  value = "Aguardando Pagamento" class="btn btn-info">Aguardando Pagamento</button>
    <button type="submit" name="filtro_status" value = "Devolvido" class="btn btn-primary" style="background-color: rgba(255,57,17,0.78)">Devolvido</button>
</form>

<style>
    @import url("https://fonts.googleapis.com/css?family=Montserrat:400,400i,700");
    .btn-outline {
        font-weight: bold;
        background-color: #ffffff;
        border-style: solid;
        border-width: 3px;
        padding: 0;
        outline: 0;
        display: inline-flex;
        overflow: hidden;
        cursor: pointer;
        transition: all 300ms ease-in-out;
    }
    .btn-outline i,
    .btn-outline span {
        width: 100%;
        display: block;
        padding: 0.7rem;
        margin: 0;
        box-sizing: border-box;
        flex-shrink: 0;
    }
    .btn-outline i {
        margin-left: -100%;
        transition: margin-left 300ms ease-in-out;
    }
    .btn-outline:hover {
        color: #ffffff;
        background-color: #404040;
    }
    .btn-outline:hover i {
        margin-left: 0;
    }
    .btn-outline.default {
        color: #404040;
        border-color: #404040;
    }
    .btn-outline.default:hover {
        color: #ffffff;
        background-color: #404040;
    }
    .btn-outline.default:focus {
        box-shadow: 0 0 0 0.3rem rgba(64, 64, 64, 0.7);
    }
    .btn-outline.default.btn-outline-small:focus {
        box-shadow: 0 0 0 0.2rem rgba(64, 64, 64, 0.7);
    }
    .btn-outline.default.btn-outline-large:focus {
        box-shadow: 0 0 0 0.4rem rgba(64, 64, 64, 0.7);
    }
    .btn-outline.primary {
        color: #1489ff;
        border-color: #1489ff;
    }
    .btn-outline.primary:hover {
        color: #ffffff;
        background-color: #1489ff;
    }
    .btn-outline.primary:focus {
        box-shadow: 0 0 0 0.3rem rgba(20, 137, 255, 0.7);
    }
    .btn-outline.primary.btn-outline-small:focus {
        box-shadow: 0 0 0 0.2rem rgba(20, 137, 255, 0.7);
    }
    .btn-outline.primary.btn-outline-large:focus {
        box-shadow: 0 0 0 0.4rem rgba(20, 137, 255, 0.7);
    }
    .btn-outline.success {
        color: #00ce0d;
        border-color: #00ce0d;
    }
    .btn-outline.success:hover {
        color: #ffffff;
        background-color: #00ce0d;
    }
    .btn-outline.success:focus {
        box-shadow: 0 0 0 0.3rem rgba(0, 206, 13, 0.7);
    }
    .btn-outline.success.btn-outline-small:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 206, 13, 0.7);
    }
    .btn-outline.success.btn-outline-large:focus {
        box-shadow: 0 0 0 0.4rem rgba(0, 206, 13, 0.7);
    }
    .btn-outline.warning {
        color: #f1da19;
        border-color: #f1da19;
    }
    .btn-outline.warning:hover {
        color: #ffffff;
        background-color: #f1da19;
    }
    .btn-outline.warning:focus {
        box-shadow: 0 0 0 0.3rem rgba(241, 218, 25, 0.7);
    }
    .btn-outline.warning.btn-outline-small:focus {
        box-shadow: 0 0 0 0.2rem rgba(241, 218, 25, 0.7);
    }
    .btn-outline.warning.btn-outline-large:focus {
        box-shadow: 0 0 0 0.4rem rgba(241, 218, 25, 0.7);
    }
    .btn-outline.danger {
        color: #ff2323;
        border-color: #ff2323;
    }
    .btn-outline.danger:hover {
        color: #ffffff;
        background-color: #ff2323;
    }
    .btn-outline.danger:focus {
        box-shadow: 0 0 0 0.3rem rgba(255, 35, 35, 0.7);
    }
    .btn-outline.danger.btn-outline-small:focus {
        box-shadow: 0 0 0 0.2rem rgba(255, 35, 35, 0.7);
    }
    .btn-outline.danger.btn-outline-large:focus {
        box-shadow: 0 0 0 0.4rem rgba(255, 35, 35, 0.7);
    }
    .btn-outline.btn-outline-small {
        font-size: 0.8rem;
        border-width: 2px;
    }
    .btn-outline.btn-outline-small i,
    .btn-outline.btn-outline-small span {
        padding: 0.5rem;
    }
    .btn-outline.btn-outline-large {
        font-size: 1.5rem;
        border-width: 4px;
    }
    .btn-outline.btn-outline-large i,
    .btn-outline.btn-outline-large span {
        padding: 0.9rem;
    }

</style>

<?php

$searchModel = new PedidoSearch();

$dataProvider = $searchModel->search(['PedidoSearch'=> ['id' => $filtro]]);


$dataProvider = $searchModel->filtro_status(['PedidoSearch'=> ['status' => $filtro_status]] );

if ($filtro_status== 'Aberto'){

    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedidos em Aberto" ."</p><br>";

}elseif ($filtro_status== 'Confirmado'){
    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedidos Confirmados" ."</p><br>";

}elseif ($filtro_status== 'Enviado'){

    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedidos Enviados" ."</p><br>";

}elseif ($filtro_status== 'Concluído'){

    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedidos Concluídos" ."</p><br>";

}elseif ($filtro_status== 'Cancelado'){

    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedidos Cancelados" ."</p><br>";

}elseif ($filtro_status== 'Aguardando Pagamento'){

    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Aguardando Pagamento" ."</p><br>";

}elseif ($filtro_status== 'Devolvido'){

    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedidos Devolvidos" ."</p><br>";

}


$dataProvider->pagination = ['pageSize' => 6,];

echo  ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => 'listapedidos',
]);

\yii\widgets\Pjax::end();

?>





