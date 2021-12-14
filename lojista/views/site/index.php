<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = 'Painel Administrativo';
$this->params['breadcrumbs'][] = ['label' => ' / ' . $this->title];

$this->registerJsFile(
    Url::to(['/js/topSell.js']),
    ['depends' => [lojista\assets\AppAsset::className()]]
);

$this->registerJs('
    jQuery(document).ready(function () {
        $.ajax({
            type: "GET",
            url: baseUrl + "/site/top-sell",
            dataType: "JSON",
            success: function (data) {
                topSell(data);
            },
            error: function (data) {
                console.log(data);
            }
        });
    });
');


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
                            Total de Pedidos
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
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="overview_1">
                                    <div class="table-responsive">
                                        <table id="topsell" class="table table-hover table-light">
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


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
<!--                                <div class="tab-pane" id="overview_3">-->
<!--                                    <div class="table-responsive">-->
<!--                                        <table class="table table-hover table-light">-->
<!--                                            <thead>-->
<!--                                            <tr>-->
<!--                                                <th>-->
<!--                                                    Nome do cliente-->
<!--                                                </th>-->
<!--                                                <th>-->
<!--                                                    total de pedidos-->
<!--                                                </th>-->
<!--                                                <th>-->
<!--                                                    Valor total-->
<!--                                                </th>-->
<!--                                                <th>-->
<!--                                                </th>-->
<!--                                            </tr>-->
<!--                                            </thead>-->
<!--                                            <tbody>-->
<!--                                            <tr>-->
<!--                                                <td>-->
<!--                                                    <a href="javascript:;">-->
<!--                                                        David Wilson </a>-->
<!--                                                </td>-->
<!--                                                <td>-->
<!--                                                    3-->
<!--                                                </td>-->
<!--                                                <td>-->
<!--                                                    $625.50-->
<!--                                                </td>-->
<!--                                                <td>-->
<!--                                                    <a href="javascript:;" class="btn default btn-xs green-stripe">-->
<!--                                                        View </a>-->
<!--                                                </td>-->
<!--                                            </tr>-->
<!--                                            </tbody>-->
<!--                                        </table>-->
<!--                                    </div>-->
<!--                                </div>-->
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
                            <div class="tab-pane " id="portlet_tab2">
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

<?php $this->registerJs('
        function showTooltip(x, y, labelX, labelY) {
            $(\'<div id="tooltip" class="chart-tooltip">\' + (labelY.toFixed(0)) + \'BRL<\/div>\').css({
                position: \'absolute\',
                display: \'none\',
                top: y - 40,
                left: x - 60,
                border: \'0px solid #ccc\',
                padding: \'2px 6px\',
                \'background-color\': \'#fff\'
            }).appendTo("body").fadeIn(200);
        }

        $.ajax({
            type: "GET",
            url: baseUrl + "/site/pedidos-mes",
            dataType: "JSON",
            success: function (data) {
                var arr = $.map(data, function (el) {
                    return [[el.data, el.count]];
                });
                console.log(arr);
                initChart1(arr);
            },
            error: function (data) {
                console.log(data);
            }
        });

        $.ajax({
            type: "GET",
            url: baseUrl + "/site/valor-pedidos-mes",
            dataType: "JSON",
            success: function (data) {
                var arr = $.map(data, function (el) {
                    return [[el.data, el.sum]];
                });
                console.log(arr);
                initChart2(arr);
            },
            error: function (data) {
                console.log(data);
            }
        });

        $.ajax({
            type: "GET",
            url: baseUrl + "/site/numeros-dash",
            dataType: "JSON",
            success: function (data) {
                var arr = $.map(data, function (el) {
                    return [[el.sum, el.count, el.avg]];
                });
                var num = arr[0][2];
                var media = num.toString().match(/^\d+(?:\.\d{0,2})?/);
                var soma = arr[0][0];
                var pedidos = arr[0][1];

                $("#totalorders").append("<p>" + pedidos + "</p>");
                $("#sales").append("<p>R$ " + soma + "</p>");
                $("#averageorders").append("<p>R$ " + media + "</p>");
            },
            error: function (data) {
                console.log(data);
            }
        });


        var initChart1 = function (data) {
            var plot_statistics = $.plot(
                $("#statistics_1"),
                [
                    {
                        data: data,
                        lines: {
                            fill: 0.6,
                            lineWidth: 0
                        },
                        color: [\'#f89f9f\']
                    },
                    {
                        data: data,
                        points: {
                            show: true,
                            fill: true,
                            radius: 5,
                            fillColor: "#f89f9f",
                            lineWidth: 3
                        },
                        color: \'#fff\',
                        shadowSize: 0
                    }
                ],
                {
                    xaxis: {
                        tickLength: 0,
                        tickDecimals: 0,
                        mode: "categories",
                        min: 0,
                        font: {
                            lineHeight: 15,
                            style: "normal",
                            variant: "small-caps",
                            color: "#6F7B8A"
                        }
                    },
                    yaxis: {
                        ticks: 3,
                        tickDecimals: 0,
                        tickColor: "#f0f0f0",
                        font: {
                            lineHeight: 15,
                            style: "normal",
                            variant: "small-caps",
                            color: "#6F7B8A"
                        }
                    },
                    grid: {
                        backgroundColor: {
                            colors: ["#fff", "#fff"]
                        },
                        borderWidth: 1,
                        borderColor: "#f0f0f0",
                        margin: 0,
                        minBorderMargin: 0,
                        labelMargin: 20,
                        hoverable: true,
                        clickable: true,
                        mouseActiveRadius: 6
                    },
                    legend: {
                        show: false
                    }
                }
            );
            var previousPoint = null;
            $("#statistics_1").bind("plothover", function (event, pos, item) {
                $("#x").text(pos.x.toFixed(2));
                $("#y").text(pos.y.toFixed(2));
                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(2),
                            y = item.datapoint[1].toFixed(2);

                        showTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1]);
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });

        };

        var initChart2 = function (data) {

            var plot_statistics = $.plot(
                $("#statistics_2"),
                [
                    {
                        data: data,
                        lines: {
                            fill: 0.6,
                            lineWidth: 0
                        },
                        color: [\'#BAD9F5\']
                    },
                    {
                        data: data,
                        points: {
                            show: true,
                            fill: true,
                            radius: 5,
                            fillColor: "#BAD9F5",
                            lineWidth: 3
                        },
                        color: \'#fff\',
                        shadowSize: 0
                    }
                ],
                {

                    xaxis: {
                        tickLength: 0,
                        tickDecimals: 0,
                        mode: "categories",
                        min: 0,
                        font: {
                            lineHeight: 14,
                            style: "normal",
                            variant: "small-caps",
                            color: "#6F7B8A"
                        }
                    },
                    yaxis: {
                        ticks: 3,
                        tickDecimals: 0,
                        tickColor: "#f0f0f0",
                        font: {
                            lineHeight: 14,
                            style: "normal",
                            variant: "small-caps",
                            color: "#6F7B8A"
                        }
                    },
                    grid: {
                        backgroundColor: {
                            colors: ["#fff", "#fff"]
                        },
                        borderWidth: 1,
                        borderColor: "#f0f0f0",
                        margin: 0,
                        minBorderMargin: 0,
                        labelMargin: 20,
                        hoverable: true,
                        clickable: true,
                        mouseActiveRadius: 6
                    },
                    legend: {
                        show: false
                    }
                }
            );

            var previousPoint = null;

            $("#statistics_2").bind("plothover", function (event, pos, item) {
                $("#x").text(pos.x.toFixed(2));
                $("#y").text(pos.y.toFixed(2));
                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(2),
                            y = item.datapoint[1].toFixed(2);

                        showTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1]);
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });

        }
', \yii\web\View::POS_END) ?>