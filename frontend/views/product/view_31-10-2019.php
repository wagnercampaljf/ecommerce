<?php

use common\models\Respostas;
use common\models\ValorProdutoFilial;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\widgets\MaskedInput;
use yii\widgets;

/* @var $this yii\web\View
 * @var $produto common\models\Produto
 */

//organização das breadcrumbs em inicio/cat/subcat/produto
$this->title = $produto->nome . ' ' . $produto->codigo_global . ' ';
$slug = (!empty($produto->slug)) ? "/" . $produto->slug : " ";
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::to(['/p'], 'https') . '/' . $produto->id . $slug]);
$this->registerMetaTag(['name' => 'description', 'content' => 'Compre agora ' . $produto->nome . ' com o melhor preço no Peça Agora. Escolha o modelo de ' . $produto->subcategoria->nome . ' e aproveite nossas ofertas!']);

$this->registerJsFile(
    Url::to(['frontend/web/js/productPage.js']),
    ['depends' => [frontend\assets\AppAsset::className()]]
);
$this->registerJsFile(
    Url::to(['frontend/web/js/ouibounce.min.js']),
    ['depends' => [frontend\assets\AppAsset::className()]]
);
$this->registerCssFile(
    Url::to(['frontend/web/css/ouibounce.css']),
    ['depends' => [frontend\assets\AppAsset::className()]]
);

$this->registerJs("
$('#avise').click(function(){
    $('html, body').animate({
        scrollTop: $( $(this).attr('href') ).offset().top
    }, 1300);
    return false;
});");
/*$this->registerJs("
var _ouibounce = ouibounce(document.getElementById('ouibounce-modal'), {
  aggressive: true,
  timer: 20000,
  callback: function() {
   console.log('ouibounce fired!'); 
   }
});

$('body').on('click', function() {
  $('#ouibounce-modal').hide();
});

$('#ouibounce-modal .modal-footer').on('click', function() {
  $('#ouibounce-modal').hide();
});

$('#ouibounce-modal .modal-pop').on('click', function(e) {
  e.stopPropagation();
});
");*/

$this->params['breadcrumbs'][] = [
    'label' => $produto->subcategoria->categoria->nome,
    'url' => ['auto/' . $produto->subcategoria->categoria->slug]
];
$this->params['breadcrumbs'][] = [
    'label' => $produto->subcategoria->nome,
    'url' => [
        'auto/' . $produto->subcategoria->categoria->slug . '/' . $produto->subcategoria->slug,
    ]
];
$this->params['breadcrumbs'][] = $this->title;
$juridica = Yii::$app->params['isJuridica']();

$maxValue = ValorProdutoFilial::find()->ativo()->maiorValorProduto($produto->id, $juridica)->one();
if ($maxValue != null) {
    $minValue = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id, $juridica)->one();
} else {
    $maxValue = ValorProdutoFilial::find()->ativo()->maiorValorProduto($produto->id, $juridica, '>=')->one();
    $minValue = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id, $juridica, '>=')->one();
}
echo Html::hiddenInput('produto_id', Yii::$app->request->get('id'), ['id' => 'produto_id']);

echo "<script> fbq('track', 'ViewContent'); </script>";

?>

<div itemscope itemtype="http://schema.org/Product" class="site-product">
    <div class="panel panel-body">
        <div class="main-product-view">
        	<!-- <div class="img-product-view text-center  col-lg-2 col-md-2 col-sm-2 col-xs-12"> -->
        	<div class="img-product-view text-center  col-lg-1 col-md-1 col-sm-1 hidden-xs">
        		<?php
        		$options = ['width' => '100%', 'height' => '100%','align' => 'center', 'alt' => $produto->getLabel(), 'title' => $produto->getLabel(), 'itemprop' => 'image'];
                $imagens = $produto->getImages();
		        $total = 0;
                foreach ($imagens as $k => $imagem) {
//                   echo Html::img($imagem, $options);
			         $total++;
                }
                ?>
        		
        		<div class="clearfix">
                    <div id="thumbcarousel" class="carousel slide" data-interval="false">
                        <div class="carousel-inner">
                            <div class="item active">
                                <?php
                                
                                foreach ($imagens as $k => $imagem) {
                                    //echo '<div class="row"><div data-target="#carousel" data-slide-to="'.$k.'" class="thumb">';
                                    echo '<div class="row margem_produto_miniatura"><div data-target="#carousel" data-slide-to="'.$k.'" class="">';
                                    $options_imagem = ['height' => "52", 'width' => "62", 'alt' => $produto->getLabel(), 'title' => $produto->getLabel(), 'itemprop' => 'image'];
                                    echo Html::img($imagem, $options_imagem);
                                    echo '</div></div>';
                                }
				                    /*$imgaviso="https://www.pecaagora.com/frontend/web/assets/img/aviso.png";
                                    echo '<div data-target="#carousel" data-slide-to="'.$total.'" class="thumb">';
                                    echo Html::img($imgaviso, $options);
                                    echo '</div>';*/
                                
                                    if (isset($produto->video)){
                                        $video_complemento  = explode("=",$produto->video);
                                        if (isset($video_complemento[1])){                                            
                                            $imgaviso="https://www.pecaagora.com/frontend/web/assets/img/youtube-logo.jpg";
                                            echo '<div data-target="#carousel" data-slide-to="'.$total.'" class="" align="center">';
                                            echo Html::img($imgaviso, $options_imagem);
                                            echo '</div>';
                                        }
                                    }
                                
                                ?>
                                
                                <!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/9jzgzuIT8mE" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>-->
                            </div><!-- /item -->
			    
                        </div><!-- /carousel-inner -->
			
                            
                        
                    </div> <!-- /thumbcarousel -->
                </div><!-- /clearfix -->
        	</div>
            <div class="img-product-view text-center  col-lg-7 col-md-7 col-sm-7 col-xs-12">
                <div id="carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($imagens as $k => $imagem) {
                                $ative = ($k == 0) ? "active" : "";
                                echo '<div class="item '.$ative.'" data-thumb="0">';
                                //echo Html::img($imagem, $options);
				list($width, $height, $type, $attr) = getimagesize($imagem);
                                if ($width < 10000 && $height < 10000){
                                    $options_imagem = ['id'=>'zoom_0'.$k, 'width' => '100%', 'height' => '100%','align' => 'center', 'alt' => $produto->getLabel(), 'title' => $produto->getLabel(), 'itemprop' => 'image'];
                                    echo Html::img($imagem, $options_imagem);
                                } else{
                                    $options_imagem_zoom = ['id'=>'zoom_0'.$k, 'data-zoom-image' => $imagem, 'width' => '100%', 'height' => '100%','align' => 'center', 'alt' => $produto->getLabel(), 'title' => $produto->getLabel(), 'itemprop' => 'image'];
                                    echo Html::img($imagem, $options_imagem_zoom);
                                    echo "<script>$('#zoom_0".$k."').elevateZoom({zoomType: 'window',cursor: 'crosshair',zoomWindowFadeIn: 500,zoomWindowFadeOut: 750});</script>";
                                }
                                echo '</div>';
                            } 
                        
                            //Yii::$app->
                        
				            /*echo '<div class="item" data-thumb="0">';
				            $imgaviso="https://www.pecaagora.com/frontend/web/assets/img/aviso.png";
                            echo Html::img($imgaviso, $options);
                            echo '</div>';*/
                        
                            if (isset($produto->video)){
                                $video_complemento  = explode("=",$produto->video);
                                if (isset($video_complemento[1])){
                                    $video_id   = explode("&",$video_complemento[1]);
                                    //print_r($video_complemento);print_r($video_id); die;
                                    echo '<div class="item" data-thumb="0">';
                                    echo '<div class="embed-responsive embed-responsive-16by9">';
                                    echo '<iframe  width="520" height="292" class="embed-responsive-item" src="https://www.youtube.com/embed/'.$video_id[0].'" allowfullscreen></iframe>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            }
                            
                        ?>
                        
						
						<!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/9jzgzuIT8mE" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>-->
                    </div>
                </div>
                

            </div>
            
            <div class=" col-lg-4 col-md-4 col-sm-4 col-sx-12">
            	<!-- <div class="container header-product-view">-->
                    <span itemprop="name" style="word-wrap: break-word" class="h3"><b><?= $produto->nome." <font color='white'>(".$produto->codigo_global.")</font>"//$produto->label ?> </b></span><br><br>
                <!-- </div>-->
                                
                <div class="panel panel-primary price lead text-center">
                    <?php
                    $ativo = true;
                    if (isset(Yii::$app->session["carrinho"][$minValue->produtoFilial->id])) {
                        $ativo = false;
                    }
                    ?>
                    <?php if ($minValue->produtoFilial->estoque > 0) { ?>
                        <div itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer" class="panel-body">
                            <small>Melhor Preço</small>
                            <br>
                            <span itemprop="lowPrice" class="h1 text-success"><?= $minValue->labelTitulo($juridica) ?></span>
                            <div class="row">
                                <br>
                                <a href="" aria-disabled="true" onclick="addProdutoCarrinho('<?= $minValue->produtoFilial->id ?>', this);return false;"
                                   class="btn-lg col-lg-offset-2 col-lg-8  btn-danger <?= ($ativo ?: 'disabled') ?>"><i
                                   class="no-color fa fa-shopping-cart"></i><b> <?= ($ativo ? "Comprar" : 'Já Adicionado') ?></b>
                                </a>
                            </div>
                            <meta itemprop="priceCurrency" content="BRL"/>
                        </div>
                    <?php } else { ?>
                        <div class="panel-body">
                            <span class="h1 text-success"></span>
                            <div class="row">
                                <?php
                                $href = Url::base() . '/orcamento?peca=' . $produto->id . '&filial=' . $minValue->produtoFilial->filial->id;
                                echo Html::a('Solicite um <br> Orçamento!', $href, ['class' => 'btn btn-success col-lg-6 col-lg-offset-3', 'target' => '_blank']); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <small>
                        <small>Vendido e entregue por <b><?= $minValue->produtoFilial->filial->nome ?></b></small>
                    </small>

                    <br>
                    <!--<div class="panel-footer">
                            <span class="h5"><a id="outrasLojas" href="#tabconteudo">Veja nossas outras <b><?= ""//count($filiais) ?></b> ofertas deste produto <i class="fa fa-arrow-right" aria-hidden="true"></i></a></span>
                    </div>-->
                </div>
                
                <div class="form-inline" action="">
                    <div class="row">
                        <div class="input-group col-lg-10">
                            <?= MaskedInput::widget([
                                'name' => 'seu-cep',
                                'mask' => '99999-999',
                                'value' => Yii::$app->params['getCepComprador'](false),
                                'options' => [
                                    'id' => 'seu_cep',
                                    'placeholder' => 'Digite seu CEP',
                                    'class' => 'form-control seu-cep',
                                ]
                            ])
                            ?>
                            <span class="input-group-btn ">
                                <button data-toggle="tab" type="submit" id="calcula-frete" class="btn btn-primary">
                                    <i class="fa no-color fa-truck"></i>
                                    Calcular Frete
                                </button>
                        </span>
                        </div>
                    </div>
                    <div class="filial_<?= $minValue->produtoFilial->filial->id ?> product-view-div panel-lojas-product-view-datails col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left">

                    </div>
                </div><br>
                
                <div class="h4"> <u>Detalhes do Produto</u></div>
                <?php //if (!empty($produto->fabricante->nome)) { ?>
                    <!--<div class="product-detail">
                        <strong>Fabricante: </strong><?= ""//$produto->fabricante->nome ?>
                    </div>-->
                <?php //} ?>
                <?php //if (!empty($produto->fabricante->sac)) { ?>
                    <!-- <div class="product-detail">
                        <strong>SAC do Fabricante: </strong><?= ""//$produto->fabricante->sac ?>
                    </div>-->
                <?php //} ?>
                <?php //if (!empty($produto->codigo_fabricante)) { ?>
                    <!--<div class="product-detail">
                        <strong>Identificador do Fabricante: </strong><?= ""//$produto->codigo_fabricante ?>
                    </div>-->
                <?php //} ?>
		<?php if (!empty($produto->id)) { ?>
                    <div class="product-detail">
                        <strong>Identificador do Produto: </strong><?= "PA".$produto->id ?>
                    </div>
                <?php } ?>
                <?php //if (!empty($produto->codigo_global)) { ?>
                    <!--<div class="product-detail">
                        <strong>Código Global: </strong><?= ""//$produto->codigo_global ?>
                    </div>-->
                <?php //} ?>
                <?php if (!empty($produto->codigo_montadora)) { ?>
                    <div class="product-detail">
                        <strong>NCM: </strong><?= $produto->codigo_montadora ?>
                    </div>
                <?php } ?>
		<?php if (!empty($produto->codigo_barras)) { ?>
                    <div class="product-detail">
                        <strong>EAN: </strong><?= $produto->codigo_barras ?>
                    </div>
                <?php } ?>
                <span itemprop="description" class="clearfix">
                    <?= $produto->microDescricao() ?>
                     </span>
                <br>
                <a href="#tabconteudo" id="verAplicacao" class="h4">
                    Ver Aplicação
                </a>
                <hr>
                
                
                <div>
                    <a href="https://pecaagora.zendesk.com/hc/pt-br/articles/206974937-O-que-%C3%A9-Compra-Garantida-e-Como-Funciona-" target="_blank"><img width="70%"
                                                                                                                                                            src="https://www.moip.com.br/imgs/banner_2_3.jpg"></a>
                    <a href="https://pecaagora.zendesk.com/hc/pt-br/articles/206974937-O-que-%C3%A9-Compra-Garantida-e-Como-Funciona-" target="_blank"> <img width="27%"
                                                                                                                                                             src="https://www.moip.com.br/imgs/banner_1_3.jpg"></a>
                </div>

		<?php if (!empty($produto->fabricante->nome)) { ?>
                    <div class="product-detail">
                        <font color="white"><strong>Fabricante: </strong><?= $produto->fabricante->nome ?></font>
                    </div>
                <?php } ?>
		<?php if (!empty($produto->codigo_global)) { ?>
                    <div class="product-detail">
                        <font color="white"><strong>Código Global: </strong><?= $produto->codigo_global ?></font>
                    </div>
                <?php } ?>
		<?php if (!empty($produto->codigo_fabricante)) { ?>
                    <div class="product-detail">
                        <font color="white"><strong>Identificador do Fabricante: </strong><?= $produto->codigo_fabricante ?></font>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>

    <div id="tabconteudo" class="tab-product-view">
        <ul id="tabs" class="nav nav-tabs nav-tabs-view" data-tabs="tabs">
            <li class="active"><a id="aplicacao" href="#compatibilidade" data-toggle="tab">Detalhes do Produto</a></li>
            <?= ""//'<li><a id="loja" href="#lojas" data-toggle="tab">Outras Lojas</a></li>'?>
            <li><a href="#sobre" data-toggle="tab">Sobre a Loja</a></li>
        </ul>
        <div class="tab-content tab-content-view">
            <div class="tab-pane active fade in" id="compatibilidade">
                <?= $this->render('view/_compatibilidade', ['produto' => $produto]); ?>
            </div>
            <div class="tab-pane  fade in" id="lojas">
                <?= ""//$this->render('view/_lojas', ['produto' => $produto, 'filiais' => $filiais]); ?>
            </div>
            <div class="tab-pane fade" id="sobre">
                <?= $this->render('view/_sobre', ['produto' => $produto]); ?>
            </div>
        </div>

	<!--
	<h4>Comentários</h4>
        <div id="disqus_thread"></div>
        <script>
            (function () {  // DON'T EDIT BELOW THIS LINE
                var d = document, s = d.createElement('script');

                s.src = '//peaagora.disqus.com/embed.js';

                s.setAttribute('data-timestamp', +new Date());
                (d.head || d.body).appendChild(s);
            })();
        </script>
        <noscript>Por favor ative o JavaScript para ver os <a href="https://disqus.com/?ref_noscript" rel="nofollow">Comentários.</a></noscript>
	-->
    </div>

</div>
<!--<div id="ouibounce-modal">
    <div class="underlay"></div>
    <div class="modal-pop">
        <div class="modal-title">
            <h2> Ei, Espere... </h2>
            <h2>Nos ajude a melhorar o Peça Agora!</h2>
        </div>
        <div class="modal-body">
            <h3>Por que está indo embora?</h3>

            <?= ''//$this->render('_form', ['model' => new Respostas(), 'produtoId' => $produto->id]); ?>
        </div>
        <div class="modal-footer">
            <p onclick="document.getElementById('ouibounce-modal').style.display = 'none';">
                Não, obrigado!
            </p>
        </div>
    </div>
</div>-->


<!--- 07-11-2019 Mobile -->

<style>
    .fab {
        background: rgba(0, 129, 130, 0.22);
        height: 60px;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;;
        padding: 10px 15px;
        z-index: 100;
    }

    button {
        display: block;
    }

    @media (max-width: 768px) {
        button {
            display: block;
        }
    }


    .fab button{
        cursor: pointer;
        width: 48px;
        height: 48px;
        border-radius: 3px;
        background-color: #cb60b3;
        border: none;
        box-shadow: 0 1px 5px rgba(0,0,0,.4);
        font-size: 24px;
        color: white;
    }

    .fab button.main{
        width: 130px;
        height: 40px;
        left: 50px;
        background-color: #ff0005;
        bottom: 0;
        z-index: 20;
    }

    .fab button.main:before{
        content: 'Comprar';
        font-size: 20px;
    }



    .fab button.main:active,
    .fab button.main:focus{
        outline: none;
        background-color: #118522;
        box-shadow: 0 3px 8px rgba(0,0,0,.5);
    }


    /*  inicio botao 2  */

    .fab button.main2:active,
    .fab button.main2:focus{
        outline: none;
        background-color: #118522;
        box-shadow: 0 3px 8px rgba(0,0,0,.5);
    }
    .fab button.main2:before{
        content: 'Comprar';
        font-size: 20px;
    }

    .fab button.main2{
        width: 160px;
        height: 30px;
        background-color: #ff0005;
        right: 50px;
        bottom: 0;
        z-index: 20;
    }
    .fab {
        display: none;
    }

    @media (max-width: 768px) {
        .fab {
            display: flex;
        }
    }
    .preco{
        color: #fffafe;
        border-width: 1px;
        border-style: dashed;
        background-color: rgba(0, 117, 118, 0.87);
        height: 27px;
        font-size: 18px;
        right: 20px;
        font-weight: bold;
        text-align: center;
    }
    .valor{

        color: #fffafe;

    }


</style>

<div class="container">
    <div class="fab col-sm-1">
        <a href="" aria-disabled="true" onclick="addProdutoCarrinho('<?= $minValue->produtoFilial->id ?>', this);return false; <?= ($ativo ?: 'disabled') ?>">
            <button type="button" class="main">
            </button>
        </a>

        <div class="col-sm-1 preco">
            <span class="valor" itemprop="lowPrice"><?= $minValue->labelTitulo($juridica) ?></span>
        </div>
        <div class="col-sm-5">

        </div>
    </div>
</div>
