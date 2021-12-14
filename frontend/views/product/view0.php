<?php

use common\models\Produto;
use common\models\Respostas;
use common\models\ValorProdutoFilial;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\widgets\MaskedInput;
use yii\widgets;
use vendor\iomageste\Moip\Moip;
use Inacho\CreditCard;



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
<style type="text/css">

    p {
        text-align: justify;
        text-indent: 50px;
        font-size: 16px;
    }

    h2 {
        margin-bottom: 25px;
    }

    }

    .img-container{
        width: 300px;
        height: 200px;
        overflow: hidden;
        border: 2px solid #000;
        z-index: 1000000;
    }

    .img-container img{
        width: 100%;
        height: 100%;
        -webkit-transition: -webkit-transform .5s ease;
        transition: transform .5s ease;
    }

    .img-container:hover img{
        -webkit-transform: scale(1.5 );
        transform: scale(1.5);
    }
    .by-brand{
        color: #007576;
        margin: 0;
    }


</style>
<!-- Created by Rohan Hapani -->


<div itemscope itemtype="http://schema.org/Product" class="site-product">
    <div class="panel panel-body">
        <div class="col-sm-12 colzero">
            <span itemprop="name " style="word-wrap: break-word; font-size: 18px" class="h3"><b><?= $produto->nome." <font color='#FFFFFF'>(".$produto->codigo_global.")</font>"//$produto->label ?> </b></span><br><br>
            <span style="display: block; margin: -20px 0 10px 0">Marca.: <a class="by-brand" href="#"><?= $produto->marcaProduto?></a></span>
        </div>
        <div class="main-product-view">
        	<!-- <div class="img-product-view text-center  col-lg-2 col-md-2 col-sm-2 col-xs-12"> -->

            <!-- CAROUSEL -->
            <div class="img-product-view text-center  col-lg-1 col-md-1 col-sm-1 hidden-xs">
                <?php
                $options = ['width' => '100%', 'height' => '100%','align' => 'center', 'alt' => $produto->getLabel(), 'title' => $produto->getLabel(), 'itemprop' => 'image'];
                $imagens = $produto->getImages();
                $total = 0;
                foreach ($imagens as $k => $imagem) {
// echo Html::img($imagem, $options);
                    $total++;
                }
                ?>

                <div class="clearfix">
                    <div id="thumbcarousel" class="carousel slide" data-interval="false">
                        <div class="carousel-inner">
                            <div class="item active ">
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

                    <div class="carousel-inner img-container" id="zoom">
                        <?php foreach ($imagens as $k => $imagem) {
                            $ative = ($k == 0) ? "active" : "";
                            echo '<div class="item '.$ative.'" data-thumb="0">';

                            //echo Html::img($imagem, $options);
                            list($width, $height, $type, $attr) = getimagesize($imagem);
                            if ($width < 500 && $height < 500){
                                $options_imagem = ['id'=>'zoom_0'.$k, 'width' => '100%', 'height' => '100%','align' => 'center', 'alt' => $produto->getLabel(), 'title' => $produto->getLabel(), 'itemprop' => 'image'];
                                echo Html::img($imagem, $options_imagem);
                            } else{
                                //EXEMPLO ZOOM SEPARADO - echo "<img onClick='teste();' id='zoom_03' src='http://localhost/pecaagora/site/get-link?produto_id=6922&ordem=1' data-zoom-image='http://localhost/pecaagora/site/get-link?produto_id=6923&ordem=1'/>";
                                //$imagem_zoom = str_ireplace('get-link', 'get-link-zoom', $imagem);
                                $options_imagem_zoom = ['id'=>'zoom_0'.$k, 'data-zoom-image' => $imagem, 'width' => '100%', 'height' => '100%','align' => 'center', 'alt' => $produto->getLabel(), 'title' => $produto->getLabel(), 'itemprop' => 'image'];
                                echo Html::img($imagem, $options_imagem_zoom);

                                //Zoom//

                                //echo "<script>$('#zoom_0".$k."').elevateZoom({zoomType: 'window',cursor: 'crosshair',zoomWindowFadeIn: 500,zoomWindowFadeOut: 750});</script>";
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
            <!-- CAROUSEL -->




            <?= ""//(($produto->id)%2==1) ?  222: 111?>



            <!-- CRONOMETRO -->
            <h6><i class="fa fa-lock fa-lg" aria-hidden="true"></i> Compra 100% segura</h6>

          
                <div class=" col-lg-4 col-md-2 col-sm-4 col-sx-12 text-center hidden-xs" style="background-color:  #7a1002">
                    <span class="text-center wrapper" style="color: #ffffff; font-size: 15px;padding: 6px; border-bottom:solid 2px #ffffff"><img src="<?= Url::to('@assets/'); ?>img/relogio.gif" style="width: 32px; height: 32px; " border="0" alt="relogio-imagem-animada-0148"><h4> &nbsp OFERTA ESPECIAL  </h4> </span>  <!--<img src="<?= Url::to('@assets/'); ?>img/chapeu-natal.png" style="width: 45px; height: 40px; " border="0" alt="relogio-imagem-animada-0148">--><br>
                    <span class="contagem"><span class="text-center" style="color: #ffffff;"> PREÇO COM <small style="font-size: 16px;color: #ffffff;font-weight: bold;"><?=(($produto->id)%2==1)? "-18%": "-11%"?>&nbsp</small></span>
                    </span>
                    <span id="demo"></span>
                </div><br>


            <?= ""//(($produto->id)%2==1) ?  222: 111?>

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


                    document.getElementById("demo").innerHTML =  days +  " : " + hours + " : "
                        + minutes + " : " + seconds + "";


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

            </style>
            <!-- CRONOMETRO -->


            <div class=" col-lg-4 col-md-6 col-sm-4 col-sx-12" style="background-color: #d7d7d7;"><br>
            	<!-- <div class="container header-product-view">-->

                <!-- Quebra de linha 09/10/209-->

                <!-- POLITICA DE TROCAA -->


                <div class="row">
                    <span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star <?= (($produto->id)%5!=1) ? "checked" : ""?>"></span>
                </div><br>

                <!--BOTÃO ABRIR MODAL, POLITICA DE TROCA -->
                <span style="text-decoration: underline" data-toggle="modal" data-target="#modalExemplo">
                    <a href="#" style="color: #4A4E5A"> Veja a política de troca</a> <i class="fa fa-check-square-o" aria-hidden="true"></i>
                </span>


                <div class="modal fade" id="modalExemplo" aria-hidden="true" style="z-index: 999999">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <img class="logo" alt="Peca Agora" title="Peça Agora"
                                     src="<?= Url::to('@assets/'); ?>img/pecaagora_padrao.png">
                                <h2 class="modal-title" id="exampleModalLabel">Política de Troca e Devolução </h2>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <h3>Devolução:</h3>
                                <p>De acordo com o CDC (Código de Defesa do Consumidor), serão aceitas somente as devoluções que forem informadas dentro do prazo de 7 (sete) dias corridos após a data da entrega da mercadoria;</p>



                                <p>Só serão aceitas as mercadorias que não possuírem violação ou indícios de má uso do produto;</p>

                                <p>Em caso de arrependimento de compra ou produtos adquiridos erroneamente, terão o prazo de 7 (sete) dias corridos com a possibilidade de devolução ou troca;</p><br><br><br><br><br>

                                <div class="text-center"><h4>Recuse a entrega da mercadoria caso o produto esteja violado e nos avise imediatamente.</h4></div>


                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- POLITICA DE TROCAA -->







                <div class="panel panel-primary price lead text-center">
                    <?php $depor=0.9?>

                    <!-- PREÇO COM DESCONTO  -->
                    <span style=" font-size: 16px">De: </span> <span style="text-decoration: line-through; font-size: 16px">R$<?= (($produto->id)%2==1) ?  number_format(($minValue->getValorFinal($juridica)/ 0.82), 2, ',', '') : number_format(($minValue->getValorFinal($juridica)/0.89), 2, ',', '')?></span> <span style=" font-size: 16px"> por</span>

                    <?php
                    $ativo = true;
                    if (isset(Yii::$app->session["carrinho"][$minValue->produtoFilial->id])) {
                        $ativo = false;
                    }
                    ?>
                    <?php if ($minValue->produtoFilial->estoque > 0) { ?>
                        <div itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer" class="panel-body">
                            <small >Melhor Preço</small>
                            <br>
                            <span itemprop="lowPrice"  class="h1 text-success"><?= $minValue->labelTitulo($juridica) ?></span>

                            <div class="row">
                                <br>


			    <a href="<?= Yii::$app->urlManager->hostInfo.Yii::$app->urlManagerFrontEnd->baseUrl.'/carrinho/adicionar-produto-carrinho/?id='.$minValue->produtoFilial->id?>" aria-disabled="true"
                                   class="btn-lg col-lg-offset-2 col-lg-8  btn-danger  <?= ($ativo ?: 'disabled') ?>" style="height: 46px;"><i
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
                        <small style="color: white">Vendido e entregue por <b><?= $minValue->produtoFilial->filial->nome ?></b></small>
                    </small>

                    <br>
                    <!--<div class="panel-footer">
                            <span class="h5"><a id="outrasLojas" href="#tabconteudo">Veja nossas outras <b><?= ""//count($filiais) ?></b> ofertas deste produto <i class="fa fa-arrow-right" aria-hidden="true"></i></a></span>
                    </div>-->
                </div>

                <?php
                    $taxafixa=0.69;
                    $divide=6;
                    $divide5=5;
                    $divide4=4;
                    $divide3=3;
                    $divide2=2;
                    $acrescimos=0.0135;

                ?>



                <!-- DIVIDE DE 6 VEZES  -->
                <div><i class="fa fa-credit-card" aria-hidden="true"></i> <?= $minValue->labelTitulo($juridica) ?> em até 6x de <?= number_format(($minValue->getValorFinal($juridica)/ $divide), 2, ',', '') ?>   c/ juros no cartão de crédito.
                    <span style="text-decoration: underline" data-toggle="modal" data-target="#ExemploModalCentralizado">
                    <a href="#" style="color: #4A4E5A"> Mais formas de pagamentos</a></span>


                    <!-- Modal -->
                    <div class="modal fade" id="ExemploModalCentralizado" tabindex="-1" style="z-index: 999999" role="dialog" aria-labelledby="TituloModalCentralizado" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <img class="logo" alt="Peca Agora" title="Peça Agora"
                                     src="<?= Url::to('@assets/'); ?>img/pecaagora_padrao.png">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h3 class="modal-title" id="TituloModalCentralizado">Veja todas as parcelas</h3>
                                    <p class="alert alert-warning">
                                        <i class="glyphicon glyphicon-exclamation-sign"></i>
                                        ATENÇÃO - Taxa Fixa por Loja: <?= Yii::$app->formatter->asCurrency(Moip::TAXMOIP) ?>
                                    </p>
                                </div>
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true" style="color: black"> CARTÃO DE CRÉDITO</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false" style="color: black">BOLETO BANCÁRIO</a>
                                    </li>

                                </ul>
                                <div class="tab-content" style="border: 1px solid #bfbfbf" id="myTabContent">
                                    <div class="tab-pane fade " id="home" role="tabpanel" aria-labelledby="home-tab">
                                        <div class="modal-body">

                                            <?php
                                            $options = '';
                                            foreach (CreditCard::$parcelamentoMOIP as $nrParcelas => $juros) {
                                                $valor_prazo = $minValue->getValorFinal($juridica)+0.69;
                                                $taxMoip = Moip::TAXMOIP + ($valor_prazo * CreditCard::taxaMoip($nrParcelas));
                                                $valor_prazo += ($valor_prazo - $taxMoip) * CreditCard::jurosMoip($nrParcelas);
                                                $valor_parcela = $valor_prazo / $nrParcelas;

                                                $options .= "<i class=\"fa fa-credit-card\" aria-hidden=\"true\">&nbsp".$nrParcelas . "x de " . Yii::$app->formatter->asCurrency($valor_parcela) . " Total: " . Yii::$app->formatter->asCurrency($valor_prazo) . " ($juros%)"."</i><hr>";
                                            }
                                            echo $options;

                                            ?>
                                        </div>
                                        <div class="container">
                                            <ul class="list-unstyled">
                                                <li class="pagamento">
                                                    <img src="<?= Url::to('@assets/'); ?>img/visa.png" style="width: 40px; height: 27px">
                                                    <img src="<?= Url::to('@assets/'); ?>img/mastercard.png" style="width: 40px; height: 27px">
                                                    <img src="<?= Url::to('@assets/'); ?>img/amex.png" style="width: 40px; height: 27px" >
                                                    <img src="https://img.icons8.com/ultraviolet/37/000000/discover.png">
                                                    <img src="https://img.icons8.com/color/37/000000/stripe.png">
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                        <h3 style="margin-left: 10px">R$<?= number_format(($minValue->getValorFinal($juridica)+ $taxafixa), 2, ',', '') ?></h3> <p style="margin-left: 10px">  O boleto será gerado após a finalização de sua compra. Imprima e pague no banco ou pague pela internet utilizando o código de barras do boleto.</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>

                    </div>


                </div><br>

                
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
                </div><a href="http://www.buscacep.correios.com.br/sistemas/buscacep/default.cfm" target="_blank" >
                    <small style="color:#007576">Não sei meu CEP</small>
                </a> <br> <br> <br>
                
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
                    <i class="fa fa-plus-circle" aria-hidden="true"> Informações sobre o produto</i>
                </a>

               <!-- <a href="#graficopreco" id="verAplicacao" class="h4">
                    <i class="fa fa-line-chart" aria-hidden="true">Grafico de preço</i>
                </a>-->
                <hr>
                
                
                <div>
                    <div class=" row">
                        <ul class="list-unstyled">
                            <li class="pagamento">
                                <img src="<?= Url::to('@assets/'); ?>img/visa.png" style="width: 38px; height: 25px">
                                <img src="<?= Url::to('@assets/'); ?>img/mastercard.png" style="width: 38px; height: 25px">
                                <img src="<?= Url::to('@assets/'); ?>img/amex.png" style="width: 38px; height: 25px" >
                                <img src="https://img.icons8.com/ultraviolet/35/000000/discover.png">
                                <img src="https://img.icons8.com/color/35/000000/stripe.png">
                                <img src="<?= Url::to('@assets/'); ?>img/boleto.png" style="width: 38px; height: 25px" >
                            </li>

                    </div>

                </div>


                <p>
                    <button class="btn btn-link" style="background-color: #d7d7d7; !important;"  type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        <i class="fa fa-info-circle" style="color: #d7d7d7 " aria-hidden="true"></i>
                    </button>
                </p>
                <div class="collapse" id="collapseExample">
                    <div class="card card-body">
                        <?php if (!empty($produto->fabricante->nome)) { ?>
                            <div class="product-detail">
                                <font color="#d7d7d7"><strong style="color: #d7d7d7">Fabricante: </strong><?= $produto->fabricante->nome ?></font>
                            </div>
                        <?php } ?>
                        <?php if (!empty($produto->codigo_global)) { ?>
                            <div class="product-detail">
                                <font color="#d7d7d7"><strong style="color: #d7d7d7">Código Global: </strong><?= $produto->codigo_global ?></font>
                            </div>
                        <?php } ?>
                        <?php if (!empty($produto->codigo_fabricante)) { ?>
                            <div class="product-detail">
                                <font color="#d7d7d7"><strong style="color:#d7d7d7">Código Fabricante: </strong><?= $produto->codigo_fabricante ?></font>

                            </div>
                        <?php } ?>
                        <div class="product-detail">
                            <small style="color: #d7d7d7">Vendido e entregue por <b><?= $minValue->produtoFilial->filial->nome ?></b></small>

                        </div>
                        <?php if (!empty($produto->codigo_barras)) { ?>
                            <div class="product-detail">
                                <font color="#d7d7d7"><strong style="color: #d7d7d7">Código de barras: </strong><?= $produto->codigo_barras ?></font>
                            </div>
                        <?php } ?>

                    </div>
                </div>

            </div>

        </div>
    </div>


    <!-- MODELO DE PORDUTOS RELACIONADOS -->

    <!--<div class="col-sm-12 col-xs-12 pad-sm-md-lg panel">
        <div class="content-page hidden-xs">
            <div class="col-xs-12 colzero" style="margin-bottom: 40px;">
                <div class="page-heading">
                    <div class="h3 margin-bottom-15">Compre junto</div>
                </div>
                <div class="compre-junto-mini impressionUrlTrackingEvent" style="display: flex; flex-direction: row; align-items: center" data-url="">
                    <div class="col-sm-3">
                        <div class="product-container col-sm-12">
                            <div class="left-block padmobile">

                                <img id="product-img-116724" class="img-responsive img-produto" src="<?= Url::to('@assets/'); ?>img/teste4.jpeg"  >

                                <input id="check-0" class="checkbox-compre-junto" style="width: 20px; height: 20px; margin-top: 27px; display: none" type="checkbox" checked="" disabled="">
                            </div>
                            <div class="right-block padmobile" style="padding-top: 5px">
                                <h2 class="product-name">
                                    <span class="hidden-xs">PROTETOR ACRILICO FAROL PARA SCANIA P/G/R 2013 COLADO FAROL</span>
                                </h2>

                                <div class="content_price">
                                    <span style="color: #007576; font-weight: 600;font-size: 16px;">R$ 139,<span class="price-cent">56</span></span>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="align-center" style="margin-left: 0px; margin-right: 10px">
                        <i style="color: #909090; font-size: 20px;" class="fa fa-plus fa-1x"></i>
                    </div>
                    <div class="col-sm-7">
                        <div class="col-sm-11 tracking-id-product" style="display: flex; align-items: center">
                            <div class="col-sm-1">
                                <input id="check-1" class="checkbox-compre-junto" style="width: 20px; height: 20px;"  type="checkbox" checked="">
                            </div>
                            <div class="col-sm-3 col-xs-3 paddingNull">
                                <a href="#" class="show-bg-preloader tagManagerProductClick" >
                                    <img src="<?= Url::to('@assets/'); ?>img/teste1.jpg"  style="width: 90px; height: 90px"  >

                                </a>
                            </div>
                            <div class="col-sm-8 col-xs-9 show-bg-preloader" style="font-size: 14px; margin: 5px 0 0 0px;">
                                <a class="tagManagerProductClick" href="#" title="teste ">
                                    <span class="product-name">SINALIZADOR "GIROFLEX" FLASH AMARELO 48V ROTATIVO MAGNETICO DNI  </span>
                                    <br>
                                    <span style="color: #007576; font-weight: 600;font-size: 16px;">R$300,<span class="price-cent">20</span></span>

                                </a>
                            </div>
                        </div>
                        <div class="col-sm-11 tracking-id-product" style="display: flex; align-items: center" d>
                            <div class="col-sm-1">
                                <input id="check-2" class="checkbox-compre-junto" style="width: 20px; height: 20px;" data-product="{&quot;id&quot;:&quot;85259&quot;,&quot;trackingUrl&quot;:&quot;https:\/\/recs.chaordicsystems.com\/v0\/click\/?trackingId=Cg5sb2phZG9tZWNhbmljbxIHcHJvZHVjdBoEbnVsbCIxN2M1NWMyZTIxZDljNDJmMzllOWNlY2E5MTU5NzExZTgxNTY5OTMxNjMyOTE1MzA2MCoHZGVza3RvcDIDdG9wOiQ1ZjgwM2RmZi05NjQ0LTI5OTctNzgyNS1hNzYzMTc5MWQzZjNCBTg1MjU5SgExWiQxNDZkZWY2MC0xNWJlLTExZWEtYWExZi04NTk0NjNlYjM4ODk%3D&quot;,&quot;codProduto&quot;:85259,&quot;codCategoria&quot;:{&quot;codCategoria&quot;:11,&quot;categoria&quot;:&quot;Equipamento Auto Center&quot;,&quot;categoriaSlug&quot;:&quot;ferramentas-para-auto-center&quot;},&quot;subCategoria&quot;:{&quot;codSubCategoria&quot;:109,&quot;subCategoria&quot;:&quot;Alinhamento e Balanceamento&quot;,&quot;subCategoriaSlug&quot;:&quot;alinhamento-e-balanceamento&quot;},&quot;codigo&quot;:&quot;MAQUINAS RIBEIRO-MR7000A&quot;,&quot;produto&quot;:&quot;Balanceadora de Rodas Motorizada Azul 220v&quot;,&quot;tensao&quot;:&quot;220 Volts&quot;,&quot;foto1&quot;:&quot;11\/109\/85259\/Balanceadora-de-Rodas-Motorizada-Azul-22-maquinas-ribeiro-mr7000a1.JPG&quot;,&quot;foto2&quot;:&quot;11\/109\/85259\/Balanceadora-de-Rodas-Motorizada-Azul-22-maquinas-ribeiro-mr7000a2.JPG&quot;,&quot;precoDe&quot;:4417.94,&quot;preco&quot;:4333.22,&quot;billetPrice&quot;:3899.9,&quot;qtdAvaliacao&quot;:null,&quot;mediaAvaliacao&quot;:0,&quot;peso&quot;:78,&quot;promocao&quot;:null,&quot;subVitrine&quot;:false,&quot;ativo&quot;:true,&quot;nomeMarca&quot;:&quot;MAQUINAS RIBEIRO&quot;,&quot;abMercado&quot;:true,&quot;estoque&quot;:1,&quot;estoquePermitidoVenda&quot;:1,&quot;specialKit&quot;:false,&quot;clickQuantity&quot;:19470,&quot;installmentPaymentValue&quot;:361.1,&quot;installmentPaymentQuantity&quot;:12,&quot;bitDescontoQtde&quot;:false,&quot;bitFreteGratis&quot;:false,&quot;prodKit&quot;:null,&quot;slug&quot;:&quot;balanceadora-de-rodas-motorizada-azul-220v-maquinas-ribeiro-mr7000a&quot;,&quot;descontoPromocao&quot;:2,&quot;pontosAPrazo&quot;:8646,&quot;pontosAVista&quot;:8646}" type="checkbox" checked="">
                            </div>
                            <div class="col-sm-3 col-xs-3 paddingNull">
                                <a href="#" class="show-bg-preloader tagManagerProductClick" data-product="{&quot;id&quot;:&quot;85259&quot;,&quot;trackingUrl&quot;:&quot;https:\/\/recs.chaordicsystems.com\/v0\/click\/?trackingId=Cg5sb2phZG9tZWNhbmljbxIHcHJvZHVjdBoEbnVsbCIxN2M1NWMyZTIxZDljNDJmMzllOWNlY2E5MTU5NzExZTgxNTY5OTMxNjMyOTE1MzA2MCoHZGVza3RvcDIDdG9wOiQ1ZjgwM2RmZi05NjQ0LTI5OTctNzgyNS1hNzYzMTc5MWQzZjNCBTg1MjU5SgExWiQxNDZkZWY2MC0xNWJlLTExZWEtYWExZi04NTk0NjNlYjM4ODk%3D&quot;,&quot;codProduto&quot;:85259,&quot;codCategoria&quot;:{&quot;codCategoria&quot;:11,&quot;categoria&quot;:&quot;Equipamento Auto Center&quot;,&quot;categoriaSlug&quot;:&quot;ferramentas-para-auto-center&quot;},&quot;subCategoria&quot;:{&quot;codSubCategoria&quot;:109,&quot;subCategoria&quot;:&quot;Alinhamento e Balanceamento&quot;,&quot;subCategoriaSlug&quot;:&quot;alinhamento-e-balanceamento&quot;},&quot;codigo&quot;:&quot;MAQUINAS RIBEIRO-MR7000A&quot;,&quot;produto&quot;:&quot;Balanceadora de Rodas Motorizada Azul 220v&quot;,&quot;tensao&quot;:&quot;220 Volts&quot;,&quot;foto1&quot;:&quot;11\/109\/85259\/Balanceadora-de-Rodas-Motorizada-Azul-22-maquinas-ribeiro-mr7000a1.JPG&quot;,&quot;foto2&quot;:&quot;11\/109\/85259\/Balanceadora-de-Rodas-Motorizada-Azul-22-maquinas-ribeiro-mr7000a2.JPG&quot;,&quot;precoDe&quot;:4417.94,&quot;preco&quot;:4333.22,&quot;billetPrice&quot;:3899.9,&quot;qtdAvaliacao&quot;:null,&quot;mediaAvaliacao&quot;:0,&quot;peso&quot;:78,&quot;promocao&quot;:null,&quot;subVitrine&quot;:false,&quot;ativo&quot;:true,&quot;nomeMarca&quot;:&quot;MAQUINAS RIBEIRO&quot;,&quot;abMercado&quot;:true,&quot;estoque&quot;:1,&quot;estoquePermitidoVenda&quot;:1,&quot;specialKit&quot;:false,&quot;clickQuantity&quot;:19470,&quot;installmentPaymentValue&quot;:361.1,&quot;installmentPaymentQuantity&quot;:12,&quot;bitDescontoQtde&quot;:false,&quot;bitFreteGratis&quot;:false,&quot;prodKit&quot;:null,&quot;slug&quot;:&quot;balanceadora-de-rodas-motorizada-azul-220v-maquinas-ribeiro-mr7000a&quot;,&quot;descontoPromocao&quot;:2,&quot;pontosAPrazo&quot;:8646,&quot;pontosAVista&quot;:8646}" data-position="3" data-list="Compre junto" data-userid="">
                                    <img src="<?= Url::to('@assets/'); ?>img/teste2.jpg"  style="width: 90px; height: 90px"  >
                                </a>
                            </div>
                            <div class="col-sm-8 col-xs-9 show-bg-preloader" style="font-size: 14px; margin: 5px 0 0 0px;">
                                <a class="tagManagerProductClick" href="#" title="Balanceadora de Rodas Motorizada Azul 220v" data-product="{&quot;id&quot;:&quot;85259&quot;,&quot;trackingUrl&quot;:&quot;https:\/\/recs.chaordicsystems.com\/v0\/click\/?trackingId=Cg5sb2phZG9tZWNhbmljbxIHcHJvZHVjdBoEbnVsbCIxN2M1NWMyZTIxZDljNDJmMzllOWNlY2E5MTU5NzExZTgxNTY5OTMxNjMyOTE1MzA2MCoHZGVza3RvcDIDdG9wOiQ1ZjgwM2RmZi05NjQ0LTI5OTctNzgyNS1hNzYzMTc5MWQzZjNCBTg1MjU5SgExWiQxNDZkZWY2MC0xNWJlLTExZWEtYWExZi04NTk0NjNlYjM4ODk%3D&quot;,&quot;codProduto&quot;:85259,&quot;codCategoria&quot;:{&quot;codCategoria&quot;:11,&quot;categoria&quot;:&quot;Equipamento Auto Center&quot;,&quot;categoriaSlug&quot;:&quot;ferramentas-para-auto-center&quot;},&quot;subCategoria&quot;:{&quot;codSubCategoria&quot;:109,&quot;subCategoria&quot;:&quot;Alinhamento e Balanceamento&quot;,&quot;subCategoriaSlug&quot;:&quot;alinhamento-e-balanceamento&quot;},&quot;codigo&quot;:&quot;MAQUINAS RIBEIRO-MR7000A&quot;,&quot;produto&quot;:&quot;Balanceadora de Rodas Motorizada Azul 220v&quot;,&quot;tensao&quot;:&quot;220 Volts&quot;,&quot;foto1&quot;:&quot;11\/109\/85259\/Balanceadora-de-Rodas-Motorizada-Azul-22-maquinas-ribeiro-mr7000a1.JPG&quot;,&quot;foto2&quot;:&quot;11\/109\/85259\/Balanceadora-de-Rodas-Motorizada-Azul-22-maquinas-ribeiro-mr7000a2.JPG&quot;,&quot;precoDe&quot;:4417.94,&quot;preco&quot;:4333.22,&quot;billetPrice&quot;:3899.9,&quot;qtdAvaliacao&quot;:null,&quot;mediaAvaliacao&quot;:0,&quot;peso&quot;:78,&quot;promocao&quot;:null,&quot;subVitrine&quot;:false,&quot;ativo&quot;:true,&quot;nomeMarca&quot;:&quot;MAQUINAS RIBEIRO&quot;,&quot;abMercado&quot;:true,&quot;estoque&quot;:1,&quot;estoquePermitidoVenda&quot;:1,&quot;specialKit&quot;:false,&quot;clickQuantity&quot;:19470,&quot;installmentPaymentValue&quot;:361.1,&quot;installmentPaymentQuantity&quot;:12,&quot;bitDescontoQtde&quot;:false,&quot;bitFreteGratis&quot;:false,&quot;prodKit&quot;:null,&quot;slug&quot;:&quot;balanceadora-de-rodas-motorizada-azul-220v-maquinas-ribeiro-mr7000a&quot;,&quot;descontoPromocao&quot;:2,&quot;pontosAPrazo&quot;:8646,&quot;pontosAVista&quot;:8646}" data-position="3" data-list="Aproveite e leve também" data-userid="" style="min-height: 66px; display: block;">
                                    <span class="product-name">ENGRENAGEM PLANETARIA EIXO TRAS. DANA 80 - 284 VW</span>
                                    <br>
                                    <span style="color: #007576; font-weight: 600;font-size: 16px;">R$200,<span class="price-cent">10</span></span>

                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="align-center" style="margin-left: 0px; margin-right: 10px; margin-left: -90px;">
                        <i style="color: #909090; font-size: 40px; font-weight: 600;">=</i>
                    </div>
                    <div class="col-sm-3">
                        <div class="" style="padding: 15px;">
                            <div style="text-align: center;">
                                <span id="qtd-products">Comprar  3 itens por</span><br>
                                <span id="price-products">R$ 639,86</span>
                            </div>
                            <a class="btn btn-compre-junto strong600" id="add-to-cart-compre-junto">Comprar Junto</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .compre-junto-mini  {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
            }

            .compre-junto-mini .align-center {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .compre-junto-mini  {
                display: flex;
                flex-direction: column;
            }

            .compre-junto-mini  {
                border: solid 1px #f9f5f5;
            }

            .compre-junto-mini {
                border-top: solid 1px #f9f5f5;
            }

            .compre-junto-mini .btn-compre-junto {
                color: #0E661B;
                border-color: #0E661B;
                margin: 2px auto;
                padding: 7px 5px 7px 5px;
                display: block;
            }

            .compre-junto-mini #price-products {
                font-size: 18px;
                font-weight: bold;
                color: #ff5225;

            }

            .compre-junto-mini .product-name {
                color: #000000c4;
                font-weight: 600;
            }


        </style>
    </div>-->

    


    <!-- carousel  CAROUSEL PRODUTOS RELACIONADOS
 <style type="text/css">


    .carousel {
        margin: 50px auto;
        padding: 0 70px;
    }
    .carousel .item {
        color: #747d89;
        min-height: 325px;
        text-align: center;
        overflow: hidden;
    }
    .carousel .thumb-wrapper {
        padding: 25px 15px;
        background: #fff;
        border-radius: 6px;
        text-align: center;
        position: relative;
        box-shadow: 0 2px 3px rgba(0,0,0,0.2);
    }
    .carousel .item .img-box {
        height: 120px;
        margin-bottom: 20px;
        width: 100%;
        position: relative;
    }
    .carousel .item img {
        max-width: 100%;
        max-height: 100%;
        display: inline-block;
        position: absolute;
        bottom: 0;
        margin: 0 auto;
        left: 0;
        right: 0;
    }
    .carousel .item h4 {
        font-size: 18px;
    }
    .carousel .item h4, .carousel .item p, .carousel .item ul {
        margin-bottom: 5px;
    }
    .carousel .thumb-content .btn {
        color: #007475;
        font-size: 11px;
        text-transform: uppercase;
        font-weight: bold;
        background: none;
        border: 1px solid #007475;
        padding: 6px 14px;
        margin-top: 5px;
        line-height: 16px;
        border-radius: 20px;
    }
    .carousel .thumb-content .btn:hover, .carousel .thumb-content .btn:focus {
        color: #fff;
        background: #007475;
        box-shadow: none;
    }
    .carousel .thumb-content .btn i {
        font-size: 14px;
        font-weight: bold;
        margin-left: 5px;
    }
    .carousel .carousel-control {
        height: 44px;
        width: 40px;
        background: #007475;
        margin: auto 0;
        border-radius: 4px;
        opacity: 0.8;
    }
    .carousel .carousel-control:hover {
        background: #007475;
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
        color: #fff;
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
    .carousel .carousel-indicators {
        bottom: -50px;
    }
    .carousel-indicators li, .carousel-indicators li.active {
        width: 10px;
        height: 10px;
        margin: 4px;
        border-radius: 50%;
        border: none;
    }
    .carousel-indicators li {
        background: rgba(0, 0, 0, 0.2);
    }
    .carousel-indicators li.active {
        background: rgba(0, 0, 0, 0.6);
    }
    .carousel .wish-icon {
        position: absolute;
        right: 10px;
        top: 10px;
        z-index: 99;
        cursor: pointer;
        font-size: 16px;
        color: #abb0b8;
    }
    .carousel .wish-icon  {
        color: #ff6161;
    }
    .star-rating li {
        padding: 0;
    }

</style>

<script type="text/javascript">
    $(document).ready(function(){
        $(".wish-icon i").click(function(){
            $(this).toggleClass("fa-heart fa-heart-o");
        });
    });
</script>

    <div class="container hidden-xs">
        <div class="row">
            <div class="col-md-12">
                <h2>Compre Também</h2>
                <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="0">
                    <ol class="carousel-indicators">
                        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#myCarousel" data-slide-to="1"></li>
                        <li data-target="#myCarousel" data-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner">


                        <div class="item carousel-item active" >
                            <div class="row">
                                <div class="col-sm-4">


                                    <div class="thumb-wrapper">
                                        <span class="wish-icon"><i class="fa fa-heart-o"></i></span>
                                        <div class="img-box">


                                        </div>
                                                <div class="thumb-content">
                                            <h4> </h4>
                                            <div class="star-rating">
                                                <div class="row">
                                                    <span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star <?= (($produto->id)%5!=1) ? "checked" : ""?>"></span>
                                                </div>
                                            </div>
                                            <p class="item-price"><strike></span></strike> <b>
                                                    </del></b></p>
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
                                            <h4>Aditivo radiador</h4>
                                            <p class="item-price"><strike>R$289.00</strike> <span>R$269.00</span></p>
                                            <div class="star-rating">
                                                <div class="row">
                                                    <span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star <?= (($produto->id)%5!=1) ? "checked" : ""?>"></span>
                                                </div>
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
                                            <h4>Oleo</h4>
                                            <p class="item-price"><strike>R$369.00</strike> <span>R$349.00</span></p>
                                            <div class="star-rating">
                                                <div class="row">
                                                    <span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star <?= (($produto->id)%5!=1) ? "checked" : ""?>"></span>
                                                </div>
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
                                            <h4>Faixa lateral</h4>
                                            <p class="item-price"><strike>R$315.00</strike> <span>R$250.00</span></p>
                                            <div class="star-rating">
                                                <div class="row">
                                                    <span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star <?= (($produto->id)%5!=1) ? "checked" : ""?>"></span>
                                                </div>
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
                                            <h4>Caixa lampada</h4>
                                            <p class="item-price"><strike>R$450.00</strike> <span>R$418.00</span></p>
                                            <div class="star-rating">
                                                <div class="row">
                                                    <span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star checked"></span><span class="fa fa-star <?= (($produto->id)%5!=1) ? "checked" : ""?>"></span>
                                                </div>
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
    </div><br>
 carousel  CAROUSEL PRODUTOS RELACIONADOS -->






    <div id="tabconteudo" class="tab-product-view panel-body" style="background-color: white">
        <ul id="tabs" class="nav nav-tabs nav-tabs-view" data-tabs="tabs">
            <li class="active"><a id="aplicacao" href="#compatibilidade" data-toggle="tab"style="color: black">Detalhes do Produto</a></li>
            <?= ""//'<li><a id="loja" href="#lojas" data-toggle="tab">Outras Lojas</a></li>'?>
            <li><a href="#sobre" data-toggle="tab" style="color: black">Sobre a Loja</a></li>
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

</div><br>

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


<!-- GRAFICO -
<div id="graficopreco" class="tab-product-view panel-body" style="background-color: white">


    <div class="col-sm-10">
        <h1> Grafico Preço </h1>
    </div>
    <div class="col-sm-10">
        <h1> Grafico Preço </h1>
    </div>
    <div class="col-sm-10">
        <h1> Grafico Preço </h1>
    </div>
    <div class="col-sm-10">
        <h1> Grafico Preço </h1>
    </div>
    <div class="col-sm-10">
        <h1> Grafico Preço </h1>
    </div>



</div>
<!-- GRAFICO -->


<div class="container">
    <?php
    $ativo = true;
    if (isset(Yii::$app->session["carrinho"][$minValue->produtoFilial->id])) {
        $ativo = false;
    }
    ?>
    <?php if ($minValue->produtoFilial->estoque > 0) { ?>
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
    <?php } else { ?>
        <div class="fab col-sm-1">
            <span class="h1 text-success"></span>
            <div class="row">
                <?php
                $href = Url::base() . '/orcamento?peca=' . $produto->id . '&filial=' . $minValue->produtoFilial->filial->id;
                echo Html::a('Solicite um <br> Orçamento!', $href, ['class' => 'esquerda btn btn-success col-lg-6 col-lg-offset-3', 'target' => '_blank']); ?>
            </div>
        </div>
    <?php } ?>
</div>


<style>
    .esquerda{
        margin-right: 250px;
    }

</style>
<!-- BOTAO MOBILE -->







