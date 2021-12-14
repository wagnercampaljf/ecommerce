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
                    <input type="text" name="filtro" id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist" placeholder="Procure por dados do pedido ..." value="<?= $filtro ?>">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
                    </span>
                </div>
            </form>
        </div>
    </div><br>
</div>

<form id="subscribeNews" action="<?= Url::to(['/pedidos/index']) ?>">
    <button type="submit" name="" value="" class="btn-outline default"><span>Limpar filtro</span></button>
    <button type="submit" name="filtro_status" value="Aberto" class="btn btn-primary">Aberto</button>
    <button type="submit" name="filtro_status" value="Confirmado" class="btn btn-success">Confirmado</button>
    <button type="submit" name="filtro_status" value="Enviado" class="btn btn-secondary" style="background-color: rgba(36,138,169,0.67)">Enviado</button>
    <button type="submit" name="filtro_status" value="Concluído" class="btn btn-dark" style="background-color: #2f323e; color: white">Concluído</button>
    <button type="submit" name="filtro_status" value="Cancelado" class="btn btn-danger">Cancelado</button>
    <button type="submit" name="filtro_status" value="Aguardando Pagamento" class="btn btn-info">Aguardando Pagamento</button>
    <button type="submit" name="filtro_status" value="Devolvido" class="btn btn-primary" style="background-color: rgba(255,57,17,0.78)">Devolvido</button>
</form>


<?php

$searchModel = new PedidoSearch();

$dataProvider = $searchModel->search(['PedidoSearch' => ['id' => $filtro]]);

$dataProvider = $searchModel->filtro_status_site(['PedidoSearch' => ['status' => $filtro_status]]);

if ($filtro_status == 'Aberto') {

    echo "<br>" . "<p style='font-size: 30px;color: #d90010'>" . "Pedidos em Aberto" . "</p><br>";
} elseif ($filtro_status == 'Confirmado') {
    echo "<br>" . "<p style='font-size: 30px;color: #d90010'>" . "Pedidos Confirmados" . "</p><br>";
} elseif ($filtro_status == 'Enviado') {

    echo "<br>" . "<p style='font-size: 30px;color: #d90010'>" . "Pedidos Enviados" . "</p><br>";
} elseif ($filtro_status == 'Concluído') {

    echo "<br>" . "<p style='font-size: 30px;color: #d90010'>" . "Pedidos Concluídos" . "</p><br>";
} elseif ($filtro_status == 'Cancelado') {

    echo "<br>" . "<p style='font-size: 30px;color: #d90010'>" . "Pedidos Cancelados" . "</p><br>";
} elseif ($filtro_status == 'Aguardando Pagamento') {

    echo "<br>" . "<p style='font-size: 30px;color: #d90010'>" . "Aguardando Pagamento" . "</p><br>";
} elseif ($filtro_status == 'Devolvido') {

    echo "<br>" . "<p style='font-size: 30px;color: #d90010'>" . "Pedidos Devolvidos" . "</p><br>";
}

$dataProvider->pagination = ['pageSize' => 6,];

echo  ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => 'listapedidos',
]);

\yii\widgets\Pjax::end();

?>