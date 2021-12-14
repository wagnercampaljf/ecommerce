<?php
use lojista\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

//AppAsset::register($this);
//$this->registerJs("baseUrl ='" . Yii::$app->urlManager->baseUrl . "'", \yii\web\View::POS_BEGIN);

?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <link rel="icon" href="<?= yii::$app->urlManagerFrontEnd->baseUrl.'/assets/img/favicon.ico'?>" sizes="16x16" type="image/png">
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
          type="text/css">
    <link href="<?= Url::to('@assets/'); ?>/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"
          type="text/css">
    <link href="<?= Url::to('@assets/'); ?>/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet"
          type="text/css">
    <link href="<?= Url::to('@assets/'); ?>/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"
          type="text/css">
    <link href="<?= Url::to('@assets/'); ?>/global/plugins/uniform/css/uniform.default.css" rel="stylesheet"
          type="text/css">
    <!-- END GLOBAL MANDATORY STYLES -->
    <link href="<?= Url::to('@assets/'); ?>/admin/pages/css/login.css" rel="stylesheet" type="text/css"/>
    <!-- BEGIN THEME STYLES -->
    <link href="<?= Url::to('@assets/'); ?>/global/css/components-rounded.css" id="style_components" rel="stylesheet"
          type="text/css">
    <link href="<?= Url::to('@assets/'); ?>/global/css/plugins.css" rel="stylesheet" type="text/css">
    <link href="<?= Url::to('@assets/'); ?>/admin/layout3/css/layout.css" rel="stylesheet" type="text/css">
    <link href="<?= Url::to('@assets/'); ?>/admin/layout3/css/themes/default.css" rel="stylesheet" type="text/css"
          id="style_color">
    <link href="<?= Url::to('@assets/'); ?>/admin/layout3/css/custom.css" rel="stylesheet" type="text/css">

</head>
<body class="login">
<?php $this->beginBody() ?>
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGO -->
<div class="logo">

</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
    <?= $content ?>
</div>
<div class="copyright">
    2014 © PeçaAgora. Todos os Direitos Reservados.
</div>
<div class="scroll-to-top">
    <i class="icon-arrow-up"></i>
</div>
<script src="<?= Url::to('@assets/'); ?>/global/plugins/respond.min.js"></script>
<script src="<?= Url::to('@assets/'); ?>/global/plugins/excanvas.min.js"></script>
<script src="<?= Url::to('@assets/'); ?>/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="<?= Url::to('@assets/'); ?>/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<!--<script src="<?= Url::to('@assets/'); ?>/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>-->
<script src="<?= Url::to('@assets/'); ?>/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!--<script src="<?= Url::to('@assets/'); ?>/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"-->
<!--        type="text/javascript"></script>-->
<!--<script src="<?= Url::to('@assets/'); ?>/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>-->
<script src="<?= Url::to('@assets/'); ?>/global/plugins/jquery.bui.min.js" type="text/javascript"></script>
<script src="<?= Url::to('@assets/'); ?>/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?= Url::to('@assets/'); ?>/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<script src="<?= Url::to('@assets/'); ?>/global/scripts/metronic.js" type="text/javascript"></script>
<script src="<?= Url::to('@assets/'); ?>/admin/layout3/scripts/layout.js" type="text/javascript"></script>
<script src="<?= Url::to('@assets/'); ?>/admin/layout3/scripts/demo.js" type="text/javascript"></script>
<script src="<?= Url::to('@assets/'); ?>/admin/pages/scripts/login.js" type="text/javascript"></script>
<script>
    jQuery(document).ready(function () {
        Metronic.init(); // init metronic core components
        Layout.init(); // init current layout
        Demo.init(); // init demo features
        Login.init();

    });
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
