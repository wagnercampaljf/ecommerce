<?php

use backend\assets\AppAsset;
use frontend\widgets\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;


/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
$this->registerJs("baseUrl ='" . Yii::$app->urlManager->baseUrl . "'", \yii\web\View::POS_BEGIN);

?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css">
    <?php $this->head() ?>
</head>

<body>

    <div class="preloader"></div>

    <?php $this->beginBody() ?>
    <div class="content">
        <ul class="exo-menu">
            <!--Pedidos -->
            <li class="mega-drop-down"><a href="<?= Yii::$app->urlManager->baseUrl .  '/pedidos/index'; ?>"><i class="glyphicon glyphicon-shopping-cart"></i>Pedidos</a>
                <div class="animated fadeIn mega-menu" style="z-index: 10000">
                    <div class="mega-menu-wrap">
                        <div class="row">
                            <div class="col-md-2">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li>
                                        <a style="color: white" rel="" href="<?= Yii::$app->urlManager->baseUrl .  '/pedidos/index'; ?>">
                                            <i class="glyphicon glyphicon-shopping-cart"></i>
                                            <div class="row" style="font-size: 15px">Peça Agora</div>
                                        </a>
                                    </li><br>
                                    <li>
                                        <a style="color: white" rel="" href="<?= Yii::$app->urlManager->baseUrl .  '/pedidos/pedido-interno'; ?>">
                                            <i class="glyphicon glyphicon-shopping-cart"></i>
                                            <div class="row" style="font-size: 15px">Pedido Interno</div>

                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/pedidos-mercado-livre/index'; ?>"><i class="glyphicon glyphicon-shopping-cart"></i>
                                            <div class="row" style="font-size: 15px">Mercado Livre</div>
                                        </a>
                                    </li>
				    <br><li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/pedidos-mercado-livre/baixar-pedidos-m-l-por-data'; ?>"><i class="glyphicon glyphicon-shopping-cart"></i>
                                            <div class="row" style="font-size: 15px">Mercado Livre(Baixar por data)</div>
                                        </a>
                                    </li>
				    <br><li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/pedidos-mercado-livre-expedicao/faturamento'; ?>"><i class="glyphicon glyphicon-shopping-cart"></i>
                                            <div class="row" style="font-size: 15px">Mercado Livre(Faturamento)</div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/pedidos-b2w/index'; ?>"><i class="glyphicon glyphicon-shopping-cart"></i>
                                            <div class="row" style="font-size: 15px">Pedidos B2W</div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                           <!-- <div class="col-md-3">
                                <h4 class="row mega-title">Relatorios</h4>
                                <ul class="icon-des">
                                    <li><a href="#"><i class="glyphicon glyphicon-list-alt"></i>Relatorios</a></li>
                                </ul>
                            </div>-->

                        </div>
                    </div>
                </div>
            </li>
            <!--Produto -->
            <li class="mega-drop-down"><a href="<?= Yii::$app->urlManager->baseUrl . '/produto/index'; ?>"><i class="fa fa-cogs"></i>Produtos</a>
                <div class="animated fadeIn mega-menu" style="z-index: 10000">
                    <div class="mega-menu-wrap">
                        <div class="row">
                            <div class="col-md-2">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li> <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/categoria/index'; ?>"><i class="fa fa-sitemap"></i><div class="row" style="font-size: 15px">Categorias</div></a></li><br>
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/fabricante/index'; ?>"><i class="fa fa-indent"></i><div class="row" style="font-size: 15px">Fabricante</div></a></li><br>
                                    <li> <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/imagens'; ?>"><i class="fa fa-file-image-o"></i><div class="row" style="font-size: 15px">Imagens</div></a></li><br>
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/marca-produto'; ?>"><i class="fa fa-maxcdn"></i><div class="row" style="font-size: 15px">Marca (Produto)</div></a></li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/marca/index'; ?>"><i class="fa fa-maxcdn"></i><div class="row" style="font-size: 15px">Marca</div> </a></li><br>
                                    <li> <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/modelo/index'; ?>"><i class="fa fa-list-alt"></i><div class="row" style="font-size: 15px">Modelo</div></a></li><br>
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/produto/index'; ?>"><i class="fa fa-wrench"></i><div class="row" style="font-size: 15px">Produtos</div></a></li><br>
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/sub-categoria/index'; ?>"><i class="fa fa-sitemap"></i><div class="row" style="font-size: 15px">SubCategorias</div></a></li><br>

                                </ul>
                            </div>
                            <!--<div class="col-md-3">
                                <h4 class="row mega-title">Relatorio</h4>
                                <ul class="icon-des">
                                    <li><a style="color: white" rel="nofollow" href=""><i class="glyphicon glyphicon-list-alt"></i>Relatorios</a></li>
                                </ul>
                            </div>-->

                        </div>
                    </div>
                </div>
            </li>
            <!-- Consulta geral -->

            <li class="mega-drop-down"><a href="<?= Yii::$app->urlManager->baseUrl . '/produto/pesquisar'; ?>"><i class="glyphicon glyphicon-search"></i>Consulta</a>
                <div class="animated fadeIn mega-menu" style="z-index: 10000">
                    <div class="mega-menu-wrap">
                        <div class="row">
                            <div class="col-md-2">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/produto/pesquisar'; ?>"><i class="glyphicon glyphicon-shopping-cart"></i><div class="row" style="font-size: 15px">Pesquisar Produto</div></a></li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/produto/movimentacao-estoque'; ?>"><i class="glyphicon glyphicon-search"></i><div class="row" style="font-size: 15px">Movimentação Estoque</div></a></li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/formulario-garantia'; ?>"><i class="fa fa-list-alt"></i><div class="row" style="font-size: 15px">Garantia</div></a></li>
                                </ul>
                            </div>
                            <!-- <div class="col-md-3">
                                 <h4 class="row mega-title">Relatorios</h4>
                                 <ul class="icon-des">
                                     <li><a href="#"><i class="glyphicon glyphicon-list-alt"></i>Relatorios</a></li>
                                 </ul>
                             </div>-->

                        </div>
                    </div>
                </div>
            </li>

            <!--Estoque-->
            <li class="mega-drop-down"><a href="<?= Yii::$app->urlManager->baseUrl . '/produto-filial'; ?>"><i class="fa fa-dropbox"></i>Estoque</a>
                <div class="animated fadeIn mega-menu" style="z-index: 10000">
                    <div class="mega-menu-wrap">
                        <div class="row">
                            <div class="col-md-2">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/filial/index'; ?>"><i class="fa fa-list-alt"></i><div class="row" style="font-size: 15px">Filiais</div></a></li></br>
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/relatorio/index'; ?>"><i class="fa fa-list-alt"></i><div class="row" style="font-size: 15px">Relatórios</div></a></li>

                                </ul>
                            </div>
                            <div class="col-md-2">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li>  <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/markup-mestre/index'; ?>"><i class="fa fa-th"></i><div class="row" style="font-size: 15px">Markup</div></a></li>


                                </ul>
                            </div>
                            <div class="col-md-2">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/produto/index'; ?>"><i class="fa fa-wrench"></i><div class="row" style="font-size: 15px">Produtos</div></a></li><br>
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/produto-filial'; ?>"><i class="fa fa-dropbox"></i><div class="row" style="font-size: 15px">Estoque</div></a></li><br>
                                    <li> <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/valor-produto-filial'; ?>"><i class="fa fa-money"></i><div class="row" style="font-size: 15px">Valor</div></a></li>

                                </ul>
                            </div>


                        </div>
                    </div>
                </div>
            </li>
            <!--Compras-->
            <li class="mega-drop-down"><a href="<?= Yii::$app->urlManager->baseUrl . '/pedido-compra/index'; ?>"><i class="fa fa-shopping-cart"></i>Compras</a>
                <div class="animated fadeIn mega-menu" style="z-index: 10000">
                    <div class="mega-menu-wrap">
                        <div class="row">
                            <div class="col-md-2">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/pedido-compra/index'; ?>"><i class="fa fa-list-alt"></i><div class="row" style="font-size: 15px">Compras</div></a></li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li>  <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/compradores/index'; ?>"><i class="fa fa-th"></i><div class="row" style="font-size: 15px">Compradores</div></a></li><br>
                                    <li> <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/banner/index'; ?>"><i class="fa fa-money"></i><div class="row" style="font-size: 15px">Anúncios</div></a></li>

                                </ul>
                            </div>
                            <!--<div class="col-md-2">
                                <h4 class="row mega-title">Relatorio</h4>
                                <ul class="icon-des">
                                  <li>  <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/markup-mestre/index'; ?>"><i class="fa fa-list-alt"></i>Relatorios</a></li>
                                </ul>
                            </div>-->


                        </div>
                    </div>
                </div>
            </li>
            <!--Constula expediçaõ -->
            <li class="mega-drop-down"><a href="<?= Yii::$app->urlManager->baseUrl . '/consulta-expedicao'; ?>"><i class="glyphicon glyphicon-search"></i>Expedição</a>
            </li>
            <li class="mega-drop-down"><a href="<?= Yii::$app->urlManager->baseUrl .  '/nota-fiscal/index'; ?>"><i class="glyphicon glyphicon-shopping-cart"></i>Notas Fiscais</a>
                <div class="animated fadeIn mega-menu" style="z-index: 10000">
                    <div class="mega-menu-wrap">
                        <div class="row">
                            <div class="col-md-2">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                    <li>
                                        <a style="color: white" rel="" href="<?= Yii::$app->urlManager->baseUrl .  '/nota-fiscal/index'; ?>">
                                            <i class="glyphicon glyphicon-shopping-cart"></i>
                                            <div class="row" style="font-size: 15px">Notas Não Validadas</div>
                                        </a>
                                    </li><br>
                                    <li>
                                        <a style="color: white" rel="" href="<?= Yii::$app->urlManager->baseUrl .  '/nota-fiscal/notas-validadas'; ?>">
                                            <i class="glyphicon glyphicon-shopping-cart"></i>
                                            <div class="row" style="font-size: 15px">Notas Validadas</div>
                                        </a>
                                    </li><br>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </li>

            <li class="mega-drop-down"><a href="<?= Yii::$app->urlManager->baseUrl . '/produto/pesquisar'; ?>"><i class="fa fa-wrench"></i>Processamentos</a>
                <div class="animated fadeIn mega-menu" style="z-index: 10000">
                    <div class="mega-menu-wrap">
                        <div class="row">


                        

                            <div class="col-md-2">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                <li>  <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/processamento/index'; ?>"><i class="fa fa-wrench"></i><div class="row" style="font-size: 15px">Criar processamento</div></a></li><br>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h4 class="row mega-title"></h4>
                                <ul class="">
                                <li>  <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/funcao/index'; ?>"><i class="fa fa-wrench"></i><div class="row" style="font-size: 15px">Funçoes</div></a></li>
                                </ul>
                            </div>
                        
                       
                        </div>
                    </div>
                </div>
            </li>




            <!--Relatorio -->
            <!--<li class="mega-drop-down"><a href="#"><i class="fa fa-list"></i>Relatorios</a>
                <div class="animated fadeIn mega-menu" style="z-index: 10000">
                    <div class="mega-menu-wrap">
                        <div class="row">
                            <div class="col-md-2">
                                <h4 class="row mega-title">Estoque</h4>
                                <ul class="icon-des">
                                    <li> <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/produto-filial/relatorios'; ?>"><i class="fa fa-globe"></i>Produtos por filial </a></li>
                                </ul>
                            </div>
                            <div class="col-md-2">
                                <h4 class="row mega-title">Produto</h4>
                                <ul class="icon-des">
                                    <li> <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/produto-filial/relatorios'; ?>"><i class="fa fa-globe"></i>Listagem de Produtos </a></li>
                                </ul>
                            </div>
                            <div class="col-md-2">
                                <h4 class="row mega-title">Pedidos</h4>
                                <ul class="icon-des">
                                    <li> <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/produto-filial/relatorios'; ?>"><i class="fa fa-globe"></i>Pedidos ML </a></li>
                                    <li> <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/produto-filial/relatorios'; ?>"><i class="fa fa-globe"></i>Pedidos B2W </a></li>
                                    <li> <a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/produto-filial/relatorios'; ?>"><i class="fa fa-globe"></i>Pedidos Peça Agora</a></li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </li>-->

            <!--login-->
            <li class="mega-drop-down">
                <a  rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/site/logout'; ?>">
                    <?= '<i class="fa fa-power-off"></i> Sair (' . (isset(Yii::$app->user->identity->nome) ? Yii::$app->user->identity->nome : "") . ')' ?>
                </a>

            </li>
            <a href="#" class="toggle-menu visible-xs-block">|||</a>
        </ul>

    </div><br><br>

    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

    <style>


        .clearfix:after,
        .clearfix:before {
            content: '';
            display: table
        }

        .clearfix:after {
            clear: both;
            display: block
        }
        ul{
            list-style:none;
            margin: 0;
            padding: 0;
        }

       /* .content{
            margin: 2px 100px 0px 100px;
        }*/

        .exo-menu{
            width: 100%;
            float: left;
            list-style: none;
            position:relative;
            background: #23364B;
        }
        .exo-menu > li {	display: inline-block;float:left;}
        .exo-menu > li > a{
            color: #ccc;
            text-decoration: none;
            text-transform: uppercase;
            border-right: 1px #365670 dotted;
            -webkit-transition: color 0.2s linear, background 0.2s linear;
            -moz-transition: color 0.2s linear, background 0.2s linear;
            -o-transition: color 0.2s linear, background 0.2s linear;
            transition: color 0.2s linear, background 0.2s linear;
        }
        .exo-menu > li > a.active,
        .exo-menu > li > a:hover,
        li.drop-down ul > li > a:hover{
            background:#009FE1;
            color:#fff;
        }
        .exo-menu i {
            float: left;
            font-size: 18px;
            margin-right: 6px;
            line-height: 20px !important;
        }
        li.drop-down,
        .flyout-right,
        .flyout-left{position:relative;}
        li.drop-down:before {
            content: "\f103";
            color: #fff;
            font-family: FontAwesome;
            font-style: normal;
            display: inline;
            position: absolute;
            right: 6px;
            top: 20px;
            font-size: 14px;
        }
        li.drop-down>ul{
            left: 0px;
            min-width: 230px;

        }
        .drop-down-ul{display:none;}
        .flyout-right>ul,
        .flyout-left>ul{
            top: 0;
            min-width: 230px;
            display: none;
            border-left: 1px solid #365670;
        }

        li.drop-down>ul>li>a,
        .flyout-right ul>li>a ,
        .flyout-left ul>li>a {
            color: #fff;
            display: block;
            padding: 20px 22px;
            text-decoration: none;
            background-color: #365670;
            border-bottom: 1px dotted #547787;
            -webkit-transition: color 0.2s linear, background 0.2s linear;
            -moz-transition: color 0.2s linear, background 0.2s linear;
            -o-transition: color 0.2s linear, background 0.2s linear;
            transition: color 0.2s linear, background 0.2s linear;
        }
        .flyout-right ul>li>a ,
        .flyout-left ul>li>a {
            border-bottom: 1px dotted #B8C7BC;
        }

        h4.row.mega-title {
            color:#eee;
            margin-top: 0px;
            font-size: 14px;
            padding-left: 15px;
            padding-bottom: 13px;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
        }
        .flyout-mega ul > li > a {
            font-size: 90%;
            line-height: 25px;
            color: #fff;
            font-family: inherit;
        }
        .flyout-mega ul > li > a:hover,
        .flyout-mega ul > li > a:active,
        .flyout-mega ul > li > a:focus{
            text-decoration: none;
            background-color: transparent !important;
            color: #ccc !important
        }
        /*mega menu*/

        .mega-menu {
            left: 0;
            right: 0;
            padding: 15px;
            display:none;
            padding-top: 0;
            min-height: 100%;

        }
        h4.row.mega-title {
            color: #eee;
            margin-top: 0px;
            font-size: 14px;
            padding-left: 15px;
            padding-bottom: 13px;
            text-transform: uppercase;
            border-bottom: 1px solid #547787;
            padding-top: 15px;
            background-color: #365670
        }
        .mega-menu ul li a {
            line-height: 25px;
            font-size: 90%;
            display: block;
        }
        ul.stander li a {
            padding: 3px 0px;
        }

        ul.description li {
            padding-bottom: 12px;
            line-height: 8px;
        }

        ul.description li span {
            color: #ccc;
            font-size: 85%;
        }
        a.view-more{
            border-radius: 1px;
            margin-top:15px;
            background-color: #009FE1;
            padding: 2px 10px !important;
            line-height: 21px !important;
            display: inline-block !important;
        }
        a.view-more:hover{
            color:#fff;
            background:#0DADEF;
        }
        ul.icon-des li a i {
            color: #fff;
            text-align: center;
        }

        ul.icon-des li {
            width: 100%;
            display: table;
            margin-bottom: 11px;
        }
        /*Blog DropDown*/
        .Blog{
            left:0;
            display:none;
            color:#fefefe;
            padding-top:15px;
            background:#547787;
            padding-bottom:15px;
        }
        .Blog .blog-title{
            color:#fff;
            font-size:15px;
            text-transform:uppercase;

        }
        .Blog .blog-des{
            color:#ccc;
            font-size:90%;
            margin-top:15px;
        }
        .Blog a.view-more{
            margin-top:0px;
        }

        .Images h4 {
            font-size: 15px;
            margin-top: 0px;
            text-transform: uppercase;
        }
        /*common*/
        .flyout-right ul>li>a ,
        .flyout-left ul>li>a,
        .flyout-mega-wrap,
        .mega-menu{
            background-color: #547787;
        }

        /*hover*/
        .Blog:hover,
        .Images:hover,
        .mega-menu:hover,
        .drop-down-ul:hover,
        li.flyout-left>ul:hover,
        li.flyout-right>ul:hover,
        .flyout-mega-wrap:hover,
        li.flyout-left a:hover +ul,
        li.flyout-right a:hover +ul,
        .blog-drop-down >a:hover+.Blog,
        li.drop-down>a:hover +.drop-down-ul,
        .images-drop-down>a:hover +.Images,
        .mega-drop-down a:hover+.mega-menu,
        li.flyout-mega>a:hover +.flyout-mega-wrap{
            display:block;
        }
        /*responsive*/
        @media (min-width:767px){
            .exo-menu > li > a{
                display:block;
                padding: 20px 22px;
            }
            .mega-menu, .flyout-mega-wrap, .Images, .Blog,.flyout-right>ul,
            .flyout-left>ul, li.drop-down>ul{
                position:absolute;
            }
            .flyout-right>ul{
                left: 100%;
            }
            .flyout-left>ul{
                right: 100%;
            }
        }
        @media (max-width:767px){

            .exo-menu {
                min-height: 58px;
                background-color: #23364B;
                width: 100%;
            }

            .exo-menu > li > a{
                width:100% ;
                display:none ;

            }
            .exo-menu > li{
                width:100%;
            }
            .display.exo-menu > li > a{
                display:block ;
                padding: 20px 22px;
            }

            .mega-menu, .Images, .Blog,.flyout-right>ul,
            .flyout-left>ul, li.drop-down>ul{
                position:relative;
            }

        }
        a.toggle-menu{
            position: absolute;
            right: 0px;
            padding: 20px;
            font-size: 27px;
            background-color: #ccc;
            color: #23364B;
            top: 0px;
        }
    </style>


    <script>



        $(function () {
            $('.toggle-menu').click(function(){
                $('.exo-menu').toggleClass('display');

            });

        });


    </script>

    <!-- BEGIN PAGE CONTAINER -->
    <div class="page-container"><br><br>
        <!-- BEGIN PAGE HEAD -->
        <div class="page-head">
            <div class="container">
                <!-- BEGIN PAGE TITLE -->
                <div class="page-title">
                    <h1><?= Html::encode($this->title); ?>
                    </h1>
                </div>
                <!-- END PAGE TITLE -->
            </div>
        </div>
        <!-- END PAGE HEAD -->
        <!-- BEGIN PAGE CONTENT -->
        <div class="page-content">
            <div class="container">
                <!-- BEGIN PAGE BREADCRUMB -->
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    'itemTemplate' => "<li>{link}</li> | "
                ]) ?>
                <!-- END PAGE BREADCRUMB -->
                <!-- BEGIN PAGE CONTENT INNER -->
                <div class="row">
                    <div class="col-md-12">
                        <?= Alert::widget() ?>
                        <?= $content; ?>
                    </div>
                </div>
                <!-- END PAGE CONTENT INNER -->
            </div>
        </div>
        <!-- END PAGE CONTENT -->
    </div>
    <!-- END PAGE CONTAINER -->
    <!-- BEGIN PRE-FOOTER -->
    <div class="page-prefooter">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 footer-block">
                    <h2>SOBRE</h2>

                    <p>
                        Peça Agora: o Shopping online onde você pode vender as melhores peças, baterias e pneus para
                        veículos leves e pesados.
                    </p>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12 footer-block">
                    <h2>Contato</h2>
                    <address class="margin-bottom-40">
                        Telefone: (32) 3015-0023<br>
                        Email: <a href="maito:contato@pecaagora.com">lojista@pecaagora.com</a>
                    </address>
                </div>
            </div>
        </div>
    </div>
    <!-- END PRE-FOOTER -->
    <!-- BEGIN FOOTER -->
    <div class="page-footer">
        <div class="container text-center">
            www.pecaagora.com / OPT Soluções LTDA / CNPJ nº 18.947.338/0001-10 / Endereço: AV Brasil, 5550, Santa Terezinha - Juiz de Fora - MG -
            36045-475<br>
            2021 &copy; PeçaAgora. Todos os Direitos Reservados.
        </div>
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>

    <?php $this->endBody() ?>
    <script>
        jQuery(document).ready(function() {
            Metronic.init(); // init metronic core components
            Layout.init(); // init current layout
            Demo.init();
        });
    </script>

</body>

</html>
<?php $this->endPage() ?>
