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
$this->title = 'O Shopping Online do seu Veículo';
//echo Yii::$app->security->generatePasswordHash('1q2w3e');die;
$this->registerLinkTag(['rel' => 'canonical', 'href' => 'https://www.pecaagora.com']);
$this->registerMetaTag(['name' => 'description', 'content' => 'O melhor preço em autopeças para carros, motos, caminhões, ônibus e tratores. Confira nossas ofertas e compre online! Entregamos em todo o Brasil!']);
?>

<h1 class="span-logo">Peça Agora</h1>
<h2 class="span-logo">O shopping online de autopeças seu carro, moto e caminhao.</h2>
</div>

<div class="visible-xs"><br><br></div>

<!-- Carousel Imagens -->
<div id="myCarousel" class="carousel slide hidden-xs" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
        <li data-target="#myCarousel" data-slide-to="3"></li>
        <li data-target="#myCarousel" data-slide-to="4"></li>
        <li data-target="#myCarousel" data-slide-to="5"></li>

    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner ">
        <div class="item active">
            <a href="https://www.pecaagora.com/p/8673/cuica-freio-spring-brake-30-x-30-haste-longa-carretas-trucks-randon-guerra-ford-vw-mb-iveco-2rr607947"><img src="<?= Url::to('@assets/'); ?>img/0.webp" width="100%" height="100%"></a>
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

        <div class="item">
            <img src="<?= Url::to('@assets/'); ?>img/6.webp" width="100%" height="100%">
        </div>
    </div>

    <!-- Left and right controls -->
    <a class="left carousel-control " style="width: 3%" href="#myCarousel" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left"></span>

    </a>
    <a class="right carousel-control" style="width: 4%" href="#myCarousel" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right"></span>

    </a>
</div><br>
<!-- Carousel Imagens -->





<div class="container ">
    <div class="site-index clearfix pull-left">
        <div class="row margin-bottom-25">
            <!-- Em oferta  -->
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
				    $arrayid = [8673, 28942, 229939, 251843, 38614, 275078, 222292, 222293, 222248, 222241, 231596, 231618, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222496, 222528, 247475, 227171, 231646, 231647, 249999, 231651, 231348, 231514, 41523, 56129, 222494, 231648, 231664, 257602];
        			} else {
        			    //$arrayid = [222248, 38614, 40156, 41523, 55917, 56129, 222241, 222292, 222293, 222494, 222496, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222528, 227171, 227171, 231348, 231514, 231596, 231618, 231646, 231647, 231648, 231649, 231651, 231664, 238131]; //local
				    //$arrayid = [/*28943*/8673, 28942, 229939, 56399, 38614, /*40156*/275078, 222292, 222293, 222248, 222241, 231596, 231618, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222496, 222528, 227171, 227171, 231646, 231647, 231649, 231651, 231348, 231514, 41523, 56129, 222494, 231648, 231664, 55917]; //local
				    $arrayid = [8673, 28942, 229939, 251843, 38614, 275078, 222292, 222293, 222248, 222241, 231596, 231618, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523,222496, 222528, 247475, 227171, 231646, 231647, 249999, 231651, 231348, 231514, 41523, 56129, 222494, 231648, 231664, 257602];
        			}
        			
        			for($x = 0 ; $x<=2 ; $x++){
    			?>
                
                <div class="item <?= (($x==0)? "active" : "")?>">
                    <div class="container ">
                        <div class="site-index clearfix pull-left">
                            <div class="row margin-bottom-25">
                           
                            <!-- Em destaque  -->
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
                                <!-- Em oferta -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php }?>
                
            </div>
            <!-- Left and right controls -->
            <a class="left carouselPaginaPrincipal carousel-control" href="#myCarouselOferta" data-slide="prev">
            	<span class="glyphicon glyphicon-chevron-left"></span>
            	<span class="sr-only">Previous</span>
            </a>
            <a class="right carouselPaginaPrincipal carousel-control" href="#myCarouselOferta" data-slide="next">
            	<span class="glyphicon glyphicon-chevron-right"></span>
            	<span class="sr-only">Next</span>
            </a>
                       
		</div>
        <!-- Em oferta -->
        
        

        </div>
        <!-- Em destaque  + depoimento-->
        <div class="row col-xs-12  col-sm-12 col-md-12 col-lg-12 clearfix" style="padding-right: 0; background-color: white; border-radius: 20px">
            <?= $this->render('createNewsletter', ['model' => new Newsletter()]); ?>
        </div>        <!-- Banner -->
        <br> <br>

        <!-- Em destaque -->
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
                                <!-- Em destaque  -->
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
                                    <!-- Em destaque -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php }?>
                    
                </div>
                <!-- Left and right controls -->
                <a class="left carouselPaginaPrincipal carousel-control" href="#myCarouselDestaque" data-slide="prev">
                	<span class="glyphicon glyphicon-chevron-left"></span>
                	<span class="sr-only">Previous</span>
                </a>
                <a class="right carouselPaginaPrincipal carousel-control" href="#myCarouselDestaque" data-slide="next">
                	<span class="glyphicon glyphicon-chevron-right"></span>
                	<span class="sr-only">Next</span>
                </a>
                           
    		</div>
    	</div>
        <!-- Em destaque -->
        
        <!-- Mais Vendidos -->
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
                <a class="left carouselPaginaPrincipal carousel-control" href="#myCarousel" data-slide="prev">
                	<span class="glyphicon glyphicon-chevron-left"></span>
                	<span class="sr-only">Previous</span>
                </a>
                <a class="right carouselPaginaPrincipal carousel-control" href="#myCarousel" data-slide="next">
                	<span class="glyphicon glyphicon-chevron-right"></span>
                	<span class="sr-only">Next</span>
                </a>
                           
    		</div>
    	</div>
        <!-- Em destaque -->
        
    </div>
</div>








