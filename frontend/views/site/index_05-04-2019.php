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

<div class="row home-busca col-lg-12 text-center header">
    <div class="row col-lg-12">
        <div id="myCarouselBanner" class="carousel slide" data-ride="carousel">
        	<div class="carousel-inner">
                <div class="item active">
                	<div class="row col-lg-12">
                        <div class="hidden-xs col-lg-10 col-lg-offset-1 col-xs-12 col-sm-12 col-md-10 col-md-offset-1">
                            <img src="<?= Url::to('@assets/'); ?>img/home-chassi.webp" width="100%" height="100%">
                        </div>
                        <div class="visible-xs col-lg-10 col-lg-offset-1 col-xs-12 col-sm-12 col-md-10 col-md-offset-1">
                            <a href="http://bit.ly/2rNDwtf">
                                <img src="<?= Url::to('@assets/'); ?>img/home-cel.jpg" width="100%">
                            </a>
                        </div>
                    </div>
                </div>   
            </div>
            
            <!-- Left and right controls -->
            <a class="left carouselPaginaPrincipal carousel-control" href="#myCarouselBanner" data-slide="prev">
            	<span class="glyphicon glyphicon-chevron-left"></span>
            	<span class="sr-only">Previous</span>
            </a>
            <a class="right carouselPaginaPrincipal carousel-control" href="#myCarouselBanner" data-slide="next">
            	<span class="glyphicon glyphicon-chevron-right"></span>
            	<span class="sr-only">Next</span>
            </a>
        </div>
    </div>
    <!-- <br>
    <br>
    <div class="home-busca-campo col-lg-offset-3 col-lg-6">
        <form id="main-search" class="form form-inline " role="form" action="<?= ""//Url::to(['/search']) ?>">
            <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12"
                 style="padding-left: 0px !important;padding-right: 0px !important; ">
                <input type="text"
                       name="<?= ""//is_null(Yii::$app->request->get('codigo_global')) ? 'nome' : 'codigo_global' ?>"
                       id="main-search-product" class="form-control form-control-search input-lg sombra data-hj-whitelist"
                       placeholder="Procure por Código, Marca, Aplicação ..."
                       value="<?= ""//Yii::$app->request->get('nome',Yii::$app->request->get('codigo_global', null)) ?>">
                <span class="input-group-btn">
                      <button type="submit" class="btn btn-primary control btn-lg sombra" id="main-search-btn"><i style="color: #fff" class="fa fa-2x fa-search no-color"></i></button>
                    </span>
            </div>
            <?php
            /*$this->registerJs("
            $('#main-search-product').change(function () {
              results = new RegExp('#').exec($(this).val());
              if(results != null && results['index'] == 0 ){
                $(this).attr('name', 'codigo_global');
              }else{
                $(this).attr('name', 'nome');
              }
            });
          ");*/
            ?>
        </form>
        <br>
        <div class="row col-lg-12">
            <h2>Olá, Qual peça você procura hoje?</h2>
        </div>
    </div>-->
</div>

<div class="container ">
    <div class="site-index clearfix pull-left">
        <div class="row margin-bottom-25">
            <!-- Em oferta  -->
            <div class="h3 margin-bottom-15">Produtos em Oferta</div>
            <div id="myCarouselOferta" class="carousel slide" data-ride="carousel">
              <div class="carousel-inner">
                
                <?php 
        			if (!YII_DEBUG) {
        			    $arrayid = [31591, 37768, 231664, 38614, 40156, 222293, 222292, 231596, 231514, 231348, 231649, 231651, 231648, 231647, 231646, 222241, 231618, 227171, 222500, 222515, 222528, 222496, 222494, 222503, 222517, 222513, 222523, 222518, 222505, 227171, 41523, 56129, 55917]; //producao
        			} else {
        			    $arrayid = [31591, 37768, 231664, 38614, 40156, 222293, 222292, 231596, 231514, 231348, 231649, 231651, 231648, 231647, 231646, 222241, 31519, 231618, 227171, 222500, 222515, 222528, 222496, 222494, 222503, 222517, 222513, 222523, 222518, 222505, 227171, 41523, 56129, 55917]; //local
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
                                        
                                        $produtos = Produto::find()->andWhere(['id' => $arrayid])->orderBy('id')->all();
                                        for ($i = ($x*4); $i <= ($x*4+3); $i++) {
                                            $produto = ArrayHelper::getValue($produtos, $i);
                                            //$teste = $produtos[$i]->menor();
                                            //print_r($teste);
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
        <div class="row col-xs-12  col-sm-12 col-md-12 col-lg-12 clearfix" style="padding-right: 0">
            <?= $this->render('createNewsletter', ['model' => new Newsletter()]); ?>
        </div>        <!-- Banner -->
        <br> <br>

        <!-- Em destaque -->
        <div class="row margin-bottom-25">
            <div class="h3 margin-bottom-15">Produtos em Destaque</div>
            <div id="myCarouselDestaque" class="carousel slide" data-ride="carousel">
                  <div class="carousel-inner">
                    
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
                                        $produtos = Produto::find()->andWhere(['id' => $arrayid])->orderBy('id')->all();
                                        for ($i = ($x*4)+12; $i <= ($x*4+15); $i++) {
                                            $produto = ArrayHelper::getValue($produtos, $i);
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
                                        $produtos = Produto::find()->andWhere(['id' => $arrayid])->orderBy('id')->all();
                                        for ($i = ($x*4)+24; $i <= ($x*4+27); $i++) {
                                            $produto = ArrayHelper::getValue($produtos, $i);
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








