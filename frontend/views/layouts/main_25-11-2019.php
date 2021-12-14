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
        <a rel="nofollow" href="<?= Url::to(['/carrinho']) ?>" class="cart-wrap pull-left" style="padding: -0px; color: #040100 !important;">
            <i class="fa fa-shopping-cart fa-3x" style="color: #040100 !important;"></i>
            <span class="badge cart-count"><?= count(Yii::$app->session['carrinho']) ?></span>
        </a>

        <li class="nav-toggle hidden-lg" style="background-color: white ;border-radius: 10px">
            <button style="background-color: #007576" class="nav-toggle-btn main-btn icon-btn"><i style="color: white" class="fa fa-bars"></i></button>
        </li>

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
            line-height:16px;
            padding:8px 12px 9px;
            position: static !important;
        }
        .menu-list li{
            display: inline-block;

        }

        .dropdown-content{
            position: absolute;
            left:5%;
            top: 130px;
            width: 90%;

        }

        .text-menu{
            color: white;

        }

        }
    </style>



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
    <!--<div id="navigation" style="background-color: #007576;">
        <div id="responsive-nav">
            <div class="category-nav">
                <a class="category-header btn btn-outline-dark">Categorias&nbsp <i class="fa fa-bars" aria-hidden="true" style="color: white;"></i></a>
                <ul class="category-list">
                    <li class="dropdown side-dropdown">
                        <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">Autopeças <i class="fa fa-angle-right"></i></a>
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
                                </div>
                            </div>
                            <div class="row hidden-sm hidden-xs">
                                <div class="col-md-12">
                                    <hr>
                                    <a class="banner banner-1" href="#">
                                        <img src="<?= Url::to('@assets/'); ?>img/BANNER Cuica.jpg">
                                        <div class="banner-caption text-center">
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>

            </div>
        </div>

        <div class="container">
            <div id="responsive-nav">
                <ul class="menu-list" >
                    <li class="dropdown" >
                        <a href="#" class="text-menu">Acabamentos e Cabine</a>
                        <div class="dropdown-content">
                            <div class="row" >
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/volante.png">
                                        <li> <h3 class="list-links-title"><a href="#">VOLANTES</a></h3> </li><br>
                                        <li><a href="#">Volante direcão universal</a></li>
                                        <li><a href="#">Volante esportivo</a></li>
                                        <li><a href="#">Volante direção completo ...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/parachoque.png">
                                        <li> <h3 class="list-links-title"><a href="#">Parachoques e Componentes</a> </h3> </li><br>
                                        <li><a href="#">Parachoque dianteiro</a></li>
                                        <li><a href="#">Parachoque para scania</a></li>
                                        <li><a href="#">Parachoque dianteiro central </a></li>
                                        <li><a href="#">Parachoque central estreito </a></li>
                                        <li><a href="#">Suporte parachoque dianteiro... </a></li>
                                    </ul>
                                    <hr>
                                </div>
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/defletor.png">
                                        <li> <h3 class="list-links-title"><a href="#">DEFLETORS</a></h3> </li><br>
                                        <li><a href="#">Defletor capô</a></li>
                                        <li><a href="#">Defletor lateral cabine</a></li>
                                        <li><a href="#">Defletor cubo</a></li>
                                        <li><a href="#">Defletor de ar</a></li>
                                        <li><a href="#">Defletor terminal...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/componentes-farol.png">
                                        <li> <h3 class="list-links-title"><a href="#">Componentes de Farol</a>  </h3> </li><br>
                                        <li><a href="#">Protetor farol auxiliar</a></li>
                                        <li><a href="#">Par farol neblina</a></li>
                                        <li><a href="#">Lente farol dianteiro</a></li>
                                        <li><a href="#">Farol principal</a></li>
                                        <li><a href="#">Farol neblina...</a></li>
                                    </ul>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown" >
                        <a href="#" class="text-menu"> Autopeças</a>
                        <div class="dropdown-content">
                            <div class="row">
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/farol.png">
                                        <li> <h3 class="list-links-title"><a href="#">Faróis</a></h3> </li><br>
                                        <li><a href="#">Farol principal mercedes benz 1620 esquerdo</a></li>
                                        <li><a href="#">Farol principal mercedes benz 1620 direito</a></li>
                                        <li><a href="#">Farol principal mb mercedes benz 914 esquerdo</a></li>
                                        <li><a href="#">Farol principal mb mercedes benz 914 direito</a></li>
                                        <li><a href="#">Lampada led 67 24v volts...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/lanternas.png">
                                        <li> <h3 class="list-links-title"><a href="#">Lanternas</a></h3> </li><br>
                                        <li><a href="#">Lanterna traseira esquerdo</a></li>
                                        <li><a href="#">Lanterna traseira direita</a></li>
                                        <li><a href="#">Lanterna traseira direita</a></li>
                                        <li><a href="#">Lanterna traseira esquerda</a></li>
                                        <li><a href="#">Lanterna seta pisca...</a></li>
                                    </ul>
                                    <hr>
                                </div>
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/acessorios.png">
                                        <li> <h3 class="list-links-title"><a href="#">Acessórios</a></h3> </li><br>
                                        <li><a href="#">Parafuso flange cardan</a></li>
                                        <li><a href="#">Faixa refletiva parachoque</a></li>
                                        <li><a href="#">Cavalete borracha</a></li>
                                        <li><a href="#">Porca diferencial</a></li>
                                        <li><a href="#">Grelhas de retorno...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/calota.png">
                                        <li> <h3 class="list-links-title">Calotas</h3> </li><br>
                                        <li><a href="#">Calotinha cromada</a></li>
                                        <li><a href="#">Calota roda dianteira</a></li>
                                        <li><a href="#">Calota graxa</a></li>
                                        <li><a href="#">Tampa cavalete</a></li>
                                        <li><a href="#">Calota cubo dianteiro</a></li>
                                    </ul>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown" >
                        <a href="#" class="text-menu">Embreagem e Acessórios</a>
                        <div class="dropdown-content">
                            <div class="row">
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/Embreagem.png">
                                        <li> <h3 class="list-links-title"><a href="#">Cilindro de Embreagem</a></h3> </li><br>
                                        <li><a href="#">Cilindro auxiliar</a></li>
                                        <li><a href="#">Servo de embreagem</a></li>
                                        <li><a href="#">Cilindro embreagem mestre </a></li>
                                        <li><a href="#">Cilindro embreagem...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/KitEmbreagem.png">
                                        <li> <h3 class="list-links-title"><a href="#">Kit Embreagem Caminhões e Onibus</a> </h3> </li><br>
                                        <li><a href="#">Kit embreagem sachs ford </a></li>
                                        <li><a href="#">Kit embreagem sachs agrale</a></li>
                                        <li><a href="#">Kit embreagem vw 8150</a></li>
                                        <li><a href="#">Kit embreagem sachs vw volkswagen </a></li>
                                        <li><a href="#">Kit embreagem valeo vw vlkswagen...</a></li>
                                    </ul>
                                    <hr>
                                </div>
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/Peças .png">
                                        <li> <h3 class="list-links-title"><a href="#">Peças de Embreagem</a></h3> </li><br>
                                        <li><a href="#">Reservatório óleo embreagem  </a></li>
                                        <li><a href="#">Conexão servo embreagem</a></li>
                                        <li><a href="#">Caneca servo embreagem </a></li>
                                        <li><a href="#">Reparo servo embreagem...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/rolamentos.png">
                                        <li> <h3 class="list-links-title"><a href="#">Rolamento e Componentes</a> </h3> </li><br>
                                        <li><a href="#">Rolamento nissan</a></li>
                                        <li><a href="#">Rolamento cambio 2826.5 traseiro</a></li>
                                        <li><a href="#">Rolamento cardan</a></li>
                                        <li><a href="#">Rolamento cubo roda</a></li>
                                        <li><a href="#">Rolamento piloto...</a></li>
                                    </ul>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown" >
                        <a href="#" class="text-menu">Filtros</a>
                        <div class="dropdown-content">
                            <div class="row">
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/arla.png">
                                        <li> <h3 class="list-links-title"><a href="#">Componentes ARLA</a></h3> </li><br>
                                        <li><a href="#"> Arla 32 galão 20 litros</a></li>
                                        <li><a href="#"> Tubo sistema ureia arla 50 litros </a></li>
                                        <li><a href="#">Sensor temperatura arla</a></li>
                                        <li><a href="#">Filtro do arla todos cummins</a></li>
                                        <li><a href="#">Suporte cinta tanque arla...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/filtro.png">
                                        <li> <h3 class="list-links-title"><a href="#">Filtro de Ar</a> </h3> </li><br>
                                        <li><a href="#">Filtro de ar mann (c14200)</a></li>
                                        <li><a href="#">Filtro de ar mann (c15300)</a></li>
                                        <li><a href="#">Filtro de ar mann (c172253)</a></li>
                                        <li><a href="#">Filtro de ar mann (c17262)</a></li>
                                        <li><a href="#">Filtro de ar mann (c172621)...</a></li>
                                    </ul>
                                    <hr>
                                </div>
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/filtro-ureia.png">
                                        <li> <h3 class="list-links-title"><a href="#">Filtro de Uréia (Arla)</a> </h3> </li><br>
                                        <li><a href="#">Filtro ureia com vedação mb Mercedes</a></li>
                                        <li><a href="#">Kit filtro de ureia agrale volare motor</a></li>
                                        <li><a href="#">Filtro para ureia iveco eurocargo eurofire</a></li>
                                        <li><a href="#">Filtro ureia para scania b7 b9 b12 (20713630)</a></li>
                                        <li><a href="#">Filtro com conjunto vedações tanque ureia...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/filtro-oleo.png">
                                        <li> <h3 class="list-links-title"><a href="#">Filtro de Óleo Lubrificante</a></h3> </li><br>
                                        <li><a href="#">Filtro de óleo lubrificante mann (h1271)</a></li>
                                        <li><a href="#">Elemento filtro óleo lubrificante</a></li>
                                        <li><a href="#">Elemento filtro óleo lubrificante</a></li>
                                        <li><a href="#">Filtro de óleo mb Mercedes benz 709 710</a></li>
                                        <li><a href="#">Suporte filtro diesel para scania 112 113...</a></li>
                                    </ul>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown" >
                        <a href="#" class="text-menu">Freios e Acessórios</a>
                        <div class="dropdown-content">
                            <div class="row">
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/catacra.png">
                                        <li> <h3 class="list-links-title"><a href="#">Catraca de Freio</a></h3> </li><br>
                                        <li><a href="#">Catraca freio vw volkswagen e ford</a></li>
                                        <li><a href="#">Catraca freio vw volkswagen pesados</a></li>
                                        <li><a href="#">Catraca freio automática eks volvo b58</a></li>
                                        <li><a href="#">Unidade controle catraca mb Mercedes</a></li>
                                        <li><a href="#">Catraca freio automático traseiro... </a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/cilindro.png">
                                        <li> <h3 class="list-links-title"><a href="#">Cilindro Mestre</a></h3> </li><br>
                                        <li><a href="#">Cilindro mestre freio mb sprinter cdi 311 313 413</a></li>
                                        <li><a href="#">Cilindro mestre embreagem ford sapão</a></li>
                                        <li><a href="#">Cilindro mestre iveco eurocargo 170e22 170e24</a></li>
                                        <li><a href="#">Conector 90 ng8 vw volkswagen 7110 120 7100</a></li>
                                        <li><a href="#">Cilindro mestre freio rccm00240 vw 690 790...</a></li>
                                    </ul>
                                    <hr>
                                </div>
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/disco-freio.png">
                                        <li> <h3 class="list-links-title"><a href="#">Disco de Freio</a> </h3> </li><br>
                                        <li><a href="#">Disco freio dianteiro mb Mercedes benz sprinter</a></li>
                                        <li><a href="#">Disco freio traseiro mb Mercedes benz sprinter</a></li>
                                        <li><a href="#">Disco freio ventilado dianteiro f4000 96 98</a></li>
                                        <li><a href="#">Disco freio dianteiro ventilado sem cubo</a></li>
                                        <li><a href="#">Par disco de freio dianteiro solido gm...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/kit-freio.png">
                                        <li> <h3 class="list-links-title"><a href="#">Kits e reparos de Freio</a> </h3> </li><br>
                                        <li><a href="#">Reparo pinça freio retentores iveco daily</a></li>
                                        <li><a href="#">Reparo secador ar apu mbb 1214 1218 1414 1418</a></li>
                                        <li><a href="#">Reparo válvula pedal freio para knorr iveco</a></li>
                                        <li><a href="#">Kit disco e pastilha de freio dianteiro gm</a></li>
                                        <li><a href="#">Jogo reparo válvula manetim freio 4 saída...</a></li>
                                    </ul>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown" >
                        <a href="#" class="text-menu">Engrenagens e Acessórios</a>
                        <div class="dropdown-content">
                            <div class="row">
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/engrenagem.png">
                                        <li> <h3 class="list-links-title"><a href="#">Engrenagens</a></h3> </li><br>
                                        <li><a href="#">Engrenagem motriz auxiliar câmbio rt8908</a></li>
                                        <li><a href="#">Engrenagem motriz contra eixo câmbio</a></li>
                                        <li><a href="#">Engrenagem intermediária</a></li>
                                        <li><a href="#">Engrenagem bomba alta pressão motor cummins</a></li>
                                        <li><a href="#">Engrenagem satelite hl4 mb Mercedes benz...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/correias.png">
                                        <li> <h3 class="list-links-title"><a href="#">Correias e correntes</a> </h3> </li><br>
                                        <li><a href="#">Correia direção hidráulica bomba dagua</a></li>
                                        <li><a href="#">Correia micro v alternador mb</a></li>
                                        <li><a href="#">Correia em v motor 2x13x1450 mbb ônibus</a></li>
                                        <li><a href="#">Correia alternador bomba d'água simples mb</a></li>
                                        <li><a href="#">Correia poly v Mercedes benz...</a></li>
                                    </ul>
                                    <hr>
                                </div>
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/polias.png">
                                        <li> <h3 class="list-links-title"><a href="#">Polias</a> </h3> </li><br>
                                        <li><a href="#">Polia tensora correia troller t4 t5 2.8 turbo</a></li>
                                        <li><a href="#">Polia tensora correia ferro lisa iveco</a></li>
                                        <li><a href="#">Polia esticadoramotor mb Mercedes benz</a></li>
                                        <li><a href="#">Polia alternador ferro estriada peugeot iveco</a></li>
                                        <li><a href="#">Polia bomba dagua mb Mercedes benz 1215c...</a></li>
                                    </ul>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown" >
                        <a href="#" class="text-menu">Válvulas</a>
                        <div class="dropdown-content">
                            <div class="row">
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/valvulas.png">
                                        <li> <h3 class="list-links-title"><a href="#">Válvulas</a></h3> </li><br>
                                        <li><a href="#">Válvula descarga rápida mb Mercedes benz 1938</a></li>
                                        <li><a href="#">Válvula esférica fêmea com alavanca sem niple</a></li>
                                        <li><a href="#">Válvula pedal ford novo cargo 1317 1717 2428 2628 3132 2422 2622 1517 1722</a></li>
                                        <li><a href="#">Válvula acionamento freio motor haste longa</a></li>
                                        <li><a href="#">Guia válvula admissão escape para scania 124 ...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/valvula-termo.png">
                                        <li> <h3 class="list-links-title"><a href="#">Válvula Termostática</a> </h3> </li><br>
                                        <li><a href="#">Válvula termostática reta vw 12140 ford f1000</a></li>
                                        <li><a href="#">Válvula termostática mwm x12 vw</a></li>
                                        <li><a href="#">Válvula termostática motor iveco euro cargo</a></li>
                                        <li><a href="#">Válvula termostática original cummins ford</a></li>
                                        <li><a href="#">Valvula termostatica cummins 6ctaa 8,3 71...</a></li>
                                    </ul>
                                    <hr>
                                </div>
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/bomba-combus.png">
                                        <li> <h3 class="list-links-title"><a href="#">Válvulas Solenoide</a> </h3> </li><br>
                                        <li><a href="#">Bomba combustível modelo cummins serie c</a></li>
                                        <li><a href="#">Bomba combustível volvo d13/fm13/fh13</a></li>
                                        <li><a href="#">Bomba auxiliar combustível elétrica 12v</a></li>
                                        <li><a href="#">Bomba combustível motor cummins 6ct vw</a></li>
                                        <li><a href="#">Bico válvula arla o bico mb Mercedes benz axor...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                                            <img style="70px; height:70px" src="<?= Url::to('@assets/'); ?>img/imgs-menu/válvulas-comando.png">
                                        <li> <h3 class="list-links-title"><a href="#">Válvulas e Comando de Válvulas</a> </h3> </li><br>
                                        <li><a href="#">Balancim completo motor cummins serie c</a></li>
                                        <li><a href="#">Comando válvulas motor cummins isc 8.3 isc</a></li>
                                        <li><a href="#">Comando válvula vw 8150 9150 13180 15180 mwm x10</a></li>
                                    </ul>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div><br>
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