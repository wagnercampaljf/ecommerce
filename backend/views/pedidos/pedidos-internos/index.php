<?php

use yii\grid\GridView;
use yii\helpers\Html;
use backend\models\PedidoMercadoLivreSearch;
use common\models\PedidoSearch;
use common\models\ProdutoFilial;
use yii\widgets\ListView;
use yii\helpers\Url;

\yii\widgets\Pjax::begin(['timeout' => 5000]);

$this->title = 'Pedidos Internos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-index">

    <div class="container">
        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos/pedido-interno']) ?>">
                                <div class="input-group col-md-12">
                                    <input type="text" name="filtro" id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist" placeholder="Procure por dados do pedido ..." value="">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
                                    </span>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos/baixar-pedido-omie']) ?>">
                                <div class="input-group col-md-12">
                                    <input type="text" name="num_pedido" id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist" placeholder="Informe Número Pedido Omie " value="">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-download" style="color: white"></i></button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div><br>

                    <form id="subscribeNews" action="<?= Url::to(['/pedidos/pedido-interno']) ?>">
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

                    $dataProvider = $searchModel->searchInterno(['PedidoSearch' => ['id' => $filtro]]);

                    $dataProvider = $searchModel->filtro_status_interno(['PedidoSearch' => ['status' => $filtro_status]]);

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
                    ?>
                    <p>
                        <br>
                        <?= Html::a('Novo Pedido Interno', ['create'], ['class' => 'btn btn-success']) ?>
                    </p>
                </div>
                <?php

                echo  ListView::widget([
                    'dataProvider' => $dataProvider,
                    'itemView' => '../index/listapedidos',
                ]);

                \yii\widgets\Pjax::end();

                ?>
            </div>
        </div>

    </div>
</div>
</div>