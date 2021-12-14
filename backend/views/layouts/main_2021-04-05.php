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
    <?php $this->beginBody() ?>
    <div class="page-header">
        <div class="page-header-menu">
            <?php
            NavBar::begin();
            
            $menuItems = [
                ['label' => '<i class="fa fa-tasks"></i> Pedidos Peça Agora', 'url' => ['/pedidos/index']],
                ['label' => '<i class="fa fa-tasks"></i> Pedidos Mercado Livre', 'url' => ['/pedidos-mercado-livre/index']],
                ['label' => '<i class="fa fa-cogs"></i> Produtos', 'url' => ['/produto/index']],
                ['label' => '<i class="glyphicon glyphicon-picture"></i> Imagens', 'url' => ['/imagens']],
                ['label' => '<i class="fa fa-users"></i> Compradores', 'url' => ['/compradores/index']],
                ['label' => '<i class="fa fa-bullhorn"></i> Anúncios', 'url' => ['/banner/index']],
                ['label' => '<i class="fa fa-sitemap"></i> Categorias', 'url' => ['/categoria/index']],
                ['label' => '<i class="fa fa-sitemap"></i> SubCategorias', 'url' => ['/sub-categoria/index']],
                ['label' => '<i class="fa fa-car"></i> Modelo', 'url' => ['/modelo/index']],
                ['label' => '<i class="fa fa-maxcdn"></i> Marca', 'url' => ['/marca/index']],
                ['label' => '<i class="fa fa-users"></i> Fabricante', 'url' => ['/fabricante/index']],
                ['label' => '<i class="fa fa-book"></i> Estoque', 'url' => ['/produto-filial']],
                ['label' => '<i class="glyphicon">&#xe148;</i> Valor', 'url' => ['/valor-produto-filial']],
                ['label' => '<i class="fa fa-book"></i> Marca(Produto)', 'url' => ['/marca-produto']],
                ['label' => '<i class="fa fa-book"></i> Garantia', 'url' => ['/formulario-garantia']],
                ['label' => '<i class="fa fa-search"></i> Consulta Expedição', 'url' => ['/consulta-expedicao']],
                ['label' => '<i class="fa fa-users"></i> Filiais', 'url' => ['/filial/index']],
                ['label' => '<i class="fa fa-search"></i> Pesquisar Produtos', 'url' => ['/produto/pesquisar']],
                ['label' => '<i class="fa fa-users"></i> Markup', 'url' => ['/markup-mestre/index']],
                ['label' => '<i class="fa fa-shopping-cart"></i> Compras', 'url' => ['/pedido-compra/index']],
                ['label' => '<i class="fa fa-google-wallet"></i> Movimentação Estoque', 'url' => ['/produto/movimentacao-estoque']],
                [
                    'label' => '<i class="fa fa-power-off"></i> Sair (' . Yii::$app->user->identity->nome . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ]
            ];
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
                'encodeLabels' => false
            ]);
            NavBar::end();
            ?>
        </div>
    </div>
    <!-- BEGIN PAGE CONTAINER -->
    <div class="page-container">
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
            www.pecaagora.com / OPT Soluções LTDA / CNPJ nº 18.947.338/0001-10 / Endereço: Rua José Lourenço Kelmer, s/nº,
            Campus Universitário UFJF, Centro Regional de Inovação e Transferência de Tecnologia, Juiz de Fora – MG -
            36036-900<br>
            2014 &copy; PeçaAgora. Todos os Direitos Reservados.
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