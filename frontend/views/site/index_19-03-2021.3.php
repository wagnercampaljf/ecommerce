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
$this->title = '';
//echo Yii::$app->security->generatePasswordHash('1q2w3e');die;
$this->registerLinkTag(['rel' => 'canonical', 'href' => 'https://www.pecaagora.com']);
$this->registerMetaTag(['name' => 'description', 'content' => 'Distribuidora de Autopeças Diesel. Peças para caminhões, carretas, ônibus, máquinas. Mercedes Benz, Scania, Volkswagen, Ford']);
?>

<h1 class="span-logo"></h1>
<h2 class="span-logo">O shopping online de autopeças seu carro, moto e caminhao.</h2>
</div>

<div class="visible-xs"><br></div>


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
                            <div id="myCarousel" class="carousel slide " data-ride="carousel">

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
                                        <a href="https://www.pecaagora.com/p/257500/forro-teto-caminhao-mb-1113-1513-1114-1518-2013-cabine-alta-com-bolsa-preto-pvc-087020"><img src="<?= Url::to('@assets/'); ?>img/3B-708x370.jpg" width="100%" height="100%"></a>
                                    </div>

                                    <div class="item">
                                        <a href="https://www.pecaagora.com/p/371684/lente-lanterna-traseira-mb-1114-1614-1618-1620-mercedes-benz-0025441890"><img src="<?= Url::to('@assets/'); ?>img/4B-708x370.jpg" width="100%" height="100%"></a>
                                    </div>

                                    <div class="item">
                                        <a href="https://www.pecaagora.com/p/369448/par-forro-porta-cinza-vw-690s-790s-7110s-11130-13130-14140-ate-2000-p-tf3867016-tf3867015">  <img src="<?= Url::to('@assets/'); ?>img/1B.jpg" width="100%" height="100%"></a>
                                    </div>
                                    <div class="item">
                                        <a href="https://www.pecaagora.com/p/369806/tapete-mb-709-710-912-914-borracha-texturizado-com-nome-com-logo-mercedes-benz-138115"><img src="<?= Url::to('@assets/'); ?>img/2B.jpg" width="100%" height="100%"></a>
                                    </div>
                                    <div class="item">
                                        <a href="#"><img src="<?= Url::to('@assets/'); ?>img/img_promo.jpg" width="100%" height="100%"></a>
                                    </div>
                                    <div class="item">
                                        <a href="#"><img src="<?= Url::to('@assets/'); ?>img/img_promo1.jpg" width="100%" height="100%"></a>
                                    </div>
                                </div>

                                <a class="left carousel-control " style="width: 3%" href="#myCarousel" data-slide="prev">
                                    <span class="glyphicon glyphicon-chevron-left"></span>

                                </a>
                                <a class="right carousel-control" style="width: 4%" href="#myCarousel" data-slide="next">
                                    <span class="glyphicon glyphicon-chevron-right"></span>

                                </a>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row h-50">
                    <div class="col-md-12 col-sm-12 co-xs-12 gal-item">
                        <div class="box">

                            <!-- banner 150mil produtos -->
                            <img src="<?= Url::to('@assets/'); ?>img/1C.jpg"  class="img-ht img-fluid rounded acende img-responsive" style="width:100%; height: 100%" alt="imagem responsiva">
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-4 col-sm-6 co-xs-12 gal-item">
                <div class="col-md-12 col-sm-6 co-xs-12 gal-item h-25">
                    <div class="box">
                        <a href="https://www.pecaagora.com/p/222241/disco-tacografo-caminhes-nibus-vans-semanal-125-km-20x20-dml001"><img src="<?= Url::to('@assets/'); ?>img/3A-330x178_08.jpg"  class="img-ht img-fluid rounded acende img-responsive" style="width:100%; height: 100%" alt="imagem responsiva">
                    </div>
                </div>
                <div class="col-md-12 col-sm-6 co-xs-12 gal-item h-75">
                    <div class="box">
                        <a href="https://www.pecaagora.com/p/370537/kit-estribo-l-e-c-pisantes-mb-axor-2035-2040-2044-2540-2544-2644-k-00090"><img src="<?= Url::to('@assets/'); ?>img/2A-330x178.jpg"   class="img-ht img-fluid rounded acende img-responsive" style="width:100%; height: 100%" alt="imagem responsiva">
                    </div>
                </div>

                <div class="col-md-12 col-sm-6 co-xs-12 gal-item h-75">
                    <div class="box">
                        <a href="https://www.pecaagora.com/p/247982/paralama-traseiro-dir-mb-axor-1933-2533-2831-3131-fibra-direito-grafite-9408802906"><img src="<?= Url::to('@assets/'); ?>img/1A-330x178.jpg" class="img-ht img-fluid rounded acende img-responsive" style="width:100%; height: 100%" alt="imagem responsiva"></a>
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


<!--PRODUTOS -->
<div class="container ">
    <div class="site-index clearfix pull-left" >
        <div class="row margin-bottom-25">
            <!-- PROD EM OFERTA  -->
            <div class="h3 margin-bottom-15">Produtos em Oferta</div>
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
                        $arrayid = [
                            230588,
                            230608,
                            230615,
                            230629,
                            230987,
                            230639,
                            230162,
                            230093,
                            228839,
                            230718,
                            230724,
                            230732,
                            230763,
                            230217,
                            230769,
                            230220,
                            230777,
                            230860,
                            227415,
                            230830,
                            230833,
                            230837,
                            230835,
                            228785,
                            230215,
                            230914,
                            227517,
                            230934,
                            229463,
                            230978,
                            227998,
                            231008,
                            231012,
                            231017,
                            228034,
                            227982,
                            228014,
                            227696,
                            230218,
                            231053,
                        ];
                    } else {
                        //$arrayid = [222248, 38614, 40156, 41523, 55917, 56129, 222241, 222292, 222293, 222494, 222496, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222528, 227171, 227171, 231348, 231514, 231596, 231618, 231646, 231647, 231648, 231649, 231651, 231664, 238131]; //local
                        //$arrayid = [/*28943*/8673, 28942, 229939, 56399, 38614, /*40156*/275078, 222292, 222293, 222248, 222241, 231596, 231618, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222496, 222528, 227171, 227171, 231646, 231647, 231649, 231651, 231348, 231514, 41523, 56129, 222494, 231648, 231664, 55917]; //local
                        $arrayid = [
                            230588,
                            230608,
                            230615,
                            230629,
                            230987,
                            230639,
                            230162,
                            230093,
                            228839,
                            230718,
                            230724,
                            230732,
                            230763,
                            230217,
                            230769,
                            230220,
                            230777,
                            230860,
                            227415,
                            230830,
                            230833,
                            230837,
                            230835,
                            228785,
                            230215,
                            230914,
                            227517,
                            230934,
                            229463,
                            230978,
                            227998,
                            231008,
                            231012,
                            231017,
                            228034,
                            227982,
                            228014,
                            227696,
                            230218,
                            231053,
                        ];
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
                                                                <span class="label label-warning promocao"><?= (($produto->id)%2==1)? "-18%": "-11%"?></span>
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
        <!-- Em destaque  + depoimento-->
        <div class="row col-xs-12  col-sm-12 col-md-12 col-lg-12 clearfix" style="padding-right: 0;background-color: #ffffff; border-radius: 8px">
            <?= $this->render('createNewsletter', ['model' => new Newsletter()]); ?>
        </div>        <!-- Banner -->
        <br> <br>

        <!-- PROD. EM DESTAQUE -->
        <div class="row margin-bottom-25">
            <div class="h3 margin-bottom-15">Produtos em Destaque</div>
            <div id="myCarouselDestaque" class="carousel slide" data-ride="carousel">

                <div class="carousel-inner">
                    <ol class="carousel-indicators">
                        <li data-target="#myCarouselOferta" data-slide-to="0" class="active li2"></li>
                        <li data-target="#myCarouselOferta" data-slide-to="1" class="li2"></li>
                        <li data-target="#myCarouselOferta" data-slide-to="2" class="li2"></li>
                    </ol>
                    <?php
                    for($x = 0 ; $x<=2 ; $x++){
                        ?>

                        <div class="item <?= (($x==0)? "active" : "")?>">
                            <div class="container ">
                                <div class="site-index clearfix pull-left">
                                    <div class="row margin-bottom-25">
                                        <!-- PROD. EM DESTAQUE   -->

                                        <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 clearfix"><!-- Em destaque -->
                                            <!-- <div class="h3 margin-bottom-15">Produtos em Destaque</div> -->
                                            <?php
                                            //$produtos = Produto::find()->andWhere(['id' => $arrayid])->orderBy('id')->all();
                                            for ($i = ($x*4)+12; $i <= ($x*4+15); $i++) {
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
                                        <!-- PROD. EM DESTAQUE -->
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php }?>

                </div>
                <!-- Left and right controls -->
                <a class="left  carousel-control" href="#myCarouselDestaque" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" style="color:#007576 "></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#myCarouselDestaque" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" style="color:#007576 "></span>
                    <span class="sr-only">Next</span>
                </a>

            </div>
        </div>
        <!-- PROD. EM DESTAQUE-->





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

    </div>
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











