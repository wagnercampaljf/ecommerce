<?php
/* @var $this yii\web\View */

$this->title = 'Painel Administrativo';
$this->params['breadcrumbs'][] = ['label' => ' / ' . $this->title];


?>
<div class="site-index">

    <div class="">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 margin-bottom-10">
                <a class="dashboard-stat dashboard-stat-light blue-madison" href="javascript:;">
                    <div class="visual">
                        <i class="fa fa-briefcase fa-icon-medium"></i>
                    </div>
                    <div class="details">
                        <div id="sales" class="number">

                        </div>
                        <div class="desc">
                            Total de Vendas
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a class="dashboard-stat dashboard-stat-light red-intense" href="javascript:;">
                    <div class="visual">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div class="details">
                        <div id="totalorders" class="number">


                        </div>
                        <div class="desc">
                            Total de pedidos
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a class="dashboard-stat dashboard-stat-light green-haze" href="javascript:;">
                    <div class="visual">
                        <i class="fa fa-group fa-icon-medium"></i>
                    </div>
                    <div class="details">
                        <div id="averageorders" class="number">

                        </div>
                        <div class="desc">
                            Média por pedido
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <!-- Begin: life time stats -->
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-bar-chart font-green-sharp"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Visão Geral</span>
                            <span class="caption-helper"></span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="tabbable-line">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#overview_1" data-toggle="tab">Mais Vendidos </a>
                                </li>
                                <li>
                                    <a href="#overview_3" data-toggle="tab">Clientes </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="overview_1">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-light">
                                            <thead>
                                            <tr class="uppercase">
                                                <th>
                                                    Nome do produto
                                                </th>
                                                <th>
                                                    Preço
                                                </th>
                                                <th>
                                                    Vendidos
                                                </th>
                                                <th>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <a href="javascript:;">
                                                        Apple iPhone 4s - 16GB - Black </a>
                                                </td>
                                                <td>
                                                    $625.50
                                                </td>
                                                <td>
                                                    809
                                                </td>
                                                <td>
                                                    <a href="javascript:;" class="btn default btn-xs green-stripe">
                                                        View </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="javascript:;">
                                                        Metronic - Responsive Admin + Frontend Theme </a>
                                                </td>
                                                <td>
                                                    $20.00
                                                </td>
                                                <td>
                                                    11190
                                                </td>
                                                <td>
                                                    <a href="javascript:;" class="btn default btn-xs green-stripe">
                                                        View </a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="overview_3">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-light">
                                            <thead>
                                            <tr>
                                                <th>
                                                    Nome do cliente
                                                </th>
                                                <th>
                                                    total de pedidos
                                                </th>
                                                <th>
                                                    Valor total
                                                </th>
                                                <th>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <a href="javascript:;">
                                                        David Wilson </a>
                                                </td>
                                                <td>
                                                    3
                                                </td>
                                                <td>
                                                    $625.50
                                                </td>
                                                <td>
                                                    <a href="javascript:;" class="btn default btn-xs green-stripe">
                                                        View </a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End: life time stats -->
            </div>
            <div class="col-md-6">
                <!-- Begin: life time stats -->
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption">
                            <i class="icon-share font-red-sunglo"></i>
                            <span class="caption-subject font-red-sunglo bold uppercase">Receita</span>
                            <span class="caption-helper">Pedidos concluídos</span>
                        </div>
                        <ul class="nav nav-tabs">
                            <li>
                                <a href="#portlet_tab2" data-toggle="tab" id="statistics_amounts_tab">
                                    Valores </a>
                            </li>
                            <li class="active">
                                <a href="#portlet_tab1" data-toggle="tab">
                                    Pedidos </a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="portlet_tab1">
                                <div id="statistics_1" class="chart">
                                </div>
                            </div>
                            <div class="tab-pane" id="portlet_tab2">
                                <div id="statistics_2" class="chart">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End: life time stats -->
            </div>
        </div>
    </div>
</div>


<script>

</script>