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
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title . ' Peça Agora Auto') ?></title>
    <?php $this->head() ?>
    <!-- Hotjar Tracking Code for www.pecaagora.com -->
	<script>
	    (function(h,o,t,j,a,r){
	        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
	        h._hjSettings={hjid:966127,hjsv:6};
	        a=o.getElementsByTagName('head')[0];
	        r=o.createElement('script');r.async=1;
	        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
	        a.appendChild(r);
	    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
	</script>
	<script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/735972524c76c4ea8bfef880f/ac449ffc8db454787c5540dfa.js");</script>

	<script src="<?= yii::$app->urlManager->baseUrl . '/frontend/web/js/jquery-1.8.3.min.js' ?>" type="text/javascript"></script>
	<script src="<?= yii::$app->urlManager->baseUrl . '/frontend/web/js/jquery.elevatezoom.js' ?>" type="text/javascript"></script>


</head>
<!--BLOQUEIO DA SELECAO DE CONTEUDO -->
<!--<body ondragstart='return false' onselectstart='return false' oncontextmenu='return false'>-->
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <div class="header row">
        <div class="menu-sup text-left nav">
		<div class="hidden-sm hidden-md hidden-lg hidden-xl">
    			<div class="links pull-left hidden-print col-lg-offset-1 col-md-offset-1  col-md-12 col-lg-12 col-sm-12 col-xs-12">
        	            <div class="top-list">
        	                <span>
        	                        <b>Grande São Paulo:</b><br>
        	                </span>
        	                <span>
        	                   	<span><i class="fa fa-phone-square"></i>&nbsp;(11) 2193-1099</span> <span><i class="fa fa-whatsapp"></i>&nbsp;(11) 94554-4208<br></span>
        	                </span>
        	                <span>
        	                    <b>Demais Regiões:</b><br>
        	                </span>
        	                <span>
        	                    <span><i class="fa fa-phone-square"></i>&nbsp;(32) 3015-0023 </span><span> <i class="fa fa-whatsapp"></i>&nbsp;(32) 98835-4007</span>
        	                </span>
        	            </div>
        	        </div>
    			</div>
    		<div class="hidden-xs">
    			<div class="links pull-left hidden-print col-lg-offset-1 col-md-offset-1  col-md-12 col-lg-11 col-sm-12 col-xs-12">
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
           		 </div>
		</div>
        </div>
        <div class="clearfix col-md-12" style="margin-bottom: 5px;padding-top: 15px">
            <div class="row col-md-3 col-lg-2 col-sm-4 col-xs-12 logo-wrap" style="margin-top: -5px;margin-bottom: 10px;z-index: 99">
                <div>
                    <a href="<?= Url::to(['/']) ?>">
                        <img class="logo" alt="Peca Agora" title="Peça Agora"
                             src="<?= Url::to('@assets/'); ?>img/pecaagora.png">

                    </a>
                </div>
            </div>
            <div class="row main-search col-md-6 col-lg-7 col-sm-6 col-xs-12 container-fluid"
                 style="margin-bottom: 10px;">
                <?php

                if (Url::base(true) . '/' != Yii::$app->getRequest()->absoluteUrl) {
                    ?>
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
                <?php } ?>
            </div>

            <?php if (Yii::$app->user->isGuest) { ?>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-9">
                    <i class="pull-left fa fa-user-o fa-3x" aria-hidden="true"></i>
                    <span> Olá, Bem vindo(a)</span><br>
                    <a rel="nofollow" href="<?= Url::to(['/site/login']) ?>">Entre </a>
                    <span> ou </span>
                    <a rel="nofollow" href="<?= Url::to(['/comprador/create?tipoEmpresa=fisica']) ?>"> Cadastre-se</a>
                </div>
            <?php } else { ?>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-9">
                    <i class="pull-left fa fa-user-o fa-3x" aria-hidden="true"></i>
                    <span>Olá, <?= current(str_word_count(Yii::$app->user->getIdentity()->nome, 2)) ?> </span><br>
                    <a href="<?= Url::to(['/minhaconta/pedidos']) ?>">Minha Conta</a> |
                    <a rel="nofollow" href="<?= Url::to(['/site/logout']) ?>">Sair</a>
                </div>
                <?php
            }
            ?>
            <div class="row cart col-sm-1 col-md-1 col-lg-1 col-xs-3 text-right hidden-print" style="padding: 0">
                <a rel="nofollow" href="<?= Url::to(['/carrinho']) ?>" class="cart-wrap pull-left" style="">
                    <i class="fa fa-shopping-cart fa-3x"></i>
                    <span class="badge cart-count"><?= count(Yii::$app->session['carrinho']) ?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="menuCategoria clearfix row">
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
        NavBar::end(); */
        ?>

	<!-- Início Menu -->
            <nav id="w6" class="navbar navbar-inverse nav-cats"><div class="container"><div class="navbar-header"><button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#w6-collapse"><span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span></button><a class="navbar-brand" href=".navbar-collapse" rel="nofollow" data-toggle="collapse">Departamentos</a></div><div id="w6-collapse" class="collapse navbar-collapse"><ul id="w7" class="navbar-nav nav"><li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/acabamentos-e-cabine" data-toggle="dropdown"><h3>Acabamentos e Cabine <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w8" class="dropdown-menu"><li><h3><a href="/auto/acabamentos-e-cabine/espelhos-retrovisores-e-componentes" tabindex="-1">Espelhos Retrovisores e Componentes</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/volante" tabindex="-1">Volante</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/estribo" tabindex="-1">Estribo</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/paralama-e-componentes" tabindex="-1">Paralama e Componentes</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/parabarro" tabindex="-1">Parabarro</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/cabine-e-componentes" tabindex="-1">Cabine e Componentes</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/portas-e-componentes" tabindex="-1">Portas e componentes</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/grades-frontal-e-componentes" tabindex="-1">Grades Frontal e Componentes</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/parachoques-e-componentes" tabindex="-1">Parachoques e Componentes</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/parabrisa" tabindex="-1">Parabrisa</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/portas-cabine" tabindex="-1">Portas Cabine</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/tapa-sol" tabindex="-1">Tapa Sol </a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/ponteiras-parachoques-laterais-e-componentes" tabindex="-1">Ponteiras (Parachoques Laterais) e Componentes</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/defletores" tabindex="-1">Defletores</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/cilindro-de-basculamento" tabindex="-1">Cilindro de Basculamento</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/vlvulas-suspenso" tabindex="-1">Válvulas Suspensão</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/componentes-de-farol" tabindex="-1">Componentes de Farol</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/escovas" tabindex="-1">Escovas</a></h3></li>
                <li><h3><a href="/auto/acabamentos-e-cabine/tapassol" tabindex="-1">TAPASSOL</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/acessorios" data-toggle="dropdown"><h3>Acessórios <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w9" class="dropdown-menu"><li><h3><a href="/auto/acessorios/capa-protetora" tabindex="-1">Capa Protetora</a></h3></li>
                <li><h3><a href="/auto/acessorios/itens-de-seguranca" tabindex="-1">Itens de Segurança</a></h3></li>
                <li><h3><a href="/auto/acessorios/extintores-de-incendio" tabindex="-1">Extintores de Incêndio</a></h3></li>
                <li><h3><a href="/auto/acessorios/produtos-de-limpeza" tabindex="-1">Produtos de Limpeza</a></h3></li>
                <li><h3><a href="/auto/acessorios/cinto-de-seguranca" tabindex="-1">Cinto de Segurança</a></h3></li>
                <li><h3><a href="/auto/acessorios/tapetes-e-carpetes" tabindex="-1">Tapetes e carpetes</a></h3></li>
                <li><h3><a href="/auto/acessorios/olhal" tabindex="-1">OLHAL</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/agropet" data-toggle="dropdown"><h3>Agropet <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w10" class="dropdown-menu"><li><h3><a href="/auto/agropet/agropet" tabindex="-1">Agropet</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/autopecas" data-toggle="dropdown"><h3>Autopeças <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w11" class="dropdown-menu"><li><h3><a href="/auto/autopecas/cabos" tabindex="-1">Cabos</a></h3></li>
                <li><h3><a href="/auto/autopecas/eixo-expansor" tabindex="-1">Eixo Expansor </a></h3></li>
                <li><h3><a href="/auto/autopecas/farois" tabindex="-1">Faróis</a></h3></li>
                <li><h3><a href="/auto/autopecas/baterias-caminhes-e-onibus" tabindex="-1">Baterias Caminhões e Onibus</a></h3></li>
                <li><h3><a href="/auto/autopecas/baterias-carros-e-motos" tabindex="-1">Baterias Carros e Motos</a></h3></li>
                <li><h3><a href="/auto/autopecas/bombas" tabindex="-1">Bombas</a></h3></li>
                <li><h3><a href="/auto/autopecas/bombas-de-direo-hidraulica" tabindex="-1">Bombas de Direção Hidraulica</a></h3></li>
                <li><h3><a href="/auto/autopecas/carroceria" tabindex="-1">Carroceria</a></h3></li>
                <li><h3><a href="/auto/autopecas/cubos-de-roda" tabindex="-1">Cubos de Roda</a></h3></li>
                <li><h3><a href="/auto/autopecas/eixo-dianteiro" tabindex="-1">Eixo Dianteiro</a></h3></li>
                <li><h3><a href="/auto/autopecas/eixo-traseiro" tabindex="-1">Eixo Traseiro</a></h3></li>
                <li><h3><a href="/auto/autopecas/rolamentos-de-rodas" tabindex="-1">Rolamentos de Rodas</a></h3></li>
                <li><h3><a href="/auto/autopecas/terminal" tabindex="-1">Terminal</a></h3></li>
                <li><h3><a href="/auto/autopecas/componentes-e-peas" tabindex="-1">Componentes e Peças </a></h3></li>
                <li><h3><a href="/auto/autopecas/calotas" tabindex="-1">Calotas</a></h3></li>
                <li><h3><a href="/auto/autopecas/parte-eltrica" tabindex="-1">Parte Elétrica</a></h3></li>
                <li><h3><a href="/auto/autopecas/painel-e-instrumentos" tabindex="-1">Painel e Instrumentos</a></h3></li>
                <li><h3><a href="/auto/autopecas/acessrios" tabindex="-1">Acessórios</a></h3></li>
                <li><h3><a href="/auto/autopecas/acessrios-de-baterias" tabindex="-1">Acessórios de Baterias </a></h3></li>
                <li><h3><a href="/auto/autopecas/acessrios-de-radiador" tabindex="-1">Acessórios de Radiador</a></h3></li>
                <li><h3><a href="/auto/autopecas/bomba-de-gua" tabindex="-1">Bomba de Água</a></h3></li>
                <li><h3><a href="/auto/autopecas/aparelho-teste-de-bateria" tabindex="-1">Aparelho Teste de Bateria</a></h3></li>
                <li><h3><a href="/auto/autopecas/bomba-de-combustvel" tabindex="-1">Bomba de Combustível</a></h3></li>
                <li><h3><a href="/auto/autopecas/bomba-de-leo" tabindex="-1">Bomba de Óleo</a></h3></li>
                <li><h3><a href="/auto/autopecas/caixas-carcaas-e-componentes" tabindex="-1">Caixas, Carcaças e Componentes</a></h3></li>
                <li><h3><a href="/auto/autopecas/carburador" tabindex="-1">Carburador</a></h3></li>
                <li><h3><a href="/auto/autopecas/escapamento-e-componentes" tabindex="-1">Escapamento e Componentes</a></h3></li>
                <li><h3><a href="/auto/autopecas/lanternas" tabindex="-1">Lanternas</a></h3></li>
                <li><h3><a href="/auto/autopecas/limpador-de-parabrisa" tabindex="-1">Limpador de Parabrisa</a></h3></li>
                <li><h3><a href="/auto/autopecas/mangueiras-e-tubulaes" tabindex="-1">Mangueiras e Tubulações</a></h3></li>
                <li><h3><a href="/auto/autopecas/tampas" tabindex="-1">Tampas</a></h3></li>
                <li><h3><a href="/auto/autopecas/tanques" tabindex="-1">Tanques</a></h3></li>
                <li><h3><a href="/auto/autopecas/kit-reviso-carro" tabindex="-1">Kit Revisão Carro</a></h3></li>
                <li><h3><a href="/auto/autopecas/cilindro-mestre" tabindex="-1">Cilindro Mestre</a></h3></li>
                <li><h3><a href="/auto/autopecas/buchas-coxins-e-batentes" tabindex="-1">Buchas, Coxins e Batentes</a></h3></li>
                <li><h3><a href="/auto/autopecas/buchas-rebites-parafusos-porcas-e-arruelas" tabindex="-1">Buchas, Rebites, Parafusos, Porcas e Arruelas</a></h3></li>
                <li><h3><a href="/auto/autopecas/abraadeiras-e-presilhas" tabindex="-1">Abraçadeiras e presilhas</a></h3></li>
                <li><h3><a href="/auto/autopecas/ar-condicionado" tabindex="-1">Ar Condicionado</a></h3></li>
                <li><h3><a href="/auto/autopecas/bandejas" tabindex="-1">Bandejas</a></h3></li>
                <li><h3><a href="/auto/autopecas/barra-estabilizadora" tabindex="-1">Barra Estabilizadora</a></h3></li>
                <li><h3><a href="/auto/autopecas/barra-haste-reao" tabindex="-1">Barra Haste Reação</a></h3></li>
                <li><h3><a href="/auto/autopecas/bomba-hidrulica-cabine" tabindex="-1">Bomba Hidráulica Cabine</a></h3></li>
                <li><h3><a href="/auto/autopecas/cmbio" tabindex="-1">Câmbio</a></h3></li>
                <li><h3><a href="/auto/autopecas/componentes-de-direo" tabindex="-1">Componentes de Direção</a></h3></li>
                <li><h3><a href="/auto/autopecas/eltrica" tabindex="-1">Elétrica</a></h3></li>
                <li><h3><a href="/auto/autopecas/embuchamento" tabindex="-1">Embuchamento</a></h3></li>
                <li><h3><a href="/auto/autopecas/hidrulica" tabindex="-1">Hidráulica</a></h3></li>
                <li><h3><a href="/auto/autopecas/interruptores" tabindex="-1">Interruptores</a></h3></li>
                <li><h3><a href="/auto/autopecas/longarinas-e-chassis" tabindex="-1">Longarinas e Chassis</a></h3></li>
                <li><h3><a href="/auto/autopecas/painel-frontal" tabindex="-1">Painel Frontal</a></h3></li>
                <li><h3><a href="/auto/autopecas/peas-carreta" tabindex="-1">Peças Carreta</a></h3></li>
                <li><h3><a href="/auto/autopecas/sistema-hidrulico" tabindex="-1">Sistema Hidráulico</a></h3></li>
                <li><h3><a href="/auto/autopecas/suporte-estepe" tabindex="-1">Suporte Estepe</a></h3></li>
                <li><h3><a href="/auto/autopecas/suportes" tabindex="-1">Suportes</a></h3></li>
                <li><h3><a href="/auto/autopecas/suspenso-cabine" tabindex="-1">Suspensão Cabine</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/baterias" data-toggle="dropdown"><h3>Baterias <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w12" class="dropdown-menu"><li><h3><a href="/auto/baterias/carregadores-de-baterias" tabindex="-1">Carregadores de Baterias</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/cardan" data-toggle="dropdown"><h3>Cardan <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w13" class="dropdown-menu"><li><h3><a href="/auto/cardan/garfo" tabindex="-1">Garfo</a></h3></li>
                <li><h3><a href="/auto/cardan/engraxadeira" tabindex="-1">Engraxadeira</a></h3></li>
                <li><h3><a href="/auto/cardan/cruzeta" tabindex="-1">Cruzeta</a></h3></li>
                <li><h3><a href="/auto/cardan/cardan-completo" tabindex="-1">Cardan Completo</a></h3></li>
                <li><h3><a href="/auto/cardan/suporte-cardan" tabindex="-1">Suporte Cardan</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/colas-e-adesivos" data-toggle="dropdown"><h3>Colas e adesivos <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w14" class="dropdown-menu"><li><h3><a href="/auto/colas-e-adesivos/colas-e-adesivos" tabindex="-1">Colas e adesivos</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/diversos" data-toggle="dropdown"><h3>Diversos <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w15" class="dropdown-menu"><li><h3><a href="/auto/diversos/diversos" tabindex="-1">Diversos</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/embreagem" data-toggle="dropdown"><h3>Embreagem <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w16" class="dropdown-menu"><li><h3><a href="/auto/embreagem/kit-de-embreagem-leves" tabindex="-1">Kit de Embreagem Leves</a></h3></li>
                <li><h3><a href="/auto/embreagem/ponteira" tabindex="-1">Ponteira</a></h3></li>
                <li><h3><a href="/auto/embreagem/diferencial" tabindex="-1">Diferencial</a></h3></li>
                <li><h3><a href="/auto/embreagem/flange" tabindex="-1">Flange</a></h3></li>
                <li><h3><a href="/auto/embreagem/luvas" tabindex="-1">Luvas</a></h3></li>
                <li><h3><a href="/auto/embreagem/peas-de-embreagem" tabindex="-1">Peças de Embreagem</a></h3></li>
                <li><h3><a href="/auto/embreagem/kit-embreagem-caminhes-e-onibus" tabindex="-1">Kit Embreagem Caminhões e Onibus</a></h3></li>
                <li><h3><a href="/auto/embreagem/rolamento-e-componentes" tabindex="-1">Rolamento e Componentes</a></h3></li>
                <li><h3><a href="/auto/embreagem/cilindro-de-embreagem" tabindex="-1">Cilindro de Embreagem</a></h3></li>
                <li><h3><a href="/auto/embreagem/rolamentos-de-embreagem" tabindex="-1">Rolamentos de Embreagem</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/ferragens" data-toggle="dropdown"><h3>Ferragens <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w17" class="dropdown-menu"><li><h3><a href="/auto/ferragens/ferragens" tabindex="-1">Ferragens</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/ferramentas" data-toggle="dropdown"><h3>Ferramentas <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w18" class="dropdown-menu"><li><h3><a href="/auto/ferramentas/ferramentas" tabindex="-1">Ferramentas</a></h3></li>
                <li><h3><a href="/auto/ferramentas/cavaletes" tabindex="-1">Cavaletes</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/filtros" data-toggle="dropdown"><h3>Filtros <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w19" class="dropdown-menu"><li><h3><a href="/auto/filtros/filtro-de-ar" tabindex="-1">Filtro de Ar</a></h3></li>
                <li><h3><a href="/auto/filtros/filtro-de-ar-condicionado" tabindex="-1">Filtro de Ar Condicionado</a></h3></li>
                <li><h3><a href="/auto/filtros/filtro-de-combustvel" tabindex="-1">Filtro de Combustível</a></h3></li>
                <li><h3><a href="/auto/filtros/outros-filtros" tabindex="-1">Outros Filtros</a></h3></li>
                <li><h3><a href="/auto/filtros/filtro-de-uria-arla" tabindex="-1">Filtro de Uréia (Arla)</a></h3></li>
                <li><h3><a href="/auto/filtros/componentes-arla" tabindex="-1">Componentes ARLA</a></h3></li>
                <li><h3><a href="/auto/filtros/filtro-de-leo-lubrificante" tabindex="-1">Filtro de Óleo Lubrificante </a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/freios" data-toggle="dropdown"><h3>Freios <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w20" class="dropdown-menu"><li><h3><a href="/auto/freios/junta" tabindex="-1">Junta</a></h3></li>
                <li><h3><a href="/auto/freios/kits-de-freio" tabindex="-1">Kits de Freio</a></h3></li>
                <li><h3><a href="/auto/freios/disco-de-freio" tabindex="-1">Disco de Freio</a></h3></li>
                <li><h3><a href="/auto/freios/lona-de-freio" tabindex="-1">Lona de Freio</a></h3></li>
                <li><h3><a href="/auto/freios/pastilhas-de-freio" tabindex="-1">Pastilhas de Freio</a></h3></li>
                <li><h3><a href="/auto/freios/pontuva-e-capa" tabindex="-1">Pontuva e capa</a></h3></li>
                <li><h3><a href="/auto/freios/sensores" tabindex="-1">Sensores</a></h3></li>
                <li><h3><a href="/auto/freios/sapatas-de-freio" tabindex="-1">Sapatas de Freio</a></h3></li>
                <li><h3><a href="/auto/freios/vlvulas-de-freio" tabindex="-1">Válvulas de Freio</a></h3></li>
                <li><h3><a href="/auto/freios/compressor-do-ar-do-freio-e-componentes" tabindex="-1">Compressor do Ar do Freio e Componentes</a></h3></li>
                <li><h3><a href="/auto/freios/cuca-de-freio-e-componentes" tabindex="-1">Cuíca de Freio e Componentes</a></h3></li>
                <li><h3><a href="/auto/freios/fluido-de-freios" tabindex="-1">Fluido de freios</a></h3></li>
                <li><h3><a href="/auto/freios/freio-motor" tabindex="-1">Freio Motor</a></h3></li>
                <li><h3><a href="/auto/freios/componentes-pinca-de-freio" tabindex="-1">Componentes Pinça de Freio</a></h3></li>
                <li><h3><a href="/auto/freios/catraca-de-freio" tabindex="-1">Catraca de Freio</a></h3></li>
                <li><h3><a href="/auto/freios/tambor-de-freio" tabindex="-1">Tambor de freio</a></h3></li>
                <li><h3><a href="/auto/freios/componentes-de-freio" tabindex="-1">Componentes de Freio</a></h3></li>
                <li><h3><a href="/auto/freios/flexveis" tabindex="-1">Flexíveis</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/motor" data-toggle="dropdown"><h3>Motor <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w21" class="dropdown-menu"><li><h3><a href="/auto/motor/virabrequim" tabindex="-1">Virabrequim</a></h3></li>
                <li><h3><a href="/auto/motor/vela-aquecedora" tabindex="-1">Vela Aquecedora</a></h3></li>
                <li><h3><a href="/auto/motor/vlvulas-e-comando-de-vlvulas" tabindex="-1">Válvulas e Comando de Válvulas</a></h3></li>
                <li><h3><a href="/auto/motor/polias" tabindex="-1">Polias</a></h3></li>
                <li><h3><a href="/auto/motor/turbina-e-componentes" tabindex="-1">Turbina e Componentes</a></h3></li>
                <li><h3><a href="/auto/motor/coxim" tabindex="-1">Coxim</a></h3></li>
                <li><h3><a href="/auto/motor/reservatrio-expanso" tabindex="-1">Reservatório Expansão</a></h3></li>
                <li><h3><a href="/auto/motor/vlvulas" tabindex="-1">Válvulas</a></h3></li>
                <li><h3><a href="/auto/motor/tensores" tabindex="-1">Tensores</a></h3></li>
                <li><h3><a href="/auto/motor/correias" tabindex="-1">Correias</a></h3></li>
                <li><h3><a href="/auto/motor/hlice" tabindex="-1">Hélice</a></h3></li>
                <li><h3><a href="/auto/motor/juntas" tabindex="-1">Juntas</a></h3></li>
                <li><h3><a href="/auto/motor/kit-troca-de-leo-de-motor-e-filtros" tabindex="-1">Kit Troca de Óleo de Motor e Filtros</a></h3></li>
                <li><h3><a href="/auto/motor/radiadores" tabindex="-1">Radiadores</a></h3></li>
                <li><h3><a href="/auto/motor/velas-de-ignio" tabindex="-1">Velas de Ignição</a></h3></li>
                <li><h3><a href="/auto/motor/chave-ignio" tabindex="-1">Chave Ignição</a></h3></li>
                <li><h3><a href="/auto/motor/cilindro-de-acionamento" tabindex="-1">Cilindro de Acionamento</a></h3></li>
                <li><h3><a href="/auto/motor/coletores" tabindex="-1">Coletores</a></h3></li>
                <li><h3><a href="/auto/motor/componentes-de-ignio" tabindex="-1">Componentes de Ignição</a></h3></li>
                <li><h3><a href="/auto/motor/sensores" tabindex="-1">Sensores</a></h3></li>
                <li><h3><a href="/auto/motor/tomada-de-fora" tabindex="-1">TOMADA DE FORÇA </a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/oleo" data-toggle="dropdown"><h3>Óleo <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w22" class="dropdown-menu"><li><h3><a href="/auto/oleo/graxa-rolamento" tabindex="-1">Graxa Rolamento</a></h3></li>
                <li><h3><a href="/auto/oleo/kit-3-litros-de-oleo-de-motor" tabindex="-1">Kit 3 Litros de Óleo de Motor</a></h3></li>
                <li><h3><a href="/auto/oleo/kit-4-litros-de-oleo-de-motor" tabindex="-1">Kit 4 Litros de Óleo de Motor</a></h3></li>
                <li><h3><a href="/auto/oleo/oleo-de-motor" tabindex="-1">Óleo de Motor</a></h3></li>
                <li><h3><a href="/auto/oleo/oleo-hidraulico" tabindex="-1">Óleo Hidráulico</a></h3></li>
                <li><h3><a href="/auto/oleo/oleo-cambio-transmissao" tabindex="-1">Óleo Cambio e Transmissão</a></h3></li>
                <li><h3><a href="/auto/oleo/galao-de-20-litros-oleo-de-motor-diesel" tabindex="-1">Galão de 20 Litros Óleo de Motor Diesel</a></h3></li>
                <li><h3><a href="/auto/oleo/graxa-chassi" tabindex="-1">Graxa Chassi</a></h3></li>
                <li><h3><a href="/auto/oleo/carter-leo" tabindex="-1">Carter Óleo</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/pinturas" data-toggle="dropdown"><h3>Pinturas <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w23" class="dropdown-menu"><li><h3><a href="/auto/pinturas/pintura" tabindex="-1">Pintura</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/pneus" data-toggle="dropdown"><h3>Pneus <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w24" class="dropdown-menu"><li><h3><a href="/auto/pneus/pneus-aro-13" tabindex="-1">Pneus Aro 13</a></h3></li>
                <li><h3><a href="/auto/pneus/pneus-aro-14" tabindex="-1">Pneus Aro 14</a></h3></li>
                <li><h3><a href="/auto/pneus/pneus-aro-15" tabindex="-1">Pneus Aro 15</a></h3></li>
                <li><h3><a href="/auto/pneus/pneus-aro-16" tabindex="-1">Pneus Aro 16</a></h3></li>
                <li><h3><a href="/auto/pneus/pneus-de-moto" tabindex="-1">Pneus de Moto</a></h3></li>
                <li><h3><a href="/auto/pneus/pneus-aro-17" tabindex="-1">Pneus Aro 17</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/servicos-automotivos" data-toggle="dropdown"><h3>Serviços Automotivos <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w25" class="dropdown-menu"><li><h3><a href="/auto/servicos-automotivos/servicos-de-motor" tabindex="-1">Serviços de Motor</a></h3></li>
                <li><h3><a href="/auto/servicos-automotivos/servico-de-leva-e-traz" tabindex="-1">Serviço de Leva e Traz</a></h3></li>
                <li><h3><a href="/auto/servicos-automotivos/servicos-de-suspensao" tabindex="-1">Serviços de Suspensão</a></h3></li>
                <li><h3><a href="/auto/servicos-automotivos/servicos-de-limpeza" tabindex="-1">Serviços de Limpeza</a></h3></li>
                <li><h3><a href="/auto/servicos-automotivos/servicos-de-cambio" tabindex="-1">Serviços de Cambio</a></h3></li>
                <li><h3><a href="/auto/servicos-automotivos/manutencao-preventiva" tabindex="-1">Manutenção Preventiva</a></h3></li>
                <li><h3><a href="/auto/servicos-automotivos/servicos-de-pneus" tabindex="-1">Serviços de Pneus</a></h3></li>
                <li><h3><a href="/auto/servicos-automotivos/servicos-de-freios" tabindex="-1">Serviços de Freios</a></h3></li>
                <li><h3><a href="/auto/servicos-automotivos/servicos-de-direcao" tabindex="-1">Serviços de Direção</a></h3></li></ul></li>
                <li class="dropdown"><a class="dropdown-toggle disabled" href="/auto/suspensao" data-toggle="dropdown"><h3>Suspensão <b class="fa fa-caret-right pull-right"></b></h3></a><ul id="w26" class="dropdown-menu"><li><h3><a href="/auto/suspensao/peas-suspenso" tabindex="-1">Peças Suspensão</a></h3></li>
                <li><h3><a href="/auto/suspensao/amortecedor-a-gas-tampa" tabindex="-1">Amortecedor a gás tampa</a></h3></li>
                <li><h3><a href="/auto/suspensao/amortecedores" tabindex="-1">Amortecedores</a></h3></li></ul></li></ul></div></div>
        	</nav>
        <!-- Término Menu -->

        <div class="row barradeinformacoes">
            <!--<div class="visible-lg text-center">
                <div class="panelPart col-lg-2">
                    <div class="texto">
                        Entrega em todo Brasil
                    </div>
                </div>
                <div class="panelPart col-lg-2">
                    <div class="texto">
                        Pague em até 6x
                    </div>
                </div>
                <div class="panelPart col-lg-2 text-left">
                    <a href="https://pecaagora.zendesk.com/hc/pt-br/articles/206974937-O-que-%C3%A9-Compra-Garantida-e-Como-Funciona-"
                       target="_blank">
                        <div class="texto">
                            Compra Garantida
                        </div>
                    </a>
                </div>
                <div class="panelPart col-lg-2 text-left">
                    <a href="https://pecaagora.com/site/nossaloja"
                       target="_blank">
                        <div class="texto">
                            Nossas Lojas
                        </div>
                    </a>
                </div>
            </div>-->
	    <div class="visible-lg text-center">
                <div class="panelPart col-lg-1" >
                	<div >
                        <img src="<?= yii::$app->urlManager->baseUrl . "/frontend/web/assets/img/caminhao_aviao2.png" ?>"  width="80" height="80"> 
                    </div>
                </div>
                <div class="panelPart col-lg-3  text-left">
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
    <footer class="footer">
        <div class="container">
            <div class="container footer-sitemap hidden-print">
                <div class="footer-sitemap-col col-sm-3">
                    <p class="h4">Páginas do Lojista</p>
                    <ul class="list-unstyled">
                        <li><a rel="nofollow" href="<?= Url::to(['/lojista/web']) ?>">Entrar em minha loja</a></li>
                        <!--<li><a href="#">Contato</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Como Funciona</a></li>
                        <li><a href="#">Lojas</a></li>
                        <li><a href="#">Investidores</a></li>
                        <li><a href="#">Marcas</a></li>-->
                    </ul>
                    <p class="h4">Navegação</p>
                    <ul class="list-unstyled">
                        <li><a href="<?= Url::to(['/auto']) ?>">Categorias</a></li>
                        <li><a href="<?= Url::to(['/veiculos/carros']) ?>">Carros</a></li>
                        <li><a href="<?= Url::to(['/veiculos/caminhoes']) ?>">Caminhões</a></li>
                    </ul>
                </div>
                <div class="footer-sitemap-col col-sm-3">
                    <p class="h4">Sobre</p>
                    <ul class="list-unstyled">
                        <li><a rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/site/sobre'; ?>">Quem
                                Somos</a></li>
                        <li><a rel="nofollow" href="<?= Yii::$app->urlManager->baseUrl . '/site/politicas'; ?>">Políticas
                                de uso</a>
                        </li>
                    </ul>
                    <p class="h4">Parceiros</p>
                    <ul class="list-unstyled">
                        <li><a href="https://www.sebraemg.com.br/atendimento/Home/HomePortal.aspx" rel="nofollow" target="_blank">
                                <img src="<?= Url::to('@assets/img/parceiros/Imagem2.png') ?>" width="150"></a></li>
                        <li><a href="https://endeavor.org.br/" rel="nofollow" target="_blank"><img src="<?= Url::to('@assets/img/parceiros/Imagem3.png') ?>" width="150"></a>
                        </li>
                        <li><a href="http://www.setcjf.org.br/" rel="nofollow" target="_blank"><img src="<?= Url::to('@assets/img/parceiros/Imagem5.png') ?>" width="150"></a>
                        </li>
                        <li>
                            <a href="http://www.ufjf.br/critt/" rel="nofollow" target="_blank"><img src="<?= Url::to('@assets/img/parceiros/Imagem1.png') ?>" width="75"></a>
                            <a href="http://www.ufjf.br/" rel="nofollow" target="_blank"><img src="<?= Url::to('@assets/img/parceiros/Imagem4.png') ?>" width="75"></a>
                        </li>
                    </ul>
                </div>
                <div class="footer-sitemap-col col-sm-3">
                    <p class="h4">Formas de Pagamento</p>
                    <ul class="list-unstyled">
                        <li class="pagamento">
                            <i title="Visa" class="fa fa-2x fa-cc-visa"></i>
                            <i title="Mastercard" class="fa fa-2x fa-cc-mastercard"></i>
                            <i title="American Express" class="fa fa-2x fa-cc-amex"></i>
                        </li>
                        <br/>
                        <li class="pagamento">
                            <i title="Discover" class="fa fa-2x fa-cc-discover"></i>
                            <i title="Stripe" class="fa fa-2x fa-cc-stripe"></i>
                            <i title="Boleto Bancário" class="fa fa-2x fa-barcode"></i>

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
                <div class="footer-sitemap-col col-sm-3">
                    <p class="h4">Redes Sociais</p>
                    <ul class="list-unstyled">
                        <li class="pagamento">
                            <a href="https://www.facebook.com/pecaagora" rel="publisher" target="_blank"><i
                                        title="Facebook" class="fa fa-2x fa-facebook-square"></i></a>
                            <a href="https://twitter.com/peca_agora" rel="publisher" target="_blank"><i title="Twitter"
                                                                                                        class="fa fa-2x fa-twitter-square"></i></a>
                            <a href="https://www.linkedin.com/company/9302941?trk=tyah&trkInfo=idx%3A1-1-1%2CtarId%3A1424719464082%2Ctas%3Ape%C3%A7a+agora"
                               rel="publisher" target="_blank"><i title="LinkedIn"
                                                                  class="fa fa-2x fa-linkedin-square"></i></a>
                        </li>
                        <li class="pagamento">
                            <a href="https://plus.google.com/114492497861685611853/posts" rel="publisher"
                               target="_blank"><i
                                        title="Google Plus" class="fa fa-2x fa-google-plus-square"></i></a>
                            <a href="https://www.youtube.com/channel/UCpur5CZ934mIJ_BCXViDFsQ/feed" rel="publisher"
                               target="_blank"><i title="Youtube" class="fa fa-2x fa-youtube-square"></i></a>
                        </li>
                    </ul>
                    <div class=" ">
                        <iframe
                                src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fpecaagora&tabs&width=250&height=214&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId"
                                width="250" height="214" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
                    </div>
                    <address>
                        <strong>
                            <a rel="nofollow" href="<?= 'https://pecaagora.zendesk.com/hc/pt-br' ?>" target="_blank">
                                <i class="fa fa-comments-o"></i>&nbsp;&nbsp;Central de Atendimento</a>

                        </strong><br>
                    </address>
                    <address>
                        <strong>Atendimento</strong><br>
                        Telefone: (32) 3015-0023<br>
                        Email: <a href="mailto:sac@pecaagora.com">sac@pecaagora.com</a>
                    </address>
                </div>


            </div>
            <hr/>
            <p class="text-center">www.pecaagora.com / OPT Soluções Comércio de Produtos e Serviços Automotivos LTDA /
                CNPJ nº 18.947.338/0001-10 / Inscrição Estadual 0026402800023 / Endereço:
                Rua
                José
                Lourenço Kelmer, s/nº, Campus Universitário UFJF, Centro Regional de Inovação e Transferência de
                Tecnologia,
                Juiz de Fora – MG - 36036-900
            </p>

            <p class="text-center"> &copy; PeçaAgora <?= date('Y') ?> - Todos os Direitos Reservados</p>
        </div>
    </footer>

    <?php $this->endBody() ?>
    <script id="dsq-count-scr" src="//peaagora.disqus.com/count.js" async></script>

</body>

</html>
<?php $this->endPage() ?>
