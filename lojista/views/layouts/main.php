<?php
use frontend\widgets\Alert;
use lojista\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;


/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
$this->registerJs("baseUrl ='" . Yii::$app->urlManager->baseUrl . "'", \yii\web\View::POS_BEGIN);
$this->registerJs("frontendBaseUrl ='" . Yii::$app->urlManagerFrontEnd->baseUrl . "'", \yii\web\View::POS_BEGIN);
$this->registerJs("serverUrl ='" . Yii::$app->request->serverName . "'", \yii\web\View::POS_BEGIN);

?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<?php
if (!YII_DEBUG) {
    echo $this->render('scriptZendesk');
}
?>
<head>
    <link rel="icon" href="<?= yii::$app->urlManagerFrontEnd->baseUrl . '/assets/img/favicon.ico' ?>" sizes="16x16"
          type="image/png">
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
          type="text/css">
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="page-header">
    <!-- BEGIN HEADER TOP -->
    <div class="page-header-top">
        <div class="container">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="<?= Url::base() ?>/"><img class="logo-lojista" style="margin-top:20px; margin-bottom: 10px;"
                                                   src="<?= Url::to('@frontend_assets/'); ?>img/pecaagora.png">
                </a>
            </div>
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:" class="menu-toggler"></a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    <!-- END TODO DROPDOWN -->
                    <li class="dropdown dropdown-user dropdown-dark">
                        <a href="<?= 'https://pecaagora.zendesk.com/hc/pt-br' ?>" class="dropdown-toggle"
                           target="_blank"><i class="fa fa-info-circle"></i>
                            <span class="username username-hide-mobile">Ajuda</span></a>
                    </li>

                    <li class="dropdown dropdown-separator">
                        <span class="separator"></span>
                    </li>
                    <!-- BEGIN USER LOGIN DROPDOWN -->

                    <li class="dropdown dropdown-user dropdown-dark">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                           data-close-others="true">
                            <span
                                class="username username-hide-mobile"><?= (Yii::$app->user->getIdentity()->nome) ?></span>
                        </a>
                    </li>
                    <li class="dropdown dropdown-extended dropdown-dark dropdown-tasks" id="header_task_bar">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                           data-close-others="true">
                            <i class="icon-settings"></i>
                        </a>
                    </li>
                    <li class="dropdown dropdown-extended dropdown-dark dropdown-notification"
                        id="header_notification_bar">
                        <a href="<?= Url::to(['site/logout']) ?>" class="dropdown-toggle" data-toggle=""
                           data-hover=""
                           data-close-others="">
                            <i class="icon-power"></i>
                        </a>
                    </li>
                    <!-- END NOTIFICATION DROPDOWN -->
                    <!-- BEGIN TODO DROPDOWN -->

                    <!-- END USER LOGIN DROPDOWN -->
                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
    </div>
    <!-- END HEADER TOP -->
    <div class="page-header-menu">
        <div class="container">
            <!-- BEGIN MEGA MENU -->
            <!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
            <!-- DOC: Remove data-hover="dropdown" and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
            <div class="hor-menu ">
                <ul class="nav navbar-nav">
                    <li class="active menu-dropdown">
                        <a href="<?= Url::base() ?>/" class="tooltips" data-container="body"
                           data-placement="bottom" data-html="true" data-original-title="Página Inicial">
                            <i class="fa fa-bar-chart"></i>
                            Painel Administrativo </a>
                    </li>
                    <li class="menu-dropdown">
                        <a href="<?= Url::to(['/pedidos']) ?>" target="">
                            <i class="fa fa-truck"></i>
                            Pedidos
                        </a>
                    </li>
                    <li class="menu-dropdown">
                        <a href="#" target="">
                            <i class="fa fa-calculator"></i>
                            Limites
                        </a>
                    </li>
                    <li class="menu-dropdown">
                        <a href="<?= Url::to(['/estoque']) ?>" target="">
                            <i class="fa fa-shopping-cart"></i>
                            Estoque
                        </a>
                    </li>
                    <li class="menu-dropdown">
                        <a href="<?= Url::to(['/atributos']) ?>" target="">
                            <i class="fa fa-certificate"></i>
                            Atributos
                        </a>
                    </li>
                    <li class="menu-dropdown">
                        <a href="<?= Url::to(['/transportadora']) ?>" target="">
                            <i class="fa fa-truck"></i>
                            Transportadoras
                        </a>
                    </li>
                    <li class="menu-dropdown">
                        <a href="<?= Url::to(['/lojista']) ?>" target="">
                            <i class="fa fa-truck"></i>
                            Correios
                        </a>
                    </li>
                    <li class="menu-dropdown">
                        <a href="<?= Url::to(['/minha-conta']) ?>" target="">
                            <i class="fa fa-user"></i>
                            Minha Conta
                        </a>
                    </li>
                    <li class="menu-dropdown">
                        <a href="<?= Url::to(['/mercado-livre']) ?>" target="">
                            <i class="fa fa-money"></i>
                            Mercado Livre
                        </a>
                    </li>
                </ul>
            </div>
            <!-- END MEGA MENU -->
        </div>
    </div>
    <!-- END HEADER MENU -->
</div>
<!-- END HEADER -->
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
            <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Modal title</h4>
                        </div>
                        <div class="modal-body">
                            Widget settings form goes here
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn blue">Save changes</button>
                            <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
            <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <!-- BEGIN PAGE BREADCRUMB -->
            <?= Breadcrumbs::widget(
                [
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],

                ]
            ) ?>
            <?= Alert::widget() ?>
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
            <div class="col-md-3 col-sm-6 col-xs-12 footer-block">
                <h2>SOBRE</h2>

                <p>
                    Peça Agora: o Shopping online onde você pode vender as melhores peças, baterias e pneus para
                    veículos leves e pesados.
                </p>
            </div>
            <div class="col-md-3 col-sm-6 col-xs12 footer-block">

            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 footer-block">

            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 footer-block">
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
    jQuery(document).ready(function () {
        Metronic.init(); // init metronic core components
        Index.init();
    });
</script>

</body>

</html>
<?php $this->endPage() ?>

