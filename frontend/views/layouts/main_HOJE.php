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
    $(window).load(function() {
        $('.preloader').fadeOut('slow');
    });
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
        <img src="<?= Url::to('@assets/'); ?>img/banner%207.jpg"  style="width: 100%; text-align: center">
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

<div class="clearfix col-md-12 cab_fixo" style="padding: 15px;  background: linear-gradient(#004546, white);">
    <div class="col-lg-2 col-sm-4 col-xs-12 logo-wrap hidden-xs" style="margin-top: -5px;margin-bottom: 10px;z-index: 99">
        <div>
            <a href="<?= Url::to(['/']) ?>">
                <img class="logo" alt="Peca Agora" title="Peça Agora"
                     src="<?= Url::to('@assets/'); ?>img/pecaagora.png">
            </a>
        </div>
    </div>
    <div class="main-search col col-lg-7 col-sm-6 col-xs-12 container-fluid" style="margin-bottom: 10px;">
        <?php //if (Url::base(true) . '/' != Yii::$app->getRequest()->absoluteUrl) { ?><?php //} ?>
        <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/search']) ?>">
            <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12"
                 style="padding-left: 0px !important;padding-right: 0px !important; ">
                <input type="text"
                       name="<?= is_null(Yii::$app->request->get('codigo_global')) ? 'nome' : 'codigo_global' ?>"
                       id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist"
                       placeholder="O que você procura?"
                       value="<?= Yii::$app->request->get('nome',
                           Yii::$app->request->get('codigo_global', null)) ?>">
                <span class="input-group-btn">
                      <button type="submit" class="btn btn-default btn-lg control" id="main-search-btn"><i class="fa fa-search"></i></button>
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
            <i class="pull-left fa fa-user-o fa-3x"style="color: white !important; aria-hidden="true"></i>
            <span style=" color: #000000"> Olá, Bem vindo(a)</span><br>
            <a rel="nofollow" style="font-weight: bold; font-size: 16px; color: #000000" href="<?= Url::to(['/site/login']) ?>">Entre </a>
            <span style="font-weight: bold; color: black"> ou </span>
            <a rel="nofollow" style="font-weight: bold; font-size: 16px; color: #000000" href="<?= Url::to(['/comprador/create?tipoEmpresa=fisica']) ?>"> Cadastre-se</a>
        </div>
    <?php } else { ?>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-9 hidden-xs" >
            <i class="pull-left fa fa-user-o fa-3x" style="color: white !important; aria-hidden="true"></i>
            <span style=" color: #ffffff">Olá, <?= current(str_word_count(Yii::$app->user->getIdentity()->nome, 2)) ?> </span><br>
            <a style="color: black" href="<?= Url::to(['/minhaconta/pedidos']) ?>">Minha Conta</a> |
            <a style="color: black" rel="nofollow" href="<?= Url::to(['/site/logout']) ?>">Sair</a>
        </div>
        <?php
    }?>
    <a rel="nofollow" href="<?= Url::to(['/carrinho']) ?>" class="cart-wrap pull-left" style="padding: -0px; color: white !important;">
        <i class="fa fa-shopping-cart fa-3x" style="color: white !important;"></i>
        <span class="badge cart-count"><?= count(Yii::$app->session['carrinho']) ?></span>
    </a>

</div>

<!-- PESQUISAR -->


<!-- Menu -->

<div class="menuCategoria clearfix row">
    <!-- Início Menu -->
    <nav id="w1" class="navbar navbar-inverse nav-cats">
        <div class="container"><div class="navbar-header"><button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#w1-collapse"><span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span></button><a class="navbar-brand" href=".navbar-collapse" rel="nofollow" data-toggle="collapse">Departamentos</a></div><div id="w1-collapse" class="collapse navbar-collapse"><ul id="w2" class="navbar-nav nav"><li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/acabamentos-e-cabine" data-toggle="dropdown"><h3>Acabamentos e Cabine <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w3" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/espelhos-retrovisores-e-componentes" tabindex="-1">Espelhos Retrovisores e Componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/volante" tabindex="-1">Volante</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/estribo" tabindex="-1">Estribo</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/paralama-e-componentes" tabindex="-1">Paralama e Componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/parabarro" tabindex="-1">Parabarro</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/cabine-e-componentes" tabindex="-1">Cabine e Componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/portas-e-componentes" tabindex="-1">Portas e componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/grades-frontal-e-componentes" tabindex="-1">Grades Frontal e Componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/parachoques-e-componentes" tabindex="-1">Parachoques e Componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/parabrisa" tabindex="-1">Parabrisa</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/portas-cabine" tabindex="-1">Portas Cabine</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/tapa-sol" tabindex="-1">Tapa Sol </a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/ponteiras-parachoques-laterais-e-componentes" tabindex="-1">Ponteiras (Parachoques Laterais) e Componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/defletores" tabindex="-1">Defletores</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/cilindro-de-basculamento" tabindex="-1">Cilindro de Basculamento</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/vlvulas-suspenso" tabindex="-1">Válvulas Suspensão</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/componentes-de-farol" tabindex="-1">Componentes de Farol</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/escovas" tabindex="-1">Escovas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acabamentos-e-cabine/tapassol" tabindex="-1">TAPASSOL</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/acessorios" data-toggle="dropdown"><h3>Acessórios <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w4" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/acessorios/capa-protetora" tabindex="-1">Capa Protetora</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acessorios/itens-de-seguranca" tabindex="-1">Itens de Segurança</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acessorios/extintores-de-incendio" tabindex="-1">Extintores de Incêndio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acessorios/produtos-de-limpeza" tabindex="-1">Produtos de Limpeza</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acessorios/cinto-de-seguranca" tabindex="-1">Cinto de Segurança</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acessorios/tapetes-e-carpetes" tabindex="-1">Tapetes e carpetes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/acessorios/olhal" tabindex="-1">OLHAL</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/agropet" data-toggle="dropdown"><h3>Agropet <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w5" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/agropet/agropet" tabindex="-1">Agropet</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/autopecas" data-toggle="dropdown"><h3>Autopeças <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w6" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/autopecas/cabos" tabindex="-1">Cabos</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/eixo-expansor" tabindex="-1">Eixo Expansor </a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/farois" tabindex="-1">Faróis</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/baterias-caminhes-e-onibus" tabindex="-1">Baterias Caminhões e Onibus</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/baterias-carros-e-motos" tabindex="-1">Baterias Carros e Motos</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/bombas" tabindex="-1">Bombas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/bombas-de-direo-hidraulica" tabindex="-1">Bombas de Direção Hidraulica</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/carroceria" tabindex="-1">Carroceria</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/cubos-de-roda" tabindex="-1">Cubos de Roda</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/eixo-dianteiro" tabindex="-1">Eixo Dianteiro</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/eixo-traseiro" tabindex="-1">Eixo Traseiro</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/rolamentos-de-rodas" tabindex="-1">Rolamentos de Rodas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/terminal" tabindex="-1">Terminal</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/componentes-e-peas" tabindex="-1">Componentes e Peças </a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/calotas" tabindex="-1">Calotas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/parte-eltrica" tabindex="-1">Parte Elétrica</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/painel-e-instrumentos" tabindex="-1">Painel e Instrumentos</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/acessrios" tabindex="-1">Acessórios</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/acessrios-de-baterias" tabindex="-1">Acessórios de Baterias </a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/acessrios-de-radiador" tabindex="-1">Acessórios de Radiador</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/bomba-de-gua" tabindex="-1">Bomba de Água</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/aparelho-teste-de-bateria" tabindex="-1">Aparelho Teste de Bateria</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/bomba-de-combustvel" tabindex="-1">Bomba de Combustível</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/bomba-de-leo" tabindex="-1">Bomba de Óleo</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/caixas-carcaas-e-componentes" tabindex="-1">Caixas, Carcaças e Componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/carburador" tabindex="-1">Carburador</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/escapamento-e-componentes" tabindex="-1">Escapamento e Componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/lanternas" tabindex="-1">Lanternas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/limpador-de-parabrisa" tabindex="-1">Limpador de Parabrisa</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/mangueiras-e-tubulaes" tabindex="-1">Mangueiras e Tubulações</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/tampas" tabindex="-1">Tampas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/tanques" tabindex="-1">Tanques</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/kit-reviso-carro" tabindex="-1">Kit Revisão Carro</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/cilindro-mestre" tabindex="-1">Cilindro Mestre</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/buchas-coxins-e-batentes" tabindex="-1">Buchas, Coxins e Batentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/buchas-rebites-parafusos-porcas-e-arruelas" tabindex="-1">Buchas, Rebites, Parafusos, Porcas e Arruelas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/abraadeiras-e-presilhas" tabindex="-1">Abraçadeiras e presilhas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/ar-condicionado" tabindex="-1">Ar Condicionado</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/bandejas" tabindex="-1">Bandejas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/barra-estabilizadora" tabindex="-1">Barra Estabilizadora</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/barra-haste-reao" tabindex="-1">Barra Haste Reação</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/bomba-hidrulica-cabine" tabindex="-1">Bomba Hidráulica Cabine</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/cmbio" tabindex="-1">Câmbio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/componentes-de-direo" tabindex="-1">Componentes de Direção</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/eltrica" tabindex="-1">Elétrica</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/embuchamento" tabindex="-1">Embuchamento</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/hidrulica" tabindex="-1">Hidráulica</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/interruptores" tabindex="-1">Interruptores</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/longarinas-e-chassis" tabindex="-1">Longarinas e Chassis</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/painel-frontal" tabindex="-1">Painel Frontal</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/peas-carreta" tabindex="-1">Peças Carreta</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/sistema-hidrulico" tabindex="-1">Sistema Hidráulico</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/suporte-estepe" tabindex="-1">Suporte Estepe</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/suportes" tabindex="-1">Suportes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/autopecas/suspenso-cabine" tabindex="-1">Suspensão Cabine</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/baterias" data-toggle="dropdown"><h3>Baterias <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w7" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/baterias/carregadores-de-baterias" tabindex="-1">Carregadores de Baterias</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/cardan" data-toggle="dropdown"><h3>Cardan <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w8" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/cardan/garfo" tabindex="-1">Garfo</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/cardan/engraxadeira" tabindex="-1">Engraxadeira</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/cardan/cruzeta" tabindex="-1">Cruzeta</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/cardan/cardan-completo" tabindex="-1">Cardan Completo</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/cardan/suporte-cardan" tabindex="-1">Suporte Cardan</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/colas-e-adesivos" data-toggle="dropdown"><h3>Colas e adesivos <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w9" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/colas-e-adesivos/colas-e-adesivos" tabindex="-1">Colas e adesivos</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/diversos" data-toggle="dropdown"><h3>Diversos <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w10" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/diversos/diversos" tabindex="-1">Diversos</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/embreagem" data-toggle="dropdown"><h3>Embreagem <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w11" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/embreagem/kit-de-embreagem-leves" tabindex="-1">Kit de Embreagem Leves</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/embreagem/ponteira" tabindex="-1">Ponteira</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/embreagem/diferencial" tabindex="-1">Diferencial</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/embreagem/flange" tabindex="-1">Flange</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/embreagem/luvas" tabindex="-1">Luvas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/embreagem/peas-de-embreagem" tabindex="-1">Peças de Embreagem</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/embreagem/kit-embreagem-caminhes-e-onibus" tabindex="-1">Kit Embreagem Caminhões e Onibus</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/embreagem/rolamento-e-componentes" tabindex="-1">Rolamento e Componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/embreagem/cilindro-de-embreagem" tabindex="-1">Cilindro de Embreagem</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/embreagem/rolamentos-de-embreagem" tabindex="-1">Rolamentos de Embreagem</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/ferragens" data-toggle="dropdown"><h3>Ferragens <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w12" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/ferragens/ferragens" tabindex="-1">Ferragens</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/ferramentas" data-toggle="dropdown"><h3>Ferramentas <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w13" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/ferramentas/ferramentas" tabindex="-1">Ferramentas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/ferramentas/cavaletes" tabindex="-1">Cavaletes</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/filtros" data-toggle="dropdown"><h3>Filtros <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w14" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/filtros/filtro-de-ar" tabindex="-1">Filtro de Ar</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/filtros/filtro-de-ar-condicionado" tabindex="-1">Filtro de Ar Condicionado</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/filtros/filtro-de-combustvel" tabindex="-1">Filtro de Combustível</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/filtros/outros-filtros" tabindex="-1">Outros Filtros</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/filtros/filtro-de-uria-arla" tabindex="-1">Filtro de Uréia (Arla)</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/filtros/componentes-arla" tabindex="-1">Componentes ARLA</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/filtros/filtro-de-leo-lubrificante" tabindex="-1">Filtro de Óleo Lubrificante </a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/freios" data-toggle="dropdown"><h3>Freios <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w15" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/freios/junta" tabindex="-1">Junta</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/kits-de-freio" tabindex="-1">Kits de Freio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/disco-de-freio" tabindex="-1">Disco de Freio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/lona-de-freio" tabindex="-1">Lona de Freio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/pastilhas-de-freio" tabindex="-1">Pastilhas de Freio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/pontuva-e-capa" tabindex="-1">Pontuva e capa</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/sensores" tabindex="-1">Sensores</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/sapatas-de-freio" tabindex="-1">Sapatas de Freio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/vlvulas-de-freio" tabindex="-1">Válvulas de Freio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/compressor-do-ar-do-freio-e-componentes" tabindex="-1">Compressor do Ar do Freio e Componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/cuca-de-freio-e-componentes" tabindex="-1">Cuíca de Freio e Componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/fluido-de-freios" tabindex="-1">Fluido de freios</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/freio-motor" tabindex="-1">Freio Motor</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/componentes-pinca-de-freio" tabindex="-1">Componentes Pinça de Freio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/catraca-de-freio" tabindex="-1">Catraca de Freio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/tambor-de-freio" tabindex="-1">Tambor de freio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/componentes-de-freio" tabindex="-1">Componentes de Freio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/freios/flexveis" tabindex="-1">Flexíveis</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/motor" data-toggle="dropdown"><h3>Motor <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w16" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/motor/virabrequim" tabindex="-1">Virabrequim</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/vela-aquecedora" tabindex="-1">Vela Aquecedora</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/vlvulas-e-comando-de-vlvulas" tabindex="-1">Válvulas e Comando de Válvulas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/polias" tabindex="-1">Polias</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/turbina-e-componentes" tabindex="-1">Turbina e Componentes</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/coxim" tabindex="-1">Coxim</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/reservatrio-expanso" tabindex="-1">Reservatório Expansão</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/vlvulas" tabindex="-1">Válvulas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/tensores" tabindex="-1">Tensores</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/correias" tabindex="-1">Correias</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/hlice" tabindex="-1">Hélice</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/juntas" tabindex="-1">Juntas</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/kit-troca-de-leo-de-motor-e-filtros" tabindex="-1">Kit Troca de Óleo de Motor e Filtros</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/radiadores" tabindex="-1">Radiadores</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/velas-de-ignio" tabindex="-1">Velas de Ignição</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/chave-ignio" tabindex="-1">Chave Ignição</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/cilindro-de-acionamento" tabindex="-1">Cilindro de Acionamento</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/coletores" tabindex="-1">Coletores</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/componentes-de-ignio" tabindex="-1">Componentes de Ignição</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/sensores" tabindex="-1">Sensores</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/motor/tomada-de-fora" tabindex="-1">TOMADA DE FORÇA </a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/oleo" data-toggle="dropdown"><h3>Óleo <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w17" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/oleo/graxa-rolamento" tabindex="-1">Graxa Rolamento</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/oleo/kit-3-litros-de-oleo-de-motor" tabindex="-1">Kit 3 Litros de Óleo de Motor</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/oleo/kit-4-litros-de-oleo-de-motor" tabindex="-1">Kit 4 Litros de Óleo de Motor</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/oleo/oleo-de-motor" tabindex="-1">Óleo de Motor</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/oleo/oleo-hidraulico" tabindex="-1">Óleo Hidráulico</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/oleo/oleo-cambio-transmissao" tabindex="-1">Óleo Cambio e Transmissão</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/oleo/galao-de-20-litros-oleo-de-motor-diesel" tabindex="-1">Galão de 20 Litros Óleo de Motor Diesel</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/oleo/graxa-chassi" tabindex="-1">Graxa Chassi</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/oleo/carter-leo" tabindex="-1">Carter Óleo</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/pinturas" data-toggle="dropdown"><h3>Pinturas <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w18" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/pinturas/pintura" tabindex="-1">Pintura</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/pneus" data-toggle="dropdown"><h3>Pneus <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w19" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/pneus/pneus-aro-13" tabindex="-1">Pneus Aro 13</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/pneus/pneus-aro-14" tabindex="-1">Pneus Aro 14</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/pneus/pneus-aro-15" tabindex="-1">Pneus Aro 15</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/pneus/pneus-aro-16" tabindex="-1">Pneus Aro 16</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/pneus/pneus-de-moto" tabindex="-1">Pneus de Moto</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/pneus/pneus-aro-17" tabindex="-1">Pneus Aro 17</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/servicos-automotivos" data-toggle="dropdown"><h3>Serviços Automotivos <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w20" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/servicos-automotivos/servicos-de-motor" tabindex="-1">Serviços de Motor</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/servicos-automotivos/servico-de-leva-e-traz" tabindex="-1">Serviço de Leva e Traz</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/servicos-automotivos/servicos-de-suspensao" tabindex="-1">Serviços de Suspensão</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/servicos-automotivos/servicos-de-limpeza" tabindex="-1">Serviços de Limpeza</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/servicos-automotivos/servicos-de-cambio" tabindex="-1">Serviços de Cambio</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/servicos-automotivos/manutencao-preventiva" tabindex="-1">Manutenção Preventiva</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/servicos-automotivos/servicos-de-pneus" tabindex="-1">Serviços de Pneus</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/servicos-automotivos/servicos-de-freios" tabindex="-1">Serviços de Freios</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/servicos-automotivos/servicos-de-direcao" tabindex="-1">Serviços de Direção</a></h3></li></ul></li>
                    <li class="dropdown"><a class="dropdown-toggle disabled" href="/pecaagora/auto/suspensao" data-toggle="dropdown"><h3>Suspensão <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w21" class="dropdown-menu"><li><h3><a href="/pecaagora/auto/suspensao/peas-suspenso" tabindex="-1">Peças Suspensão</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/suspensao/amortecedor-a-gas-tampa" tabindex="-1">Amortecedor a gás tampa</a></h3></li>
                            <li><h3><a href="/pecaagora/auto/suspensao/amortecedores" tabindex="-1">Amortecedores</a></h3></li></ul></li></ul></div></div></nav>

    <!-- Término Menu -->
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
    <div class="row barradeinformacoes">
        <div class="visible-lg text-center" >
            <div class="panelPart col-lg-1" >
                <div >
                    <img src="<?= yii::$app->urlManager->baseUrl . "/frontend/web/assets/img/caminhao_aviao2.png" ?>"  width="80" height="80">
                </div>
            </div>
            <div class="panelPart col-lg-3  text-left ">
                <div class="texto">
                    Entrega em todo Brasil em até 1 dia*
                </div>
                <div class="texto">
                    Entrega em até 3 horas em SP
                </div>
            </div>
            <div class="panelPart col-lg-3 text-left lateralEsquerdaBarraInformacoes">
                <div class="texto">
                    Pague em até 6x
                </div>
                <a href="https://pecaagora.zendesk.com/hc/pt-br/articles/206974937-O-que-%C3%A9-Compra-Garantida-e-Como-Funciona-" target="_blank">
                    <div class="texto">
                        Compra Garantida
                    </div>
                </a>
                <br>
            </div>
            <div class="panelPart col-lg-2 text-left img">
                <a href="https://pecaagora.com/site/nossaloja"
                   target="_blank">
                    <br><div>
                        Nossas Lojas
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
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
    <div class="footer-sitemap-col col-sm-3" style="padding: 20px">
        <img class="logo" alt="Peca Agora" title="Peça Agora"
        src="<?= Url::to('@assets/'); ?>img/pecaagora.png">
    </div>

    <!-- Paginas  -->
    <div class="footer-sitemap-col col-sm-3" style="padding: 20px">
        <p class="h4" style="color: black">Páginas do Lojista</p>
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
        <p class="h4" style="color: black">Navegação</p>
        <ul class="list-unstyled" style="color: white">
            <li><a style="color: white" href="<?= Url::to(['/auto']) ?>">Categorias</a></li>
            <li><a style="color: white" href="<?= Url::to(['/veiculos/carros']) ?>">Carros</a></li>
            <li><a style="color: white" href="<?= Url::to(['/veiculos/caminhoes']) ?>">Caminhões</a></li>
        </ul>
    </div>

    <div class="footer-sitemap-col col-sm-3" style="padding: 20px">
        <p class="h4" style="color: black">Sobre</p>
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
