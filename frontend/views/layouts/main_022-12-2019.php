<?php
//die('Site em Manutenção');

use common\models\Categoria;
use common\models\Respostas;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;
use frontend\widgets\Menu;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use \frontend\widgets\MenuWidget;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
//BaseUrl no javascript para facilitar a utilização de caminhos relativos durante o desenvolvimento
$this->registerJs("baseUrl ='" . Yii::$app->urlManager->baseUrl . "'", \yii\web\View::POS_BEGIN);
$this->registerJs("
$('.navbar').hover(
 function() {
    $('.navbar-collapse ').collapse('show');
  }, function() {
    $('.navbar-collapse ').collapse('hide');
  }
);
");
?>
<?php $this->beginPage()
?>

    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <?php
    //if (!YII_DEBUG) {
    echo $this->render('scriptZendesk');
    //}
    ?>

    <head>
        <link rel="icon" href="<?= yii::$app->urlManager->baseUrl . '/assets/img/favicon.ico' ?>" sizes="16x16"
              type="image/png">
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="google-site-verification" content="Ds43WYszDU1f65yxGLuoqFn_TFPocfAmGbdbe-qOsfs" />
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title . ' Peça Agora Auto') ?></title>
        <?php $this->head() ?>

        <script src="<?= yii::$app->urlManager->baseUrl . '/frontend/web/js/jquery-1.8.3.min.js' ?>" type="text/javascript"></script>
        <script src="<?= yii::$app->urlManager->baseUrl . '/frontend/web/js/jquery.elevatezoom.js' ?>" type="text/javascript"></script>

        <!-- Facebook Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window,document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '465917450866477');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1"
                 src="https://www.facebook.com/tr?id=465917450866477&ev=PageView
		&noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->

        <!--Script google-->
        <script async src="https://www.googletagmanager.com/gtag/js?id=952417205"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '952417205');
        </script>
        <!--Script google-->

        <!--  Google analyticts-->
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-151579146-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-151579146-1');
        </script>
        <!--  Google analyticts-->

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-PV5X39J');
        </script>
        <!-- End Google Tag Manager -->
    </head>
    <!--BLOQUEIO DA SELECAO DE CONTEUDO -->
    <!--<body ondragstart='return false' onselectstart='return false' oncontextmenu='return false'>-->
    <body>

    <!--PRELOADER -->

    <div class="preloader"></div>

    <script>


        $(document).ready(function() {
            $('.preloader').fadeOut('slow');

        })
        //$(window).load(function() {
           // $('.preloader').fadeOut();
        //});
    </script>

    <style>
        /*   PRELOADER */
        .preloader {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 999999;
            background-image: url("<?= Url::to('@assets/'); ?>img/engrenagens3.gif");
            background-repeat: no-repeat;
            background-color: rgba(255, 255, 255, 0.54);
            background-position: center;
            display: block;
        }
    </style>


    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PV5X39J" height="0" width="0" style="display:none;visibility:hidden">
        </iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->

    <?php //echo "|| ".date('h')." ||";?>
    <?php //echo date('Y-m-d H:i:s')." ||";?>
    <?php //echo "Yii::getLogger()->getElapsedTime(): "; echo Yii::getLogger()->getElapsedTime(); ?>
    <!--<div>Sesao_Carrinho: <?= ""//print_r(Yii::$app->session['carrinho'])?></div>-->
    <?php $this->beginBody() ?>

    <!-- navbar-fixed-top -->
    <div class="header hidden-xs"><!-- navbar-fixed-top -->
        <div class="menu-sup text-left nav <?php if (Url::base(true) . '/' != Yii::$app->getRequest()->absoluteUrl) { echo "hidden-xs"; } ?>">
            <img src="<?= Url::to('@assets/'); ?>img/bannertoponatal.jpg"  style="width: 100%; text-align: center">
            <!--
            <ul class="top-list">
                <li>
                    <b>Grande São Paulo:</b>
                </li>
                <li>
                    <i class="fa fa-phone-square"></i>&nbsp;(11) 2193-1099
                </li>
                <li>
                    <i class="fa fa-whatsapp"></i>&nbsp;(11) 94554-4208
                </li>
                <li>
                    <i class="fa fa-envelope-o"></i>&nbsp;<b>sac@pecaagora.com</b>
                </li>
                <li class="hidden-md hidden-lg">
                    <br>
                </li>
                <li>
                    <b>Demais Regiões:</b>
                </li>
                <li>
                    <i class="fa fa-phone-square"></i>&nbsp;(32) 3015-0023
                </li>
                <li>
                    <i class="fa fa-whatsapp"></i>&nbsp;(32) 98835-4007
                </li>
            </ul>
            -->
        </div>
    </div>
    </div>
    </div>
    <!-- navbar-fixed-top -->



    <!-- PESQUISAR -->

    <div class="clearfix col-md-12 cab_fixo " style="padding: 1px;  background: linear-gradient(#f7f7f7, #f7f7f7); height: 66px">
        <div class="col-lg-2 col-sm-4 col-xs-12 logo-wrap hidden-xs" style="margin-top:-5px;margin-bottom: 10px;z-index: 99">
            <div>
                <a href="<?= Url::to(['/']) ?>">
                    <img style="width: 198px; height:63px " class="logo" alt="Peca Agora" title="Peça Agora"
                         src="<?= Url::to('@assets/'); ?>img/pecaagora_natal.png">
                </a>
            </div>
        </div>
        <div class="main-search col col-lg-7 col-sm-6 col-xs-12 container-fluid" style="margin-bottom: 10px; padding-top: 15px">
            <?php //if (Url::base(true) . '/' != Yii::$app->getRequest()->absoluteUrl) { ?><?php //} ?>
            <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/search']) ?>">
                <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12"
                     style="padding-left: 0px !important;padding-right: 0px !important;">
                    <input type="text"
                           name="<?= is_null(Yii::$app->request->get('codigo_global')) ? 'nome' : 'codigo_global' ?>"
                           id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist"
                           placeholder="Temos tudo, pode procurar :)"
                           value="<?= Yii::$app->request->get('nome',
                               Yii::$app->request->get('codigo_global', null)) ?>">
                    <span class="input-group-btn">
                      <button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
                </span>
                </div>
                <?php
                $this->registerJs("
                $('#main-search-product').change(function () {
                results = new RegExp('#').exec($(this).val());
                if(results != null && results['index'] == 0 ){
                $(this).attr('name', 'codigo_global');
                }else{
                $(this).attr('name', 'nome');
                }
                });
                ");
                ?>
            </form>
            <?php //} ?>
        </div>
        <?php if (Yii::$app->user->isGuest) { ?>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-9 hidden-xs" >
                <i class="pull-left fa fa-user-o fa-3x" style="color: #040100 !important; aria-hidden=" true"></i>
                <span style=" color: #000000"> Olá, Bem vindo(a)</span><br>
                <a rel="nofollow" style="font-weight: bold; font-size: 14px; color: #000000" href="<?= Url::to(['/site/login']) ?>">Entre </a>
                <span style="font-weight: bold; color: black"> ou </span>
                <a rel="nofollow" style="font-weight: bold; font-size: 14px; color: #000000" href="<?= Url::to(['/comprador/create?tipoEmpresa=fisica']) ?>"> Cadastre-se</a>
            </div>
        <?php } else { ?>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-9 hidden-xs" >
                <i class="pull-left fa fa-user-o fa-3x" style="color: #040100 !important; aria-hidden=" true"></i>
                <span style=" color: #040100">Olá, <?= current(str_word_count(Yii::$app->user->getIdentity()->nome, 2)) ?> </span><br>
                <a style="color: black" href="<?= Url::to(['/minhaconta/pedidos']) ?>">Minha Conta</a> |
                <a style="color: black" rel="nofollow" href="<?= Url::to(['/site/logout']) ?>">Sair</a>
            </div>
            <?php
        }?>

        <div class="row hidden-xs">
            <div class="col">
                <a rel="nofollow" href="<?= Url::to(['/carrinho']) ?>" class="cart-wrap pull-left" style="padding: -0px; color: #040100 !important;">
                    <i class="fa fa-shopping-cart fa-3x" style="color: #040100 !important;"></i>
                    <span class="badge cart-count"><?= count(Yii::$app->session['carrinho']) ?></span>

                </a>

                <li class="nav-toggle hidden-lg" style="background-color: white ;border-radius: 10px">
                    <button style="background-color: #007576" class="nav-toggle-btn main-btn icon-btn"><i style="color: white" class="fa fa-bars"></i></button>
                </li>
            </div>
        </div>

        <!-- MOBILE -->
        <div class="col nav-toggle hidden-lg">
            <a rel="nofollow" href="<?= Url::to(['/carrinho']) ?>" class="cart-wrap pull-left" style="padding: -0px; color: #040100 !important;">
                <i class="fa fa-shopping-cart fa-3x" style="color: #040100 !important;"></i>
                <span class="badge cart-count"><?= count(Yii::$app->session['carrinho']) ?></span>

                <img class="nav-toggle" style="height: 60px; width: 250px" alt="Peca Agora" title="Peça Agora"
                     src="<?= Url::to('@assets/'); ?>img/pecaagora_natal.png">
            </a>

            <li class="nav-toggle hidden-lg" style="background-color: white;">
                <button style="background-color: #007576" class="nav-toggle-btn main-btn icon-btn"><i style="color: white" class="fa fa-bars"></i></button>
            </li>
        </div>





    </div>
    <div class="container">

    </div>




    <style>

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            padding: 12px 16px;
            z-index: 1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown:hover .custom-menu {
            display: block;
        }
    </style>

    <style>

        .custom-menu {

            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            padding: 12px 16px;
            z-index: 1;
        }

        .dropdown:hover .custom-menu {
            display: block;

        }
        .dropdown {
            border-left:solid 1px transparent;
            border-right: solid 1px transparent;
            box-sizing: border-box;
            margin-bottom: -1px;
            line-height:8px;
            padding:5px 13px 5px;
            position: static !important;
        }
        .menu-list li{
            display: inline-block;

        }

        .dropdown-content{
            position: absolute;
            left:5%;
            top: 125px;
            width: 90%;

        }

        .text-menu{
            color: white;

        }

        }
    </style>

    <!--<div id="navigation" style="background-color: #007576;">
            <div id="responsive-nav">

                <div class="category-nav show-on-click">
                    <span class="category-header">Categories <i class="fa fa-list"></i></span>
                    <ul class="category-list">
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                        <li class="dropdown side-dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Women’s Clothing <i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="list-links">
                                            <li>
                                                <h3 class="list-links-title">Categories</h3></li>
                                            <li><a href="#">Women’s Clothing</a></li>
                                            <li><a href="#">Men’s Clothing</a></li>
                                            <li><a href="#">Phones & Accessories</a></li>
                                            <li><a href="#">Jewelry & Watches</a></li>
                                            <li><a href="#">Bags & Shoes</a></li>
                                        </ul>
                                        <hr class="hidden-md hidden-lg">
                                    </div>
                                </div>
                                <div class="row hidden-sm hidden-xs">

                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    <br>




    <!--<style>
        .category-header {
            max-width:200px;
        }
      }
        .hover a {
            color:red;
            border:1px solid red;
            border-bottom:0
        }
        .custom-menu {
            display:none;
        }
        .hover .category-nav {
            display:block;
        }
    </style>-->


    <!-- menu -->
    <div id="navigation" style="background-color: #007576;">
        <div id="responsive-nav">
            <div class="category-nav show-on-click">
                <a class="category-header btn btn-outline-dark">Categorias&nbsp <i class="fa fa-bars" aria-hidden="true" style="color: white;"></i></a>
                <ul class="category-list">
                    <li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Acabamentos e Cabine<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Acabamentos e Cabine</h3></li><li ><a href="/search?nome=Alma de Aço">Alma de Aço</a></li><li ><a href="/search?nome=Espelhos Retrovisores e Componentes">Espelhos Retrovisores e Componentes</a></li><li ><a href="/search?nome=Volante">Volante</a></li><li ><a href="/search?nome=Estribo">Estribo</a></li><li ><a href="/search?nome=Grades Frontal e Componentes">Grades Frontal e Componentes</a></li><li ><a href="/search?nome=Parachoques e Componentes">Parachoques e Componentes</a></li><li ><a href="/search?nome=Portas Cabine">Portas Cabine</a></li><li ><a href="/search?nome=Tapa Sol ">Tapa Sol </a></li><li ><a href="/search?nome=Paralama e Componentes">Paralama e Componentes</a></li><li ><a href="/search?nome=Parabarro">Parabarro</a></li><li ><a href="/search?nome=Parabrisa">Parabrisa</a></li><li ><a href="/search?nome=Defletores">Defletores</a></li><li ><a href="/search?nome=Máquinas de Vidro">Máquinas de Vidro</a></li><li ><a href="/search?nome=Válvulas Suspensão">Válvulas Suspensão</a></li><li ><a href="/search?nome=Componentes de Farol">Componentes de Farol</a></li><li ><a href="/search?nome=Escovas">Escovas</a></li><li ><a href="/search?nome=Tapassol">Tapassol</a></li><li ><a href="/search?nome=Cilindro de Basculamento">Cilindro de Basculamento</a></li><li ><a href="/search?nome=Válvulas de portas">Válvulas de portas</a></li><li ><a href="/search?nome=Válvulas Nivelamento de Cabine">Válvulas Nivelamento de Cabine</a></li><li ><a href="/search?nome=Cabine e Componentes">Cabine e Componentes</a></li><li ><a href="/search?nome=Ponteiras (Parachoques Laterais) e Componentes">Ponteiras (Parachoques Laterais) e Componentes</a></li><li ><a href="/search?nome=Coxim Cabine">Coxim Cabine</a></li><li ><a href="/search?nome=Fechaduras e Travas">Fechaduras e Travas</a></li><li ><a href="/search?nome=Bancos e Componentes">Bancos e Componentes</a></li><li ><a href="/search?nome=Dobradiças">Dobradiças</a></li><li ><a href="/search?nome=Cilindros e chaves">Cilindros e chaves</a></li><li ><a href="/search?nome=Palhetas limpador parabrisa">Palhetas limpador parabrisa</a></li><li ><a href="/search?nome=Borrachas vedação">Borrachas vedação</a></li><li ><a href="/search?nome=Puxadores">Puxadores</a></li><li ><a href="/search?nome=Acabamentos, Tapetes e Carpetes">Acabamentos, Tapetes e Carpetes</a></li><li ><a href="/search?nome=Portas e componentes">Portas e componentes</a></li><li ><a href="/search?nome=Lente de Vidro">Lente de Vidro</a></li><li ><a href="/search?nome=Lente para Faróis">Lente para Faróis</a></li><li ><a href="/search?nome=Vidros Laterais">Vidros Laterais</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Acessórios<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Acessórios</h3></li><li ><a href="/search?nome=Moldura do Farol">Moldura do Farol</a></li><li ><a href="/search?nome=Capa Protetora">Capa Protetora</a></li><li ><a href="/search?nome=Bagageiro">Bagageiro</a></li><li ><a href="/search?nome=Itens de Segurança">Itens de Segurança</a></li><li ><a href="/search?nome=Engate de Tração">Engate de Tração</a></li><li ><a href="/search?nome=Tacômetro">Tacômetro</a></li><li ><a href="/search?nome=Longarina">Longarina</a></li><li ><a href="/search?nome=Bumper">Bumper</a></li><li ><a href="/search?nome=Alargador de Paralamas">Alargador de Paralamas</a></li><li ><a href="/search?nome=Extensor">Extensor</a></li><li ><a href="/search?nome=Extintores de Incêndio">Extintores de Incêndio</a></li><li ><a href="/search?nome=Produtos de Limpeza">Produtos de Limpeza</a></li><li ><a href="/search?nome=Santo Antônio">Santo Antônio</a></li><li ><a href="/search?nome=Capa de Estepe">Capa de Estepe</a></li><li ><a href="/search?nome=Rampa para Caçamba">Rampa para Caçamba</a></li><li ><a href="/search?nome=Cinto de Segurança">Cinto de Segurança</a></li><li ><a href="/search?nome=Rack do Teto">Rack do Teto</a></li><li ><a href="/search?nome=Suporte de Bicicleta">Suporte de Bicicleta</a></li><li ><a href="/search?nome=Olhal">Olhal</a></li><li ><a href="/search?nome=Cintas e Cordas">Cintas e Cordas</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Agropet<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Agropet</h3></li><li ><a href="/search?nome=Agropet">Agropet</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Autopeças<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Autopeças</h3></li><li ><a href="/search?nome=Terminal">Terminal</a></li><li ><a href="/search?nome=Eixo Expansor ">Eixo Expansor </a></li><li ><a href="/search?nome=Faróis">Faróis</a></li><li ><a href="/search?nome=Bombas">Bombas</a></li><li ><a href="/search?nome=Bombas de Direção Hidraulica">Bombas de Direção Hidraulica</a></li><li ><a href="/search?nome=Cubos de Roda">Cubos de Roda</a></li><li ><a href="/search?nome=Eixo Dianteiro">Eixo Dianteiro</a></li><li ><a href="/search?nome=Eixo Traseiro">Eixo Traseiro</a></li><li ><a href="/search?nome=Rolamentos de Rodas">Rolamentos de Rodas</a></li><li ><a href="/search?nome=Terminal">Terminal</a></li><li ><a href="/search?nome=Baterias Caminhões e Onibus">Baterias Caminhões e Onibus</a></li><li ><a href="/search?nome=Baterias Carros e Motos">Baterias Carros e Motos</a></li><li ><a href="/search?nome=Válvulas">Válvulas</a></li><li ><a href="/search?nome=Componentes e Peças ">Componentes e Peças </a></li><li ><a href="/search?nome=Calotas">Calotas</a></li><li ><a href="/search?nome=Parte Elétrica">Parte Elétrica</a></li><li ><a href="/search?nome=Painel e Instrumentos">Painel e Instrumentos</a></li><li ><a href="/search?nome=Acessórios">Acessórios</a></li><li ><a href="/search?nome=Acessórios de Baterias ">Acessórios de Baterias </a></li><li ><a href="/search?nome=Acessórios de Radiador">Acessórios de Radiador</a></li><li ><a href="/search?nome=Bomba de Água">Bomba de Água</a></li><li ><a href="/search?nome=Aparelho Teste de Bateria">Aparelho Teste de Bateria</a></li><li ><a href="/search?nome=Bomba de Combustível">Bomba de Combustível</a></li><li ><a href="/search?nome=Bomba de Óleo">Bomba de Óleo</a></li><li ><a href="/search?nome=Caixas, Carcaças e Componentes">Caixas, Carcaças e Componentes</a></li><li ><a href="/search?nome=Carburador">Carburador</a></li><li ><a href="/search?nome=Escapamento e Componentes">Escapamento e Componentes</a></li><li ><a href="/search?nome=Lanternas">Lanternas</a></li><li ><a href="/search?nome=Limpador de Parabrisa">Limpador de Parabrisa</a></li><li ><a href="/search?nome=Tampas">Tampas</a></li><li ><a href="/search?nome=Tanques">Tanques</a></li><li ><a href="/search?nome=Outros ML não vai">Outros ML não vai</a></li><li ><a href="/search?nome=Chave Ignição">Chave Ignição</a></li><li ><a href="/search?nome=Kit Revisão Carro">Kit Revisão Carro</a></li><li ><a href="/search?nome=Buchas, Coxins e Batentes">Buchas, Coxins e Batentes</a></li><li ><a href="/search?nome=Buchas, Rebites, Parafusos, Porcas e Arruelas">Buchas, Rebites, Parafusos, Porcas e Arruelas</a></li><li ><a href="/search?nome=Abraçadeiras e presilhas">Abraçadeiras e presilhas</a></li><li ><a href="/search?nome=Ar Condicionado">Ar Condicionado</a></li><li ><a href="/search?nome=Barra Estabilizadora">Barra Estabilizadora</a></li><li ><a href="/search?nome=Barra Haste Reação">Barra Haste Reação</a></li><li ><a href="/search?nome=Bomba Hidráulica Cabine">Bomba Hidráulica Cabine</a></li><li ><a href="/search?nome=Câmbio">Câmbio</a></li><li ><a href="/search?nome=Elétrica">Elétrica</a></li><li ><a href="/search?nome=Embuchamento">Embuchamento</a></li><li ><a href="/search?nome=Hidráulica">Hidráulica</a></li><li ><a href="/search?nome=Interruptores">Interruptores</a></li><li ><a href="/search?nome=Painel Frontal">Painel Frontal</a></li><li ><a href="/search?nome=Peças Carreta">Peças Carreta</a></li><li ><a href="/search?nome=Sistema Hidráulico">Sistema Hidráulico</a></li><li ><a href="/search?nome=Suporte Estepe">Suporte Estepe</a></li><li ><a href="/search?nome=Suportes">Suportes</a></li><li ><a href="/search?nome=Cabos">Cabos</a></li><li ><a href="/search?nome=Quinta Roda">Quinta Roda</a></li><li ><a href="/search?nome=Chave de Seta">Chave de Seta</a></li><li ><a href="/search?nome=Carroceria">Carroceria</a></li><li ><a href="/search?nome=Longarinas e Chassis">Longarinas e Chassis</a></li><li ><a href="/search?nome=Suspensão Cabine">Suspensão Cabine</a></li><li ><a href="/search?nome=Rodas e acessórios">Rodas e acessórios</a></li><li ><a href="/search?nome=Bicos Unidades Injetoras">Bicos Unidades Injetoras</a></li><li ><a href="/search?nome=Pedais">Pedais</a></li><li ><a href="/search?nome=Retentores Rodas">Retentores Rodas</a></li><li ><a href="/search?nome=Junta homocinética">Junta homocinética</a></li><li ><a href="/search?nome=Peças Automotivas">Peças Automotivas</a></li><li ><a href="/search?nome=Pé mecânico">Pé mecânico</a></li><li ><a href="/search?nome=Airbag">Airbag</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Baterias<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Baterias</h3></li><li ><a href="/search?nome=Carregadores de Baterias">Carregadores de Baterias</a></li><li ><a href="/search?nome=Casa Cardão não vai">Casa Cardão não vai</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Caixa de Câmbio e Transmissão<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Caixa de Câmbio e Transmissão</h3></li><li ><a href="/search?nome=Peças diferencial">Peças diferencial</a></li><li ><a href="/search?nome=Válvulas de câmbio">Válvulas de câmbio</a></li><li ><a href="/search?nome=Alavancas e manoplas">Alavancas e manoplas</a></li><li ><a href="/search?nome=Juntas e Reparos Câmbio">Juntas e Reparos Câmbio</a></li><li ><a href="/search?nome=Retentores Câmbio">Retentores Câmbio</a></li><li ><a href="/search?nome=Rolamentos Caixa Câmbio">Rolamentos Caixa Câmbio</a></li><li ><a href="/search?nome=Sensores Câmbio">Sensores Câmbio</a></li><li ><a href="/search?nome=Peças de Câmbio">Peças de Câmbio</a></li><li ><a href="/search?nome=Engrenagem Câmbio">Engrenagem Câmbio</a></li><li ><a href="/search?nome=Carcaça câmbio">Carcaça câmbio</a></li><li ><a href="/search?nome=Anéis sincronizadores e sincronização">Anéis sincronizadores e sincronização</a></li><li ><a href="/search?nome=Eixo piloto motriz">Eixo piloto motriz</a></li><li ><a href="/search?nome=Engrenagem diferencial">Engrenagem diferencial</a></li><li ><a href="/search?nome=Rolamento diferencial">Rolamento diferencial</a></li><li ><a href="/search?nome=Retentor diferencial">Retentor diferencial</a></li><li ><a href="/search?nome=Diferencial completo">Diferencial completo</a></li><li ><a href="/search?nome=Contra eixo câmbio">Contra eixo câmbio</a></li><li ><a href="/search?nome=Caixa câmbio completa">Caixa câmbio completa</a></li><li ><a href="/search?nome=Semi eixo">Semi eixo</a></li><li ><a href="/search?nome=Módulo eletrônico transmissão">Módulo eletrônico transmissão</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Cardan<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Cardan</h3></li><li ><a href="/search?nome=Garfo">Garfo</a></li><li ><a href="/search?nome=Engraxadeira">Engraxadeira</a></li><li ><a href="/search?nome=Cruzeta">Cruzeta</a></li><li ><a href="/search?nome=Cardan Completo">Cardan Completo</a></li><li ><a href="/search?nome=Suporte Cardan">Suporte Cardan</a></li><li ><a href="/search?nome=Flange">Flange</a></li><li ><a href="/search?nome=Ponteira">Ponteira</a></li><li ><a href="/search?nome=Casa Cardão Vai">Casa Cardão Vai</a></li><li ><a href="/search?nome=Terminal Cardan">Terminal Cardan</a></li><li ><a href="/search?nome=Luva Cardan">Luva Cardan</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Colas e adesivos<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Colas e adesivos</h3></li><li ><a href="/search?nome=Colas e adesivos">Colas e adesivos</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Diversos<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Diversos</h3></li><li ><a href="/search?nome=Diversos">Diversos</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Elétrica<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Elétrica</h3></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Embreagem<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Embreagem</h3></li><li ><a href="/search?nome=Platô de Embreagem">Platô de Embreagem</a></li><li ><a href="/search?nome=Kit de Embreagem Leves">Kit de Embreagem Leves</a></li><li ><a href="/search?nome=Luvas">Luvas</a></li><li ><a href="/search?nome=Peças de Embreagem">Peças de Embreagem</a></li><li ><a href="/search?nome=Disco de Embreagem">Disco de Embreagem</a></li><li ><a href="/search?nome=Kit Embreagem Caminhões e Onibus">Kit Embreagem Caminhões e Onibus</a></li><li ><a href="/search?nome=Rolamento e Componentes">Rolamento e Componentes</a></li><li ><a href="/search?nome=Cilindro de Embreagem">Cilindro de Embreagem</a></li><li ><a href="/search?nome=Rolamentos de Embreagem">Rolamentos de Embreagem</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Ferragens<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Ferragens</h3></li><li ><a href="/search?nome=Ferragens">Ferragens</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Ferramentas<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Ferramentas</h3></li><li ><a href="/search?nome=Ferramentas">Ferramentas</a></li><li ><a href="/search?nome=Cavaletes">Cavaletes</a></li><li ><a href="/search?nome=Macacos">Macacos</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Filtros<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Filtros</h3></li><li ><a href="/search?nome=Filtro de Ar">Filtro de Ar</a></li><li ><a href="/search?nome=Filtro de Ar Condicionado">Filtro de Ar Condicionado</a></li><li ><a href="/search?nome=Filtro de Combustível">Filtro de Combustível</a></li><li ><a href="/search?nome=Outros Filtros">Outros Filtros</a></li><li ><a href="/search?nome=Filtro de Uréia (Arla)">Filtro de Uréia (Arla)</a></li><li ><a href="/search?nome=Componentes ARLA">Componentes ARLA</a></li><li ><a href="/search?nome=Filtro de Óleo Lubrificante ">Filtro de Óleo Lubrificante </a></li><li ><a href="/search?nome=Filtro secador APU">Filtro secador APU</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Freios<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Freios</h3></li><li ><a href="/search?nome=Junta">Junta</a></li><li ><a href="/search?nome=Kits de Freio">Kits de Freio</a></li><li ><a href="/search?nome=Disco de Freio">Disco de Freio</a></li><li ><a href="/search?nome=Lona de Freio">Lona de Freio</a></li><li ><a href="/search?nome=Pastilhas de Freio">Pastilhas de Freio</a></li><li ><a href="/search?nome=Pontuva e capa">Pontuva e capa</a></li><li ><a href="/search?nome=Sapatas de Freio">Sapatas de Freio</a></li><li ><a href="/search?nome=Válvulas de Freio">Válvulas de Freio</a></li><li ><a href="/search?nome=Pinças de Freio">Pinças de Freio</a></li><li ><a href="/search?nome=Compressor do Ar do Freio e Componentes">Compressor do Ar do Freio e Componentes</a></li><li ><a href="/search?nome=Cuíca de Freio e Componentes">Cuíca de Freio e Componentes</a></li><li ><a href="/search?nome=Fluido de freios">Fluido de freios</a></li><li ><a href="/search?nome=Freio Motor">Freio Motor</a></li><li ><a href="/search?nome=Componentes Pinça de Freio">Componentes Pinça de Freio</a></li><li ><a href="/search?nome=Catraca de Freio">Catraca de Freio</a></li><li ><a href="/search?nome=Tambor de freio">Tambor de freio</a></li><li ><a href="/search?nome=Componentes de Freio">Componentes de Freio</a></li><li ><a href="/search?nome=Flexíveis">Flexíveis</a></li><li ><a href="/search?nome=Sensores de freio">Sensores de freio</a></li><li ><a href="/search?nome=Cabo do Freio de Mão">Cabo do Freio de Mão</a></li><li ><a href="/search?nome=Cilindro Reservatório Ar">Cilindro Reservatório Ar</a></li><li ><a href="/search?nome=Cilindro Mestre">Cilindro Mestre</a></li><li ><a href="/search?nome=Maneta freio de mão estacionamento">Maneta freio de mão estacionamento</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Hidráulica<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Hidráulica</h3></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Motor<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Motor</h3></li><li ><a href="/search?nome=Virabrequim">Virabrequim</a></li><li ><a href="/search?nome=Vela Aquecedora">Vela Aquecedora</a></li><li ><a href="/search?nome=Válvulas e Comando de Válvulas">Válvulas e Comando de Válvulas</a></li><li ><a href="/search?nome=Polias">Polias</a></li><li ><a href="/search?nome=Acessórios de Radiador">Acessórios de Radiador</a></li><li ><a href="/search?nome=Turbina e Componentes">Turbina e Componentes</a></li><li ><a href="/search?nome=Reservatório Expansão">Reservatório Expansão</a></li><li ><a href="/search?nome=Válvulas">Válvulas</a></li><li ><a href="/search?nome=Tensores">Tensores</a></li><li ><a href="/search?nome=Juntas">Juntas</a></li><li ><a href="/search?nome=Kit Troca de Óleo de Motor e Filtros">Kit Troca de Óleo de Motor e Filtros</a></li><li ><a href="/search?nome=Radiadores">Radiadores</a></li><li ><a href="/search?nome=Velas de Ignição">Velas de Ignição</a></li><li ><a href="/search?nome=Mangueiras e Tubulações">Mangueiras e Tubulações</a></li><li ><a href="/search?nome=Cilindro de Acionamento">Cilindro de Acionamento</a></li><li ><a href="/search?nome=Retentores Motor">Retentores Motor</a></li><li ><a href="/search?nome=Coxim e Suporte Motor">Coxim e Suporte Motor</a></li><li ><a href="/search?nome=Hélice e Embreagem Viscosa">Hélice e Embreagem Viscosa</a></li><li ><a href="/search?nome=Coletores">Coletores</a></li><li ><a href="/search?nome=Componentes de Ignição">Componentes de Ignição</a></li><li ><a href="/search?nome=Anéis de segmento">Anéis de segmento</a></li><li ><a href="/search?nome=Sensores">Sensores</a></li><li ><a href="/search?nome=Bronzinas, Casquilhos e Arruela de Encosto">Bronzinas, Casquilhos e Arruela de Encosto</a></li><li ><a href="/search?nome=Camisa Cilindro">Camisa Cilindro</a></li><li ><a href="/search?nome=Válvula Termostática">Válvula Termostática</a></li><li ><a href="/search?nome=Cremalheira e Volante do Motor">Cremalheira e Volante do Motor</a></li><li ><a href="/search?nome=Bomba Injetora">Bomba Injetora</a></li><li ><a href="/search?nome=Kits de Motor">Kits de Motor</a></li><li ><a href="/search?nome=Pistões">Pistões</a></li><li ><a href="/search?nome=Retentores Motor">Retentores Motor</a></li><li ><a href="/search?nome=Biela">Biela</a></li><li ><a href="/search?nome=Engrenagens">Engrenagens</a></li><li ><a href="/search?nome=Tomada de Força">Tomada de Força</a></li><li ><a href="/search?nome=Correias e correntes">Correias e correntes</a></li><li ><a href="/search?nome=Alternador e Componentes">Alternador e Componentes</a></li><li ><a href="/search?nome=Válvula de Retenção e de Alívio">Válvula de Retenção e de Alívio</a></li><li ><a href="/search?nome=Válvulas Solenoide">Válvulas Solenoide</a></li><li ><a href="/search?nome=Vareta nível óleo">Vareta nível óleo</a></li><li ><a href="/search?nome=Motor de Partida Arranque e Bobinas">Motor de Partida Arranque e Bobinas</a></li><li ><a href="/search?nome=Tampas motor">Tampas motor</a></li><li ><a href="/search?nome=Módulo Eletrônico Motor">Módulo Eletrônico Motor</a></li><li ><a href="/search?nome=Cabeçote">Cabeçote</a></li><li ><a href="/search?nome=Motor">Motor</a></li><li ><a href="/search?nome=Bloco motor">Bloco motor</a></li><li ><a href="/search?nome=Flauta distribuidor de combustível">Flauta distribuidor de combustível</a></li><li ><a href="/search?nome=Intercooler radiador de ar">Intercooler radiador de ar</a></li><li ><a href="/search?nome=Trocador de calor">Trocador de calor</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Óleo, aditivos e fluidos<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Óleo, aditivos e fluidos</h3></li><li ><a href="/search?nome=Graxa Rolamento">Graxa Rolamento</a></li><li ><a href="/search?nome=Kit 3 Litros de Óleo de Motor">Kit 3 Litros de Óleo de Motor</a></li><li ><a href="/search?nome=Kit 4 Litros de Óleo de Motor">Kit 4 Litros de Óleo de Motor</a></li><li ><a href="/search?nome=Óleo de Motor">Óleo de Motor</a></li><li ><a href="/search?nome=Óleo Hidráulico">Óleo Hidráulico</a></li><li ><a href="/search?nome=Caixa Fechada Óleo de Motor">Caixa Fechada Óleo de Motor</a></li><li ><a href="/search?nome=Óleo Cambio e Transmissão">Óleo Cambio e Transmissão</a></li><li ><a href="/search?nome=Galão de 20 Litros Óleo de Motor Diesel">Galão de 20 Litros Óleo de Motor Diesel</a></li><li ><a href="/search?nome=Graxa Chassi">Graxa Chassi</a></li><li ><a href="/search?nome=Carter Óleo">Carter Óleo</a></li><li ><a href="/search?nome=Aditivo de radiador">Aditivo de radiador</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Pinturas<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Pinturas</h3></li><li ><a href="/search?nome=Pintura">Pintura</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Pneus<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Pneus</h3></li><li ><a href="/search?nome=Pneus Aro 13">Pneus Aro 13</a></li><li ><a href="/search?nome=Pneus Aro 14">Pneus Aro 14</a></li><li ><a href="/search?nome=Pneus Aro 15">Pneus Aro 15</a></li><li ><a href="/search?nome=Pneus Aro 16">Pneus Aro 16</a></li><li ><a href="/search?nome=Pneus de Moto">Pneus de Moto</a></li><li ><a href="/search?nome=Pneus Aro 17">Pneus Aro 17</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Serviços Automotivos<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Serviços Automotivos</h3></li><li ><a href="/search?nome=Serviços de Motor">Serviços de Motor</a></li><li ><a href="/search?nome=Serviço de Leva e Traz">Serviço de Leva e Traz</a></li><li ><a href="/search?nome=Serviços de Suspensão">Serviços de Suspensão</a></li><li ><a href="/search?nome=Serviços de Limpeza">Serviços de Limpeza</a></li><li ><a href="/search?nome=Serviços de Cambio">Serviços de Cambio</a></li><li ><a href="/search?nome=Manutenção Preventiva">Manutenção Preventiva</a></li><li ><a href="/search?nome=Serviços de Pneus">Serviços de Pneus</a></li><li ><a href="/search?nome=Serviços de Freios">Serviços de Freios</a></li><li ><a href="/search?nome=Serviços de Direção">Serviços de Direção</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li><li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Suspensão e direção<i class="fa fa-angle-right"></i></a>
                        <div class="custom-menu">
                            <div class="row">
                                <div class="col-md-8" style=" line-height:8px !important;">
                                    <ul class="list-links">
                                        <li ><h3 class="list-links-title">Suspensão e direção</h3></li><li ><a href="/search?nome=Peças Suspensão">Peças Suspensão</a></li><li ><a href="/search?nome=Amortecedor a gás tampa">Amortecedor a gás tampa</a></li><li ><a href="/search?nome=Amortecedores">Amortecedores</a></li><li ><a href="/search?nome=Componentes de Direção">Componentes de Direção</a></li><li ><a href="/search?nome=Molas">Molas</a></li><li ><a href="/search?nome=Balança">Balança</a></li><li ><a href="/search?nome=Fole pneumático">Fole pneumático</a></li><li ><a href="/search?nome=Manga de Eixo">Manga de Eixo</a></li><li ><a href="/search?nome=Bandejas e braço oscilante">Bandejas e braço oscilante</a></li><li ><a href="/search?nome=Caixa de direção">Caixa de direção</a></li>        </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div id="responsive-nav">
            <ul class="menu-list" >
                <div class="container">
                    <div class="row">
                        <li class="dropdown" ><a href="/search?nome=Acabamentos e Cabine" class="text-menu">Acabamentos e Cabine</a>
                            <div class="dropdown-content">

                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Volante">Volante</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=14623&ordem=1"><a href="/p/14623">VOLANTE DIRECAO PRETO COM ESTRIA SEM EMBLEMA MERCEDES 470MM</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=14870&ordem=1"><a href="/p/14870">VOLANTE DIRECAO SEM REGULAGEM ESPORTIVO 450MM PARA SCANIA 112</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=14874&ordem=1"><a href="/p/14874">VOLANTE DIRECAO 450MM CINZA MB MERCEDES BENZ ATEGO AXOR</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Parachoques e Componentes">Parachoques e Componentes</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=6943&ordem=1"><a href="/p/6943">PROTETOR PARACHOQUE CENTRAL LE ESQUERDO IVECO STRALIS ATE 2007 </a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=6944&ordem=1"><a href="/p/6944">PROTETOR PARACHOQUE CENTRAL LD DIREITO IVECO STRALIS ATE 2007 </a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7247&ordem=1"><a href="/p/7247">PARACHOQUE DIANTEIRO CENTRAL VOLVO FH12 FM12 94 A 2003</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Parabrisa">Parabrisa</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7084&ordem=1"><a href="/p/7084">ALCA PARABRISA MB MERCEDES BENZ 1938S 1944S AXOR ATEGO</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7229&ordem=1"><a href="/p/7229">MOLDURA PARABRISA CENTRAL VOLVO VM TODOS</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7230&ordem=1"><a href="/p/7230">MOLDURA PARABRIS VOLVO VM TODOSA ESQUERDO INFERIOR </a></li>

                                    </ul> <hr>
                                </div>

                                <!--<div class="col-sm-6">
                                    <ul class="list-links">
                                        <img src="<?= Url::to('@assets/'); ?>img/banner-teste.jpg"  class="img-ht img-fluid rounded acende img-responsive" style="width:80%; height: 100%" alt="imagem responsiva">
                                    </ul> <hr>
                                </div>-->


                            </div>
                        </li><li class="dropdown" ><a href="/search?nome=Autopeças" class="text-menu">Autopeças</a>
                            <div class="dropdown-content">

                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Terminal">Terminal</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=6966&ordem=1"><a href="/p/6966">TAMPA SILENCIOSO PARA SCANIA SERIE 4</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7203&ordem=1"><a href="/p/7203">SUPORTE SILENCIOSO LATERAL PARA SCANIA SERIE 4/ SERIE 5</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=10031&ordem=1"><a href="/p/10031">TERMINAL DIFERENCIAL 46 ESTRIAS ACO CATERPILLAR CAT 924</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Eixo Expansor ">Eixo Expansor </a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7904&ordem=1"><a href="/p/7904">EIXO EXPANSORES S FREIO AR MB MERCEDES BENZ </a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7905&ordem=1"><a href="/p/7905">EIXO EXPANSORES S FREIO AR MB MERCEDES BENZ </a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=8317&ordem=1"><a href="/p/8317">EIXO EXPANSORES S FREIO AR CARRETAS FACCHINI (1999...)</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Bombas">Bombas</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=10152&ordem=1"><a href="/p/10152">FLEXIVEL BOMBA INJETORA MB MERCEDES OM366 MOTOR TRASEIRO</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=10153&ordem=1"><a href="/p/10153">FLEXIVEL BOMBA INJETORA MB MERCEDES OM366 MOTOR TRASEIRO</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=10192&ordem=1"><a href="/p/10192">REPARO VALVULA BOMBA OLEO MB MERCEDES BENZ O 366/ 709/ 912</a></li>

                                    </ul> <hr>
                                </div>
                                <!--<div class="col-sm-6">
                                    <ul class="list-links">
                                        <img src="<?= Url::to('@assets/'); ?>img/banner-menu2.jpg"  class="img-ht img-fluid rounded acende img-responsive" style="width:80%; height: 100%" alt="imagem responsiva">
                                    </ul> <hr>
                                </div>-->


                            </div>
                        </li><li class="dropdown" ><a href="/search?nome=Cardan" class="text-menu">Cardan</a>
                            <div class="dropdown-content">

                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Garfo">Garfo</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=9041&ordem=1"><a href="/p/9041">GARFO CARDAN DE SOLDAR ACO APLICACAO DIVERSAS BASCULANTES</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=9219&ordem=1"><a href="/p/9219">GARFO CARDAN DE SOLDAR VOLVO B 58 ONIBUS URBANO RODOVIARIO</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=9220&ordem=1"><a href="/p/9220">GARFO CARDAN DE SOLDAR TOYOTA PICK-UP BANDEIRANTE</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Flange">Flange</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=9010&ordem=1"><a href="/p/9010">FLANGE CONICA JUNTA UNIVERSAL </a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=9011&ordem=1"><a href="/p/9011">FLANGE CONICA JUNTA UNIVERSAL </a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=9086&ordem=1"><a href="/p/9086">FLANGE CAMBIO  MB MERCEDES BENZ SPRINTER 310/ 311/ 312</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Ponteira">Ponteira</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=9099&ordem=1"><a href="/p/9099">PONTEIRA CARDAN TOYOTA JEEP/ PICK-UP/ RURAL COM ROSCA</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=9120&ordem=1"><a href="/p/9120">PONTEIRA CARDAN GM GENERAL MOTORS C 10/ C 14 FORD TRASEIRA</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=9126&ordem=1"><a href="/p/9126">PONTEIRA CARDAN GM GENERAL MOTORS OPALA FORD TRASEIRA</a></li>

                                    </ul> <hr>
                                </div>
                                <!--<div class="col-sm-6">
                                    <ul class="list-links">
                                        <img src="<?= Url::to('@assets/'); ?>img/banner-teste.jpg"  class="img-ht img-fluid rounded acende img-responsive" style="width:80%; height: 100%" alt="imagem responsiva">
                                    </ul> <hr>
                                </div>-->


                            </div>
                        </li><li class="dropdown" ><a href="/search?nome=Embreagem" class="text-menu">Embreagem</a>
                            <div class="dropdown-content">

                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Platô de Embreagem">Platô de Embreagem</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=15261&ordem=1"><a href="/p/15261">ROLAMENTO EMBREAGEM COM CUBO MB MERCEDES BENZ 1722 1718 O500</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=15665&ordem=1"><a href="/p/15665">REPARO PLATO EMBREAGEM JOGO 3 PECAS MB MERCEDES BENZ L608</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=15666&ordem=1"><a href="/p/15666">REPARO PLATO EMBREAGEM MB MERCEDES BENZ 1111 1113 T-03A</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Peças de Embreagem">Peças de Embreagem</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7844&ordem=1"><a href="/p/7844">REPARO SERVO EMBREAGEM MB MERCEDES BENZ 1938/ 1944/ 2638</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=8295&ordem=1"><a href="/p/8295">CONEXAO SERVO EMBREAGEM VW VOLKSWAGEN CONSTELLATION</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=8345&ordem=1"><a href="/p/8345">CANECA SERVO EMBREAGEM MB MERCEDES BENZ 1632 1634 1725 1728</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Cilindro de Embreagem">Cilindro de Embreagem</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7848&ordem=1"><a href="/p/7848">CILINDRO EMBREAGEM MESTRE MB MERCEDES BENZ 608D/ 708D/ 708E</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7849&ordem=1"><a href="/p/7849">CILINDRO EMBREAGEM AUXILIAR MB MERCEDES BENZ 608D/ 708D 708E</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7850&ordem=1"><a href="/p/7850">CILINDRO EMBREAGEM MESTRE MB MERCEDES BENZ 1214/ 1218/ 1414</a></li>

                                    </ul> <hr>
                                </div>
                                <!--<div class="col-sm-6">
                                    <ul class="list-links">
                                        <img src="<?= Url::to('@assets/'); ?>img/banner-teste.jpg"  class="img-ht img-fluid rounded acende img-responsive" style="width:80%; height: 100%" alt="imagem responsiva">
                                    </ul> <hr>
                                </div>-->


                            </div>
                        </li><li class="dropdown" ><a href="/search?nome=Ferramentas" class="text-menu">Ferramentas</a>
                            <div class="dropdown-content">

                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Ferramentas">Ferramentas</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=24499&ordem=1"><a href="/p/24499">PNEU PARA CARRINHO DE MÃO LEVORIN 3,25 X 8</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=24501&ordem=1"><a href="/p/24501">PNEU PARA CARRINHO DE MÃO STARFER 3,25 X 8</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=24502&ordem=1"><a href="/p/24502">PNEU PARA CARRINHO DE MÃO STARFER 3,50 X 8</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Cavaletes">Cavaletes</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=11925&ordem=1"><a href="/p/11925">- CAVALETE 30 TONELADAS</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Macacos">Macacos</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=231505&ordem=2"><a href="/p/231505">MACACO MOLEJO HIDROPNEUMÁTICO (ACIONAMENTO MANUAL E AR) 32 TONELADAS</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=240060&ordem=2"><a href="/p/240060">MACACO MOLEJO (MANUAL) 32 TONELADAS</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=240061&ordem=2"><a href="/p/240061">MACACO MOLEJO PNEUMATICO (SÓ AR) 32 TONELADAS</a></li>

                                    </ul> <hr>
                                </div>
                               <!-- <div class="col-sm-6">
                                    <ul class="list-links">
                                        <img src="<?= Url::to('@assets/'); ?>img/banner-teste.jpg"  class="img-ht img-fluid rounded acende img-responsive" style="width:80%; height: 100%" alt="imagem responsiva">
                                    </ul> <hr>
                                </div>-->


                            </div>
                        </li><li class="dropdown" ><a href="/search?nome=Filtros" class="text-menu">Filtros</a>
                            <div class="dropdown-content">

                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Filtro de Ar">Filtro de Ar</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=1072&ordem=1"><a href="/p/1072">FILTRO DE AR MANN</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=1075&ordem=1"><a href="/p/1075">FILTRO DE AR MANN</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=1081&ordem=1"><a href="/p/1081">FILTRO DE AR MANN</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Filtro de Ar Condicionado">Filtro de Ar Condicionado</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=1342&ordem=1"><a href="/p/1342">FILTRO DE AR CONDICIONADO MANN</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=1343&ordem=1"><a href="/p/1343">FILTRO DE AR CONDICIONADO MANN</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=1347&ordem=1"><a href="/p/1347">FILTRO DE AR CONDICIONADO MANN</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Filtro de Combustível">Filtro de Combustível</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=1065&ordem=1"><a href="/p/1065">FILTRO DE COMBUSTÍVEL MANN</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=1066&ordem=1"><a href="/p/1066">FILTRO DE COMBUSTÍVEL MANN</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=1067&ordem=1"><a href="/p/1067">FILTRO DE COMBUSTÍVEL MANN</a></li>

                                    </ul> <hr>
                                </div>

                                <!--<div class="col-sm-6">
                                    <ul class="list-links">
                                        <img src="<?= Url::to('@assets/'); ?>img/banner-teste.jpg"  class="img-ht img-fluid rounded acende img-responsive" style="width:80%; height: 100%" alt="imagem responsiva">
                                    </ul> <hr>
                                </div>-->


                            </div>
                        </li><li class="dropdown" ><a href="/search?nome=Freios" class="text-menu">Freios</a>
                            <div class="dropdown-content">

                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Compressor do Ar do Freio e Componentes">Compressor do Ar do Freio e Componentes</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7750&ordem=1"><a href="/p/7750">BIELA COMPRESSOR A MB MERCEDES BENZ OM314 OM352R ANTIGA</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7751&ordem=1"><a href="/p/7751">BIELA COMPRESSOR AR MB MERCEDES BENZ OM352 OM366 MODERNA</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7752&ordem=1"><a href="/p/7752">CABECOTE COMPRESSOR AR MB MERCEDES BENZ OM314 OM321 OM352 MB</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Catraca de Freio">Catraca de Freio</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7906&ordem=1"><a href="/p/7906">CATRACA FREIO MB MERCEDES BENZ 1935/ 1941 MANUAL</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7907&ordem=1"><a href="/p/7907">CATRACA FREIO MB MERCEDES BENZ 1935/ 1941 MANUAL</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7908&ordem=1"><a href="/p/7908">CATRACA FREIO MB MERCEDES BENZ MANUAL </a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Componentes de Freio">Componentes de Freio</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7900&ordem=1"><a href="/p/7900">EXCENTRICO PATIM FREIO MB MERCEDES BENZ O321/ 1111/ 1113 LPO</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7901&ordem=1"><a href="/p/7901">EXCENTRICO PATIM FREIO DIANTEIRO MB MERCEDES BENZ 1313/ 2213</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=7902&ordem=1"><a href="/p/7902">EXCENTRICO PATIM FREIO MB MERCEDES BENZ 1513 1519 0,355</a></li>

                                    </ul> <hr>
                                </div>

                               <!-- <div class="col-sm-6">
                                    <ul class="list-links">
                                        <img src="<?= Url::to('@assets/'); ?>img/banner-teste.jpg"  class="img-ht img-fluid rounded acende img-responsive" style="width:80%; height: 100%" alt="imagem responsiva">
                                    </ul> <hr>
                                </div>-->


                            </div>
                        </li><li class="dropdown" ><a href="/search?nome=Óleo, aditivos e fluidos" class="text-menu">Óleo, aditivos e fluidos</a>
                            <div class="dropdown-content">

                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Óleo Hidráulico">Óleo Hidráulico</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=6892&ordem=1"><a href="/p/6892">ACABAMENTO GRADE LE ESQUERDO INFERIOR VOLVO FH13</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=6893&ordem=1"><a href="/p/6893">ACABAMENTO GRADE INFERIOR LD DIREITA VOLVO FH/FM</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=6929&ordem=1"><a href="/p/6929">TELA GRADE FRONTAL PARA SCANIA SERIE 5 ESQUERDO</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Graxa Chassi">Graxa Chassi</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=14757&ordem=1"><a href="/p/14757">CABO ACELERADOR SISTEMA ACELERACAO COMPLETO MB MERCEDES BENZ</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=14759&ordem=1"><a href="/p/14759">CABO VELOCIMETRO 12150MM MB MERCEDES BENZ </a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=231813&ordem=1"><a href="/p/231813">GRAXA CHASSI BALDE 10 KG RETRAK CALCIO NGLI - 2 </a></li>

                                    </ul> <hr>
                                </div>


                            </div>
                        </li><li class="dropdown" ><a href="/search?nome=Suspensão e direção" class="text-menu">Suspensão e direção</a>
                            <div class="dropdown-content">

                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Peças Suspensão">Peças Suspensão</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=11936&ordem=1"><a href="/p/11936">SUPORTE SUSPENSÃO LADO ESQUERDO VOLVO FH</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=11937&ordem=1"><a href="/p/11937">SUPORTE SUSPENSAO LADO DIREITO VOLVO FH</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=12501&ordem=1"><a href="/p/12501">MANCAL EIXO TANDEM PARA SCANIA S4 SERIE 4 P94 114 124</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Amortecedores">Amortecedores</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=6414&ordem=1"><a href="/p/6414">AMORTECEDOR DIANTEIRO CABINE ALTA BAIXA MB 1938S 1944S</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=6416&ordem=1"><a href="/p/6416">AMORTECEDOR CABINE DIANTEIRO MB AXOR TODOS </a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=6417&ordem=1"><a href="/p/6417">AMORTECEDOR TRASEIRO CABINE MB AXOR</a></li>

                                    </ul> <hr>
                                </div>




                                <div class="col-sm-6">
                                    <ul class="list-links">

                                        <li> <h3 class="list-links-title"><a href="/search?nome=Componentes de Direção">Componentes de Direção</a></h3> </li><br>

                                        <li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=10664&ordem=1"><a href="/p/10664">BARRA DIRECAO MB MERCEDES BENZ 709/ 912/ 914</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=10665&ordem=1"><a href="/p/10665">BARRA DIRECAO EIXO AXIAL CAIXA MB MERCEDES BENZ SPRINTER</a></li><li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="https://www.pecaagora.com/site/get-link?produto_id=10851&ordem=1"><a href="/p/10851">GUARDA PO CAIXA DIRECAO COMPLETA MB MERCEDES BENZ SPRINTER</a></li>

                                    </ul> <hr>
                                </div>
                                <!--<div class="col-sm-6">
                                    <ul class="list-links">
                                        <img src="<?= Url::to('@assets/'); ?>img/banner-teste.jpg"  class="img-ht img-fluid rounded acende img-responsive" style="width:80%; height: 100%" alt="imagem responsiva">
                                    </ul> <hr>
                                </div>-->


                            </div>
                        </li>
                    </div>
                </div>
            </ul>
        </div>

        <!-- menu --></div><br>

<?php
 //echo MenuWidget::widget([]);
?>

    <!-- menu vertical-->


    <!-- menu horizontal-->



    <?php
    /*NavBar::begin(
        [
            'brandLabel' => 'Departamentos',
            'brandUrl' => ".navbar-collapse",
            'brandOptions' => [

                "data-toggle" => "collapse",
                "rel" => "nofollow"
            ],
            'options' => [
                'class' => 'navbar navbar-inverse nav-cats',
            ],
        ]
    );
    echo Menu::widget(
        [
            'options' => ['class' => 'navbar-nav'],
            'items' => Categoria::getArrayMenu(),
        ]
    );
    NavBar::end();*/
    ?>

    <!-- /NAVIGATION -->







    <div class="container">
        <?= Breadcrumbs::widget(
            [
                'encodeLabels' => false,
                'homeLink' => ['label' => Html::a('Página Inicial', Url::to(['site/index']))],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
        <?= Alert::widget() ?>

        <?= $content ?>
    </div>

    <div class="row" style="background-color: #007576">
        <div class="footer-sitemap-col col-sm-2" style="padding: 20px">

        </div>

        <!-- Paginas  -->
        <div class="footer-sitemap-col col-sm-3" style="padding: 20px">
            <p class="h4" style="color: #040100">Páginas do Lojista</p>
            <ul class="list-unstyled" >
                <li><a rel="nofollow" style="color: white" href="<?= Url::to(['/lojista/intro']) ?>">Quero abrir minha Loja</a></li>
                <li><a rel="nofollow" style="color: white" href="<?= Url::to(['/lojista/web']) ?>">Entrar em minha loja</a></li>
                <!--<li><a href="#">Contato</a></li>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Como Funciona</a></li>
                <li><a href="#">Lojas</a></li>
                <li><a href="#">Investidores</a></li>
                <li><a href="#">Marcas</a></li>-->
            </ul>
        </div>
        <div class="footer-sitemap-col col-sm-3" style="padding: 20px">
            <p class="h4" style="color: #040100">Navegação</p>
            <ul class="list-unstyled" style="color: white">
                <li><a style="color: white" href="<?= Url::to(['/auto']) ?>">Categorias</a></li>
                <li><a style="color: white" href="<?= Url::to(['/veiculos/carros']) ?>">Carros</a></li>
                <li><a style="color: white" href="<?= Url::to(['/veiculos/caminhoes']) ?>">Caminhões</a></li>
            </ul>
        </div>

        <div class="footer-sitemap-col col-sm-3" style="padding: 20px">
            <p class="h4" style="color: #040100">Sobre</p>
            <ul class="list-unstyled">
                <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/site/sobre'; ?>">Quem
                        Somos</a></li>
                <li><a style="color: white" rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/site/politicas'; ?>">Políticas
                        de uso</a>
                </li>
                <li><a style="color: white" href="https://pecaagora.zendesk.com/hc/pt-br/articles/206974937-O-que-%C3%A9-Compra-Garantida-e-Como-Funciona-" target="_blank">
                        Compra Garantida</a></li>
            </ul>
        </div>
        <!-- Paginas  -->



    </div>
    <footer class="footer">
        <div class="container">
            <div class="container footer-sitemap hidden-print">
                <div class="footer-sitemap-col col-sm-6">
                    <p class="h4">Formas de Pagamento</p>
                    <ul class="list-unstyled">
                        <li class="pagamento">
                            <img src="<?= Url::to('@assets/'); ?>img/visa.png" style="width: 48px; height: 35px">
                            <img src="<?= Url::to('@assets/'); ?>img/mastercard.png" style="width: 48px; height: 35px">
                            <img src="<?= Url::to('@assets/'); ?>img/amex.png" style="width: 48px; height: 35px" >
                            <img src="https://img.icons8.com/ultraviolet/60/000000/discover.png">
                            <img src="https://img.icons8.com/color/60/000000/stripe.png">
                            <img src="<?= Url::to('@assets/'); ?>img/boleto.png" style="width: 48px; height: 35px" >

                        </li>
                        <br>
                        <li>
                            <img
                                    src="<?= yii::$app->urlManager->baseUrl . "/frontend/web/assets/img/selo-compra-segura.jpg" ?>"
                                    width="180px">
                        </li>
                        <br>
                        <li>
                            <a rel="nofollow" id="seloEbit" href="http://www.ebit.com.br/#peca-agora" target="_blank"
                               onclick="redir(this.href);"></a>
                            <script type="text/javascript" id="getSelo"
                                    src="https://imgs.ebit.com.br/ebitBR/selo-ebit/js/getSelo.js?74536">
                            </script>
                        </li>
                    </ul>
                </div>
                <div class="footer-sitemap-col col-sm-6">
                    <p class="h4">Redes Sociais</p>
                    <ul class="list-unstyled">
                        <li class="pagamento">
                            <a href="https://www.facebook.com/pecaagora/" rel="publisher" target="_blank"><img src="<?= Url::to('@assets/'); ?>img/face.png" style="width: 48px; height: 48px" ></a>

                            <a href="https://twitter.com/peca_agora" rel="publisher" target="_blank"><img src="<?= Url::to('@assets/'); ?>img/tt.png" style="width: 48px; height: 48px" ></a>

                            <a href=" https://www.linkedin.com/company/pe%C3%A7a-agora/?viewAsMember=true "
                               rel="publisher" target="_blank"><img src="<?= Url::to('@assets/'); ?>img/in.png" style="width: 48px; height: 48px" ></a>

                            <a href="https://www.youtube.com/channel/UCIhX8XDDoas1Rbt4kdZRmIg" rel="publisher"
                               target="_blank"><img src="<?= Url::to('@assets/'); ?>img/yt.png" style="width: 48px; height: 48px" ></a>



                            <a href="http://api.whatsapp.com/send?1=pt_BR&phone=5532991984007" rel="publisher"
                               target="_blank"><img src="<?= Url::to('@assets/'); ?>img/wpp.png" style="width: 48px; height: 48px" ></a>

                            <a href="https://www.instagram.com/pecaagora/" rel="publisher"
                               target="_blank"><img src="<?= Url::to('@assets/'); ?>img/insta.png" style="width: 48px; height: 48px" ></a>

                        </li><br>
                        <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fpecaagora&tabs&width=250&height=214&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId"
                                width="250" height="214" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
                    </ul>

                </div>
            </div>
            <hr/>
            <p class="text-center" style="font-size: 10px">www.pecaagora.com / OPT Soluções Comércio de Produtos e Serviços Automotivos LTDA /
                Peça Agora SP - Rua Carmópolis de Minas, 963, Vila Maria - São Paulo - SP - 02116-010 - CNPJ 18947338/0002-00

                Peça Agora MG - Rua José Lourenço Kelmer, s/nº, UFJF - CRITT, Juiz de Fora – MG - 36036-900   - CNPJ 18947338/0001-10
            </p>
            <p class="text-center" style="font-size: 10px"> &copy; PeçaAgora <?= date('Y') ?> - Todos os Direitos Reservados</p>
        </div>


    </footer>






    <?php $this->endBody() ?>
    <script id="dsq-count-scr" src="//peaagora.disqus.com/count.js" async></script>


    </body>

    </html>
<?php $this->endPage() ?>