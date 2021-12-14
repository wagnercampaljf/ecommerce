<?php

use app\models\Newsletter;
use common\models\Produto;
use common\models\ValorProdutoFilial;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use common\models\Categoria;
use frontend\widgets\FormSearch;
use frontend\widgets\Menu;
use common\models\ValorProdutoMenorMaior;



/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Distribuidora de Autopeças Diese';
//echo Yii::$app->security->generatePasswordHash('1q2w3e');die;
$this->registerLinkTag(['rel' => 'canonical', 'href' => 'https://www.pecaagora.com']);
$this->registerMetaTag(['name' => 'description', 'content' => 'O melhor preço em autopeças para carros, motos, caminhões, ônibus e tratores. Confira nossas ofertas e compre online! Entregamos em todo o Brasil!']);
?>

<h1 class="span-logo">Peça Agora</h1>
<h2 class="span-logo">O shopping online de autopeças seu carro, moto e caminhao.</h2>
</div>

<div class="visible-xs"><br></div>

<!-- Carousel Imagens
<div id="myCarousel" class="carousel slide hidden-xs" data-ride="carousel">

    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
        <li data-target="#myCarousel" data-slide-to="3"></li>
        <li data-target="#myCarousel" data-slide-to="4"></li>
        <li data-target="#myCarousel" data-slide-to="5"></li>

    </ol>


    <div class="carousel-inner ">
        <div class="item active">
            <a href="https://www.pecaagora.com/p/8673/cuica-freio-spring-brake-30-x-30-haste-longa-carretas-trucks-randon-guerra-ford-vw-mb-iveco-2rr607947"><img src="/pecaagora/frontend/web/assets/img/0.webp" width="100%" height="100%"></a>
        </div>

        <div class="item">
            <a href="https://www.pecaagora.com/p/231651/leo-15w40-ci4-motor-diesel-truck-turbo-20-litros-k15w40"><img src="<?= Url::to('@assets/'); ?>img/1.webp" width="100%" height="100%"></a>
        </div>

        <div class="item">
            <a href="https://www.pecaagora.com/p/231646/leo-68-hidrulico-20-litros-servios-leves-e-moderados-industrial-automotivo-k68"><img src="<?= Url::to('@assets/'); ?>img/2.webp" width="100%" height="100%"></a>
        </div>

        <div class="item">
            <img src="<?= Url::to('@assets/'); ?>img/3.webp" width="100%" height="100%">
        </div>

        <div class="item">
            <img src="<?= Url::to('@assets/'); ?>img/4.webp" width="100%" height="100%">
        </div>

        <div class="item">
            <img src="<?= Url::to('@assets/'); ?>img/5.webp" width="100%" height="100%">
        </div>
    </div>


    <a class="left carousel-control " style="width: 3%" href="#myCarousel" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left"></span>

    </a>
    <a class="right carousel-control" style="width: 4%" href="#myCarousel" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right"></span>

    </a>
</div><br>-->
<!-- Carousel Imagens -->


<!-- BANNERS IMAGENS -->
<div class="container " style="background-color: #f7f7f7; border-radius: 10px">
    <div class="row">
        <div class="col-md-12 col-sm-12 co-xs-12 gal-item"><br>
            <a href="https://www.pecaagora.com/search?nome=Acabamentos%20e%20Cabine"><img src="<?= Url::to('@assets/'); ?>img/banner_padrao3.jpg"  class="img-ht img-fluid rounded acende img-responsive" alt="imagem responsiva"></a>
        </div>
        <div class="row">
            <div class="col-md-8 col-sm-12 co-xs-12 gal-item">
                <div class="row h-50">
                    <div class="col-md-12 col-sm-12 co-xs-12 gal-item">
                        <div class="box">
                            <a href="https://www.pecaagora.com/p/284844/par-farol-principal-mb-axor-ld-le-mercedes-benz-p-9408200261"><img src="<?= Url::to('@assets/'); ?>img/banner-farol.jpg"  class="img-ht img-fluid rounded acende img-responsive" style="width:100%; height: 100%" alt="imagem responsiva"></a>
                        </div>
                    </div>
                </div>
                <div class="row h-50">
                    <div class="col-md-12 col-sm-12 co-xs-12 gal-item">
                        <div class="box">
                            <img src="<?= Url::to('@assets/'); ?>img/banner2.webp"  class="img-ht img-fluid rounded acende img-responsive" style="width:100%; height: 100%" alt="imagem responsiva">
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-4 col-sm-6 co-xs-12 gal-item">
                <div class="col-md-12 col-sm-6 co-xs-12 gal-item h-25">
                    <div class="box">
                        <a href=" https://www.pecaagora.com/p/28400/valvula-reguladora-pressao-wabco-8-1-bar-l608-l708-mb-mercedes-benz-0014310006  "><img src="<?= Url::to('@assets/'); ?>img/banner-valvula.jpg"  class="img-ht img-fluid rounded acende img-responsive" style="width:100%; height: 100%" alt="imagem responsiva">
                    </div>
                </div>
                <div class="col-md-12 col-sm-6 co-xs-12 gal-item h-75">
                    <div class="box">
                        <a href="https://www.pecaagora.com/p/286318/kit-farol-lanterna-scania-serie-5-adaptao-serie-4-124-k-00019"><img src="<?= Url::to('@assets/'); ?>img/banner5.webp"   class="img-ht img-fluid rounded acende img-responsive" style="width:100%; height: 100%" alt="imagem responsiva">
                    </div>
                </div>

                <div class="col-md-12 col-sm-6 co-xs-12 gal-item h-75">
                    <div class="box">
                        <a href=" https://www.pecaagora.com/p/262014/ventilador-giratorio-12-volts-especial-grande-08-polegadas-vto-458909"><img src="<?= Url::to('@assets/'); ?>img/banner-ventilador.jpg" class="img-ht img-fluid rounded acende img-responsive" style="width:100%; height: 100%" alt="imagem responsiva"></a>
                    </div>
                </div>
            </div>

        </div>
        <br/>
    </div>
</div> <br><br>

<style>
    .gal-item{
        /*overflow: hidden;*/
        padding: 8px;
    }
    .gal-item .box{
        height: 100%;
        /*overflow: hidden;*/
    }
    .box img{
        width: 100%;
        height: 100%;
        -webkit-transition: -webkit-transform .5s ease;
        transition: transform .5s ease;

    }

    img.acende:hover {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }
</style>



<!--
<div class="container" style="background-color: white">
    <div class="container">
        <header class="text-center" style="background-color: #007475; margin-top: 20px">
            <h2 >Aproveite nossas Promoções</h2>
        </header>
        <div class="row">
            <div class="col-md-8 col-sm-12 co-xs-12 gal-item">
                <div class="row h-50">
                    <div class="col-md-12 col-sm-12 co-xs-12 gal-item">
                        <div class="box">
                            <img src="http://fakeimg.pl/708x370/" class="img-ht img-fluid rounded acende">
                        </div>
                    </div>
                </div>

                <div class="row h-50">
                    <div class="col-md-12 col-sm-12 co-xs-12 gal-item">
                        <div class="box">
                            <img src="http://fakeimg.pl/708x177/" class="img-ht img-fluid rounded acende">
                        </div>
                    </div>

                </div>
            </div>


            <div class="col-md-4 col-sm-6 co-xs-12 gal-item">
                <div class="col-md-12 col-sm-6 co-xs-12 gal-item h-25">
                    <div class="box">
                        <img src="http://fakeimg.pl/330x177/" class="img-ht img-fluid rounded acende">
                    </div>
                </div>

                <div class="col-md-12 col-sm-6 co-xs-12 gal-item h-75">
                    <div class="box">
                        <img src="http://fakeimg.pl/330x177/" class="img-ht img-fluid rounded acende">
                    </div>
                </div>
                <div class="col-md-12 col-sm-6 co-xs-12 gal-item h-75">
                    <div class="box">
                        <img src="http://fakeimg.pl/330x177/" class="img-ht img-fluid rounded acende">
                    </div>
                </div>
            </div>

        </div>
        <br/>
    </div>





</div> <br><br> -->
<!-- Carousel Imagens -->



<!--PRODUTOS -->

<div class="container ">
    <div class="site-index clearfix pull-left" >
        <div class="row margin-bottom-25">
            <!-- PROD EM OFERTA  -->
            <div class="h3 margin-bottom-15">Produtos em Destaque</div>
            <div id="myCarouselOferta" class="carousel slide" data-ride="carousel" >
                <ol class="carousel-indicators">
                    <li data-target="#myCarouselOferta" data-slide-to="0" class="active li2"></li>
                    <li data-target="#myCarouselOferta" data-slide-to="1" class="li2"></li>
                    <li data-target="#myCarouselOferta" data-slide-to="2" class="li2"></li>
                </ol>
                <div class="carousel-inner">

                    <?php
                    if (!YII_DEBUG) {
                        //$arrayid = [37768, 38614, 40156, 41523, 55917, 56129, 222241, 222292, 222293, 222494, 222496, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222528, 227171, 227171, 231348, 231514, 231596, 231618, 231646, 231647, 231648, 231649, 231651, 231664, 238131]; //producao
                        //$arrayid = [/*28943*/8673, 28942, /*229939*/251843, 56399, 38614, /*40156*/275078, 222292, 222293, 222248, 222241, 231596, 231618, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222496, 222528, 227171, 227171, 231646, 231647, /*231649*/249999, 231651, 231348, 231514, 41523, 56129, 222494, 231648, 231664, /*55917*/257602]; //Producao
                        $arrayid = [8673, 28942, 229939, 251843, 38614, 275078, 222292, 222293, 222248, 222241, 231596, 231618, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222496, 222528, 247475, 227171, 231646, 231647, 249999, 231651, 231348, 231514, 41523, 56129, 222494, 231648, 231664, 257602];
                    } else {
                        //$arrayid = [222248, 38614, 40156, 41523, 55917, 56129, 222241, 222292, 222293, 222494, 222496, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222528, 227171, 227171, 231348, 231514, 231596, 231618, 231646, 231647, 231648, 231649, 231651, 231664, 238131]; //local
                        //$arrayid = [/*28943*/8673, 28942, 229939, 56399, 38614, /*40156*/275078, 222292, 222293, 222248, 222241, 231596, 231618, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222496, 222528, 227171, 227171, 231646, 231647, 231649, 231651, 231348, 231514, 41523, 56129, 222494, 231648, 231664, 55917]; //local
                        $arrayid = [8673, 28942, 229939, 251843, 38614, 275078, 222292, 222293, 222248, 222241, 231596, 231618, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222496, 222528, 247475, 227171, 231646, 231647, 249999, 231651, 231348, 231514, 41523, 56129, 222494, 231648, 231664, 257602];
                    }

                    for($x = 0 ; $x<=2 ; $x++){
                        ?>

                        <div class="item <?= (($x==0)? "active" : "")?>">
                            <div class="container ">
                                <div class="site-index clearfix pull-left">
                                    <div class="row margin-bottom-25">

                                        <!-- PROD. EM DESTAQUE -->
                                        <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 clearfix"><!-- Em destaque -->
                                            <!--<div class="h3 margin-bottom-15">Produtos em Oferta</div>-->

                                            <?php

                                            //$produtos = Produto::find()->andWhere(['id' => $arrayid])->orderBy('id')->all();
                                            for ($i = ($x*4); $i <= ($x*4+3); $i++) {
                                                //$produto = ArrayHelper::getValue($produtos, $i);
                                                $produto = Produto::find()->andWhere(['=','id',ArrayHelper::getValue($arrayid, $i)])->one();
                                                ?>
                                                <div class="produto-div clearfix col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                    <div itemscope itemtype="http://schema.org/Product" class='panel panel-body produto-search col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                                                        <div class="row clearfix rowHomePage">
                                                            <div class="produto-search-img text-center margin-bottom-10">
                                                                <span class="label label-warning promocao"><?=(($produto->id)%2==1)? "-18%": "-11%"?></span>
                                                                <a href="<?= $produto->getUrl() ?>">
                                                                    <?php
                                                                    $alt = $produto->getLabel();
                                                                    echo $produto->getImage(['class' => "text-center","height" => "auto" , 'width' => '156', 'alt' => $alt, 'title' => $alt, 'itemprop' => 'image']);
                                                                    //$maxValue = ValorProdutoFilial::find()->ativo()->maiorValorProduto($produto->id)->one();
                                                                    //$minValue = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
                                                                    $minValue = ValorProdutoMenorMaior::findOne(['produto_id'=>$produto->id]);//->menor_valor;
                                                                    ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="row text-center">
                                                            <span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star <?= (($produto->id)%5!=1) ? "checked" : ""?>"></span>
                                                        </div>
                                                        <div class="row">
                                                            <div itemprop="name" class="produto-search-title clearfix  title col-xs-12 col-sm-12 col-md-12 col-lg-12 toggle textoPaginaInicial" maxlenght="10">
                                                                <a href="<?= $produto->getUrl() ?>">
                                                                    <span><?= $produto->getLabel() ?></span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="produto-search-details-wrap clearfix preco-busca">
                                                                <div class="produto-search-details text-center col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                                 <span itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer" class="price lead">
                                                                 <!-- <small><small>a partir de</small></small><br/>-->
                                                                 <small><small>
                                                                 		<del><?= (($produto->id)%2==1) ? number_format(($minValue->getValorFinalMenor()/0.82), 2, ',', '') : number_format(($minValue->getValorFinalMenor()/0.89), 2, ',', '')?>
                                                                 		</del>
																 </small></small><br/>
                                                                 <span itemprop="lowPrice"><?= $minValue->labelTituloMenor() ?></span>
                                                                 <br/>
                                                                 <meta itemprop="priceCurrency" content="BRL"/>
                                                                 </span>
                                                                    <br>
                                                                    <div class="produto-search-button text-center col-xs-12 col-sm-12 col-md-12 col-lg-12 clearfix">
                                                                        <a href="<?= $produto->getUrl() ?>" class="btn btn-danger hide"> <!-- btn-primary -->
                                                                            <i class="no-color fa fa-shopping-cart "></i>
                                                                            Comprar
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <!-- PROD. EM OFERTA -->
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php }?>

                </div>
                <!-- Left and right controls -->
                <a class="left carousel-control" href="#myCarouselOferta" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" style="color:#007576 "></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right  carousel-control" href="#myCarouselOferta" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" style="color:#007576 "></span>
                    <span class="sr-only">Next</span>
                </a>

            </div>
            <!-- PROD. EM OFERTA-->
        </div>


        <div class="container center-block">
            <div class="h3 margin-bottom-15 hidden-xs"><img src="<?= Url::to('@assets/'); ?>img/relogio.gif" style="width: 32px; height: 32px; " border="0" alt="relogio-imagem-animada-0148" >&nbsp;&nbsp;Ofertas do dia <span id="demo"></span></div>
            <div class="row">
                <?php
                for($x = 0 ; $x<=2 ; $x++){
                    ?>
                    <div class="col-md-4 col-sm-6">
                        <?php
                        //$produtos = Produto::find()->andWhere(['id' => $arrayid])->orderBy('id')->all();
                        for ($i = ($x*4)+12; $i <= ($x*4+15); $i++) {
                            //$produto = ArrayHelper::getValue($produtos, $i);
                            $produto = Produto::find()->andWhere(['=','id',ArrayHelper::getValue($arrayid, $i)])->one();
                            ?>
                            <div class="product-grid">
                                <div class="product-image">
                                    <a href="<?= $produto->getUrl() ?>">
                                        <?php
                                        $alt = $produto->getLabel();
                                        echo $produto->getImage(['class' => "text-center", 'width' => '156', 'alt' => $alt, 'title' => $alt, 'itemprop' => 'image']);
                                        //$maxValue = ValorProdutoFilial::find()->ativo()->maiorValorProduto($produto->id)->one();
                                        //$minValue = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
                                        $minValue = ValorProdutoMenorMaior::findOne(['produto_id'=>$produto->id]);//->menor_valor;
                                        ?>
                                    </a>
                                    <ul class="social">
                                        <li><a href="<?= $produto->getUrl() ?>"  data-tip="Olhada rápida"><i class="fa fa-search"></i></a></li>
                                        <li><a href="<?= $produto->getUrl() ?>"  data-tip="Adicionar a lista de desejos"><i class="fa fa-shopping-bag"></i></a></li>
                                        <li><a href="<?= $produto->getUrl() ?>"  data-tip="Adicionar no carrinho"><i class="fa fa-shopping-cart"></i></a></li>
                                    </ul>
                                    <span class="product-new-label">Oferta</span>
                                    <span class="product-discount-label"><?=(($produto->id)%2==1)? "-18%": "-11%"?></span>
                                </div>
                                <div class="row text-center">
                                    <span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star <?= (($produto->id)%5!=1) ? "checked" : ""?>"></span>
                                </div>
                                <div class="product-content">
                                    <h3 class="title">
                                        <small><small>
                                                <del><?= (($produto->id)%2==1) ? number_format(($minValue->getValorFinalMenor()/0.82), 2, ',', '') : number_format(($minValue->getValorFinalMenor()/0.89), 2, ',', '')?>
                                                </del>
                                            </small></small><br/>
                                        <span itemprop="lowPrice" style="font-size: 25px"><?= $minValue->labelTituloMenor() ?></span></h3>

                                    <div class="price"> </span>

                                    </div>

                                    <a class="add-to-cart" href="<?= $produto->getUrl() ?>" class="btn btn-danger hide"> <!-- btn-primary -->
                                        <i class="no-color fa fa-shopping-cart "></i>
                                        Comprar
                                    </a>

                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php }?>
            </div>
        </div><br><br>


        <!-- Em destaque  + depoimento-->
        <div class="row col-xs-12  col-sm-12 col-md-12 col-lg-12 clearfix" style="padding-right: 0;background-color: #ffffff; border-radius: 8px">
            <?= $this->render('createNewsletter', ['model' => new Newsletter()]); ?>
        </div>        <!-- Banner -->
        <br> <br>

        <!-- MAIS VENDIDOS  -->
        <div class="row margin-bottom-25">
            <div class="h3 margin-bottom-15">Produtos mais Vendidos</div>
            <div id="myCarousel" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#myCarouselOferta" data-slide-to="0" class="active li2"></li>
                    <li data-target="#myCarouselOferta" data-slide-to="1" class="li2"></li>
                </ol>
                <div class="carousel-inner">

                    <?php
                    for($x = 0 ; $x<=1 ; $x++){
                        ?>

                        <div class="item <?= (($x==0)? "active" : "")?>">
                            <div class="container ">
                                <div class="site-index clearfix pull-left">
                                    <div class="row margin-bottom-25">
                                        <!-- Em destaque  -->
                                        <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 clearfix"><!-- Em destaque -->
                                            <!--<div class="h3 margin-bottom-15">Produtos mais Vendidos</div>-->
                                            <?php
                                            //$produtos = Produto::find()->andWhere(['id' => $arrayid])->orderBy('id')->all();
                                            for ($i = ($x*4)+24; $i <= ($x*4+27); $i++) {
                                                //$produto = ArrayHelper::getValue($produtos, $i);
                                                $produto = Produto::find()->andWhere(['=','id',ArrayHelper::getValue($arrayid, $i)])->one();
                                                ?>
                                                <div class="produto-div clearfix col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                    <div itemscope itemtype="http://schema.org/Product" class='panel panel-body produto-search col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                                                        <div class="row rowHomePage">
                                                            <div class="produto-search-img text-center margin-bottom-10 ">
                                                                <a href="<?= $produto->getUrl() ?>">
                                                                    <?php
                                                                    $alt = $produto->getLabel();
                                                                    echo $produto->getImage(['class' => "text-center", 'width' => '156', 'alt' => $alt, 'title' => $alt, 'itemprop' => 'image']);
                                                                    //$maxValue = ValorProdutoFilial::find()->ativo()->maiorValorProduto($produto->id)->one();
                                                                    //$minValue = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
                                                                    $minValue = ValorProdutoMenorMaior::findOne(['produto_id'=>$produto->id]);//->menor_valor;
                                                                    ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div itemprop="name" class="produto-search-title clearfix  title col-xs-12 col-sm-12 col-md-12 col-lg-12 toggle textoPaginaInicial" maxlenght="10">
                                                                <a href="<?= $produto->getUrl() ?>">
                                                                    <span><?= $produto->getLabel() ?></span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="row text-center">
                                                            <span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star <?= (($produto->id)%5!=1) ? "checked" : ""?>"></span>
                                                        </div>
                                                        <div class="row">
                                                            <div class="produto-search-details-wrap clearfix preco-busca">
                                                                <div class="produto-search-details text-center col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                             <span itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer" class="price lead">
                                                             <span itemprop="lowPrice"><?= $minValue->labelTituloMenor() ?></span>
                                                             <br/>
                                                             <meta itemprop="priceCurrency" content="BRL"/>
                                                             </span>
                                                                    <br>
                                                                    <div class="produto-search-button text-center col-xs-12 col-sm-12 col-md-12 col-lg-12 clearfix">
                                                                        <a href="<?= $produto->getUrl() ?>" class="btn btn btn-danger hide"> <!-- btn-primary -->
                                                                            <i class="no-color fa fa-shopping-cart "></i>
                                                                            Comprar
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <!-- Em destaque -->
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php }?>

                </div>
                <!-- Left and right controls -->
                <a class="left  carousel-control" href="#myCarousel" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" style="color:#007576 "></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right  carousel-control" href="#myCarousel" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" style="color:#007576 "></span>
                    <span class="sr-only">Next</span>
                </a>

            </div>
        </div>
        <!-- Em destaque -->



        <hr>

    </div>







    <style>
        h3.h3{text-align:center;margin:1em;text-transform:capitalize;font-size:1.7em;}

        /********************* shopping Demo-1 **********************/
        .product-grid{font-family:Raleway,sans-serif;text-align:center;padding:0 0 72px;border:1px solid rgba(0,0,0,.1);overflow:hidden;position:relative;z-index:1}
        .product-grid .product-image{position:relative;transition:all .3s ease 0s}
        .product-grid .product-image a{display:block}
        .product-grid .product-image img{width:100%;height:auto}
        .product-grid .pic-1{opacity:1;transition:all .3s ease-out 0s}
        .product-grid:hover .pic-1{opacity:1}
        .product-grid .pic-2{opacity:0;position:absolute;top:0;left:0;transition:all .3s ease-out 0s}
        .product-grid:hover .pic-2{opacity:1}
        .product-grid .social{width:150px;padding:0;margin:0;list-style:none;opacity:0;transform:translateY(-50%) translateX(-50%);position:absolute;top:60%;left:50%;z-index:1;transition:all .3s ease 0s}
        .product-grid:hover .social{opacity:1;top:50%}
        .product-grid .social li{display:inline-block}
        .product-grid .social li a{color:#fff;background-color:#333;font-size:16px;line-height:40px;text-align:center;height:40px;width:40px;margin:0 2px;display:block;position:relative;transition:all .3s ease-in-out}
        .product-grid .social li a:hover{color:#fff;background-color: #007475}
        .product-grid .social li a:after,.product-grid .social li a:before{content:attr(data-tip);color:#fff;background-color:#000;font-size:12px;letter-spacing:1px;line-height:20px;padding:1px 5px;white-space:nowrap;opacity:0;transform:translateX(-50%);position:absolute;left:50%;top:-30px}
        .product-grid .social li a:after{content:'';height:15px;width:15px;border-radius:0;transform:translateX(-50%) rotate(45deg);top:-20px;z-index:-1}
        .product-grid .social li a:hover:after,.product-grid .social li a:hover:before{opacity:1}
        .product-grid .product-discount-label,.product-grid .product-new-label{color:#fff;background-color: #007475;font-size:12px;text-transform:uppercase;padding:2px 7px;display:block;position:absolute;top:10px;left:0}
        .product-grid .product-discount-label{background-color:#f0ad4e;left:auto;right:0}
        .product-grid .rating{color:#FFD200;font-size:12px;padding:12px 0 0;margin:0;list-style:none;position:relative;z-index:-1}
        .product-grid .rating li.disable{color:rgba(0,0,0,.2)}
        .product-grid .product-content{background-color:#fff;text-align:center;padding:10px 0;margin:0 auto;position:absolute;left:0;right:0;bottom:-27px;z-index:1;transition:all .3s}
        .product-grid:hover .product-content{bottom:0}
        .product-grid .title{font-size:13px;font-weight:400;letter-spacing:.5px;text-transform:capitalize;margin:0 0 10px;transition:all .3s ease 0s}
        .product-grid .title a{color:#828282}
        .product-grid .title a:hover,.product-grid:hover .title a{color: #007475}
        .product-grid .price{color:#333;font-size:17px;font-family:Montserrat,sans-serif;font-weight:700;letter-spacing:.6px;margin-bottom:8px;text-align:center;transition:all .3s}
        .product-grid .price span{color:#999;font-size:13px;font-weight:400;text-decoration:line-through;margin-left:3px;display:inline-block}
        .product-grid .add-to-cart{color: #b10c10;font-size:17px;font-weight:600}
        @media only screen and (max-width:990px){.product-grid{margin-bottom:30px}
        }

    </style>


    <!-- CRONOMETRO -->
    <script>

        var countDownDate = new Date("November 26, 2019 06:00:15");
        var countDownDateTime = countDownDate.getTime();

        var x = setInterval(function() {

            var now = new Date().getTime();


            var distance = countDownDateTime - now;


            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            var milliseconds = Math.floor((distance % 1000 ) / 1);


            document.getElementById("demo").innerHTML =  days +  " : " + hours + " : "
                + minutes + " : " + seconds + " . " + milliseconds + "";


            if (distance < 0) {
                countDownDate.setHours(countDownDate.getHours()+20);
                countDownDateTime = countDownDate.getTime();
                document.getElementById("demo").innerHTML = "";
            }


        });





        // If the count down is over, write some text

        /*if (distance < 0) {
            clearInterval(x);
            document.getElementById("demo").innerHTML = "EXPIRED";
        }*/


        //if(horas == 0 && minutos == 0 && segundos == 0) {
        //data = data + 0;
        // }


    </script>
    <style>
        @font-face {
            font-family: "ubuntu";
            font-style: italic;
            font-weight: 300;
            src: local("Lato Light Italic"), local("Lato-LightItalic"), url(https://fonts.gstatic.com/s/ubuntucondensed/v8/u-4k0rCzjgs5J7oXnJcM_0kACGMtT-Dfqw.woff2) format("woff2");
        }
        a {
            text-decoration: none;
            color: #9ca0b1;
        }

        .wrapper {
            text-align: center;
        }
        .wrapper h4 {
            color: #fff;
            font-size: 15px;
            font-family: "ubuntu";
            text-transform: uppercase;
            font-weight: 700;
            font-family: "Josefin Sans", sans-serif;
            background: linear-gradient(to left, #ffffff 50%, #ffffff 50%);
            background-size: auto auto;
            background-clip: border-box;
            background-size: 200% auto;
            color: #fff;
            text-fill-color: transparent;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: textclip 5.5s linear infinite;
            display: inline-block;
        }

        @keyframes textclip {
            to {
                background-position: 200% center;
            }
        }

        #demo{
            color: #4A4E5A; !important;
            font-size: 23px!important;
        }

    </style>









</div>




<!-- carousel  NOVO-->

<style type="text/css">



    .carousel .carousel-control {
        height: 44px;
        width: 40px;
        background: rgba(0, 116, 117, 0);
        margin: auto 0;
        border-radius: 4px;
        opacity: 0.8;
    }
    .carousel .carousel-control:hover {
        background: rgba(0, 116, 117, 0);
        opacity: 1;
    }
    .carousel .carousel-control i {
        font-size: 36px;
        position: absolute;
        top: 50%;
        display: inline-block;
        margin: -19px 0 0 0;
        z-index: 5;
        left: 0;
        right: 0;
        color: #007576;
        text-shadow: none;
        font-weight: bold;
    }
    .carousel .item-price {
        font-size: 13px;
        padding: 2px 0;
    }
    .carousel .item-price strike {
        opacity: 0.7;
        margin-right: 5px;
    }
    .carousel .carousel-control.left i {
        margin-left: -2px;
    }
    .carousel .carousel-control.right i {
        margin-right: -4px;
    }

</style>
<script type="text/javascript">
    $(document).ready(function(){
        $(".wish-icon i").click(function(){
            $(this).toggleClass("fa-heart fa-heart-o");
        });
    });
</script>




<!--

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Teste <b>Produtos</b></h2>
            <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="0">

                <ol class="carousel-indicators">
                    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#myCarousel" data-slide-to="1"></li>
                    <li data-target="#myCarousel" data-slide-to="2"></li>
                </ol>

                <div class="carousel-inner">

                    <div class="item carousel-item active">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="thumb-wrapper">
                                    <span class="wish-icon"><i class="fa fa-heart-o"></i></span>
                                    <div class="img-box">
                                        <img src="/examples/images/products/ipad.jpg" class="img-responsive img-fluid" alt="">
                                    </div>
                                    <div class="thumb-content">
                                        <h4>Apple iPad</h4>
                                        <div class="star-rating">
                                            <ul class="list-inline">
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star-o"></i></li>
                                            </ul>
                                        </div>
                                        <p class="item-price"><strike>$400.00</strike> <b>$369.00</b></p>
                                        <a href="#" class="btn btn-primary">Add No carrinho </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="thumb-wrapper">
                                    <span class="wish-icon"><i class="fa fa-heart-o"></i></span>
                                    <div class="img-box">
                                        <img src="/examples/images/products/headphone.jpg" class="img-responsive img-fluid" alt="">
                                    </div>
                                    <div class="thumb-content">
                                        <h4>Sony Headphone</h4>
                                        <div class="star-rating">
                                            <ul class="list-inline">
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star-o"></i></li>
                                            </ul>
                                        </div>
                                        <p class="item-price"><strike>$25.00</strike> <b>$23.99</b></p>
                                        <a href="#" class="btn btn-primary">Add No carrinho </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="thumb-wrapper">
                                    <span class="wish-icon"><i class="fa fa-heart-o"></i></span>
                                    <div class="img-box">
                                        <img src="/examples/images/products/macbook-air.jpg" class="img-responsive img-fluid" alt="">
                                    </div>
                                    <div class="thumb-content">
                                        <h4>Macbook Air</h4>
                                        <div class="star-rating">
                                            <ul class="list-inline">
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star-half-o"></i></li>
                                            </ul>
                                        </div>
                                        <p class="item-price"><strike>$899.00</strike> <b>$649.00</b></p>
                                        <a href="#" class="btn btn-primary">Add No carrinho </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item carousel-item">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="thumb-wrapper">
                                    <span class="wish-icon"><i class="fa fa-heart-o"></i></span>
                                    <div class="img-box">
                                        <img src="/examples/images/products/play-station.jpg" class="img-responsive img-fluid" alt="">
                                    </div>
                                    <div class="thumb-content">
                                        <h4>Sony Play Station</h4>
                                        <p class="item-price"><strike>$289.00</strike> <span>$269.00</span></p>
                                        <div class="star-rating">
                                            <ul class="list-inline">
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star-o"></i></li>
                                            </ul>
                                        </div>
                                        <a href="#" class="btn btn-primary">Add No carrinho </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="thumb-wrapper">
                                    <span class="wish-icon"><i class="fa fa-heart-o"></i></span>
                                    <div class="img-box">
                                        <img src="/examples/images/products/macbook-pro.jpg" class="img-responsive img-fluid" alt="">
                                    </div>
                                    <div class="thumb-content">
                                        <h4>Macbook Pro</h4>
                                        <p class="item-price"><strike>$1099.00</strike> <span>$869.00</span></p>
                                        <div class="star-rating">
                                            <ul class="list-inline">
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star-half-o"></i></li>
                                            </ul>
                                        </div>
                                        <a href="#" class="btn btn-primary">Add No carrinho </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="thumb-wrapper">
                                    <span class="wish-icon"><i class="fa fa-heart-o"></i></span>
                                    <div class="img-box">
                                        <img src="/examples/images/products/speaker.jpg" class="img-responsive img-fluid" alt="">
                                    </div>
                                    <div class="thumb-content">
                                        <h4>Bose Speaker</h4>
                                        <p class="item-price"><strike>$109.00</strike> <span>$99.00</span></p>
                                        <div class="star-rating">
                                            <ul class="list-inline">
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star-o"></i></li>
                                            </ul>
                                        </div>
                                        <a href="#" class="btn btn-primary">Add No carrinho </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="item carousel-item">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="thumb-wrapper">
                                    <span class="wish-icon"><i class="fa fa-heart-o"></i></span>
                                    <div class="img-box">
                                        <img src="/examples/images/products/iphone.jpg" class="img-responsive img-fluid" alt="">
                                    </div>
                                    <div class="thumb-content">
                                        <h4>Apple iPhone</h4>
                                        <p class="item-price"><strike>$369.00</strike> <span>$349.00</span></p>
                                        <div class="star-rating">
                                            <ul class="list-inline">
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star-o"></i></li>
                                            </ul>
                                        </div>
                                        <a href="#" class="btn btn-primary">Add No carrinho </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="thumb-wrapper">
                                    <span class="wish-icon"><i class="fa fa-heart-o"></i></span>
                                    <div class="img-box">
                                        <img src="/examples/images/products/canon.jpg" class="img-responsive img-fluid" alt="">
                                    </div>
                                    <div class="thumb-content">
                                        <h4>Canon DSLR</h4>
                                        <p class="item-price"><strike>$315.00</strike> <span>$250.00</span></p>
                                        <div class="star-rating">
                                            <ul class="list-inline">
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star-o"></i></li>
                                            </ul>
                                        </div>
                                        <a href="#" class="btn btn-primary">Add No carrinho </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="thumb-wrapper">
                                    <span class="wish-icon"><i class="fa fa-heart-o"></i></span>
                                    <div class="img-box">
                                        <img src="/examples/images/products/pixel.jpg" class="img-responsive img-fluid" alt="">
                                    </div>
                                    <div class="thumb-content">
                                        <h4>Google Pixel</h4>
                                        <p class="item-price"><strike>$450.00</strike> <span>$418.00</span></p>
                                        <div class="star-rating">
                                            <ul class="list-inline">
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                <li class="list-inline-item"><i class="fa fa-star-half-o"></i></li>
                                            </ul>
                                        </div>
                                        <a href="#" class="btn btn-primary">Add No carrinho </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>



                <a class="carousel-control left carousel-control-prev" href="#myCarousel" data-slide="prev">
                    <i class="fa fa-angle-left"></i>
                </a>
                <a class="carousel-control right carousel-control-next" href="#myCarousel" data-slide="next">
                    <i class="fa fa-angle-right"></i>
                </a>
            </div>
        </div>
    </div>
</div> -->