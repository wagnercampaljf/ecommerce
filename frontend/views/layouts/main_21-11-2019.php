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
            <img src="<?= Url::to('@assets/'); ?>img/bannertopo10.jpg"  style="width: 100%; text-align: center">
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
        <div class="col-lg-2 col-sm-4 col-xs-12 logo-wrap hidden-xs" style="margin-top: -5px;margin-bottom: 10px;z-index: 99">
            <div>
                <a href="<?= Url::to(['/']) ?>">
                    <img class="logo" alt="Peca Agora" title="Peça Agora"
                         src="<?= Url::to('@assets/'); ?>img/pecaagora_azul.png">
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
                      <button type="submit" class="btn btn-default btn-lg control" style="background-color: #040100" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
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

                <img class="nav-toggle" style="height: 70px; width: 250px" alt="Peca Agora" title="Peça Agora"
                     src="<?= Url::to('@assets/'); ?>img/pecaagora_azul.png">
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
            padding:5px 20px 5px;
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


<?php
 echo MenuWidget::widget([]);
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

    <div class="row" style="background-color: #040100">
        <div class="footer-sitemap-col col-sm-2" style="padding: 20px">
            <!--<img class="logo" alt="Peca Agora" title="Peça Agora"
        src="<?= Url::to('@assets/'); ?>img/pecaagora.png">-->
        </div>

        <!-- Paginas  -->
        <div class="footer-sitemap-col col-sm-3" style="padding: 20px">
            <p class="h4" style="color: #ffffff">Páginas do Lojista</p>
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
            <p class="h4" style="color: #ffffff">Navegação</p>
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