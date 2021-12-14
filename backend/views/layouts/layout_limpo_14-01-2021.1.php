<?php
use backend\assets\AppAsset;
use frontend\widgets\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
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
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="page-header">
    <!-- web -->
    <div class="clearfix col-md-12 cab_fixo  " id="sumir" style="padding: 1px;  background: linear-gradient(#f7f7f7, #f7f7f7); height: 56px">
        <div class="container">
            <div class="col-xs-2 col-md-2">
                <a href="<?= Url::to(['/consulta-expedicao']) ?>">
                    <img style="padding-top: 5px; width: 180px " class="logo" alt="Peca Agora" title="Peça Agora" src="/frontend/web/assets/img/pecaagora_padrao.png">
                </a>
            </div>
            <div class="col-xs-10 col-md-10" style="padding-top: 6px">
                <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/consulta-expedicao/busca']) ?>">
                    <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left: 0px !important;padding-right: 0px !important;">
                        <input type="text"
                               name="codigo_pa"
                               id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist"
                               placeholder="Código PA ..."
                               autofocus="true">
                        <span class="input-group-btn">
							<button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
		                </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="page-header-menu">
    <?php
    NavBar::begin();
    $menuItems = [
        ['label' => '<i class="fa fa-tasks"></i> Pedidos Mercado Livre', 'url' => ['/pedidos-mercado-livre-expedicao/index']],
        ['label' => '<i class="fa fa-search"></i> Consulta Expedição', 'url' => ['/consulta-expedicao']],
        ['label' => '<i class="fa fa-search"></i> Consulta Expedição Estoque', 'url' => ['/consulta-expedicao-estoque']],
        [
            'label' => '<i class="fa fa-power-off"></i> Sair (' . Yii::$app->user->identity->nome . ')',
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post']
        ]
    ];
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => $menuItems,
        'encodeLabels' => false
    ]);
    NavBar::end();
    ?>
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

</body>

</html>
<?php $this->endPage() ?>

