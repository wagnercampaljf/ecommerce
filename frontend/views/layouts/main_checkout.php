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
use \frontend\widgets\MenuWidget;use common\models\Filial;

use kartik\date\DatePicker;
use vendor\iomageste\Moip\Moip;
use yii\debug\components\search\matchers\Base;
use yii\helpers\ArrayHelper;

use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\validators\Validator;

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

    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '493480597927084');
        fbq('track', 'NewPageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=493480597927084&ev=PageView&noscript=1"
        /></noscript>
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
    

    <!-- Facebook Pixel Code 2021-05-06 -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '3846656438767066');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=3846656438767066&ev=PageView&noscript=1"
        /></noscript>
    <!-- End Facebook Pixel Code -->

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
<!--<div id="navigation" style="background-color: #007576;"></div>-->

<div class="container">
    <div class="col-lg-2 col-sm-4 col-xs-12 logo-wrap hidden-xs" style="margin-top:-5px;margin-bottom: 10px;z-index: 99">
        <div>
            <a href="<?= Url::to(['/']) ?>">
                <img style="width: 168px; height:43px; padding-top: 5px " class="logo" alt="Peca Agora" title="Peça Agora"
                     src="<?= Url::to('@assets/'); ?>img/pecaagora_padrao.png">
            </a>
        </div>
    </div>


    <nav class="slidemenu">



        <!-- Item 1 -->
        <input type="radio" name="slideItem" id="slide-item-1" class="slide-toggle" disabled/>
        <label for="slide-item-1"><p class="icon"><i class="fa fa-cart-plus" aria-hidden="true"></i></p><span>Meu carrinho</span></label>

        <!-- Item 2 -->
        <input type="radio" name="slideItem" id="slide-item-2" class="slide-toggle" disabled/>
        <label for="slide-item-2"><p class="icon"><i class="fa fa-address-card-o" aria-hidden="true"></i></p><span>Endereço</span></label>

        <!-- Item 3 -->
        <input type="radio" name="slideItem" id="slide-item-3" class="slide-toggle"  checked/>
        <label for="slide-item-3"><p class="icon"><i class="fa fa-money" aria-hidden="true"></i></p><span>Pagamento</span></label>

        <!-- Item 4 -->
        <input type="radio" name="slideItem" id="slide-item-4" class="slide-toggle" disabled/>
        <label for="slide-item-4"><p class="icon"><i class="fa fa-check-circle-o" aria-hidden="true"></i></p><span>Pedido efetuado</span></label>

        <div class="clear"></div>

        <!-- Bar -->
        <div class="slider">
            <div class="bar"></div>
        </div>

    </nav>
</div>




<div class=" col-sm-4"></div>
<div class=" col-sm-12">
    <i class="fa fa-lock fa-lg" aria-hidden="true"></i><span style="color: #007576"><b> AMBIENTE 100% SEGURO</b></span>
</div>

<style>

    .clear{
        clear: both;
    }


    .slide-toggle{
        display: none;
    }

    .slidemenu{
        font-family: arial, sans-serif;
        max-width: 1000px;
        margin: 10px auto;
        overflow: hidden;

    }

    .slidemenu label{
        width: 25%;
        text-align: center;
        display: block;
        float: left;
        color: #007576;
        opacity: 0.2;
        font-size: 13px;

    }

    .slidemenu label:hover{
        cursor: pointer;
        color: #007576;
    }

    .slidemenu label span{
        display: block;
        padding: 2px;
    }

    .slidemenu label .icon{
        font-size: 18px;
        border: solid 1px #007576;
        text-align: center;
        height: 30px;
        width: 30px;
        display: block;
        margin: 0 auto;
        line-height: 30px;
        border-radius: 50%;
    }

    /*Bar Style*/

    .slider{
        width: 100%;
        height: 5px;
        display: block;
        background: #ccc;
        margin-top: 2px;
        border-radius: 5px;
    }

    .slider .bar{
        width: 25%;
        height: 5px;
        background: #007576;
        border-radius: 5px;
    }

    /*Animations*/
    .slidemenu label, .slider .bar {
        transition: all 500ms ease-in-out;
        -webkit-transition: all 500ms ease-in-out;
        -moz-transition: all 500ms ease-in-out;
    }

    /*Toggle*/

    .slidemenu .slide-toggle:checked + label{
        opacity: 1;
    }



    .slidemenu #slide-item-1:checked ~ .slider .bar{ margin-left: 0; }
    .slidemenu #slide-item-2:checked ~ .slider .bar{ margin-left: 25%; }
    .slidemenu #slide-item-3:checked ~ .slider .bar{ margin-left: 50%; }
    .slidemenu #slide-item-4:checked ~ .slider .bar{ margin-left: 75%; }

</style>



<div class="container ">
   <div class=" row col-sm-12">
        <!--<div class="panel panel-default col-md-12">-->



        <?= Breadcrumbs::widget(
        [
            'encodeLabels' => false,
            'homeLink' => ['label' => Html::a('Página Inicial', Url::to(['site/index']))],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]
    ) ?>
        </div>

        <?= Alert::widget() ?>

        <?= $content ?>
    </div>
</div>



<!-- RODAPÉ -->


<?php $this->endBody() ?>
<script id="dsq-count-scr" src="//peaagora.disqus.com/count.js" async></script>


</body>

</html>
<?php $this->endPage() ?>

