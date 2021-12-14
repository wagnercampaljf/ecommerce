<?php

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

<div itemscope itemtype="http://schema.org/Product" class="site-product">
    <div class="panel panel-body">
        <span itemprop="name " style="word-wrap: break-word" class="h3"><b><?= $produto->nome." <font color='#FFFFFF'>(".$produto->codigo_global.")</font>"//$produto->label ?> </b></span><br><br>

        <div class="main-product-view">
        	<!-- <div class="img-product-view text-center  col-lg-2 col-md-2 col-sm-2 col-xs-12"> -->

            <!-- CAROUSEL -->
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
                            if ($width < 500 && $height < 500){
                                $options_imagem = ['id'=>'zoom_0'.$k, 'width' => '100%', 'height' => '100%','align' => 'center', 'alt' => $produto->getLabel(), 'title' => $produto->getLabel(), 'itemprop' => 'image'];
                                echo Html::img($imagem, $options_imagem);
                            } else{
                                $options_imagem_zoom = ['id'=>'zoom_0'.$k, 'data-zoom-image' => $imagem, 'width' => '100%', 'height' => '100%','align' => 'center', 'alt' => $produto->getLabel(), 'title' => $produto->getLabel(), 'itemprop' => 'image'];
                                echo Html::img($imagem, $options_imagem_zoom);

                                // Zoom//

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

            <!-- Cronometro -->
            <h6><i class="fa fa-lock fa-lg" aria-hidden="true"></i> Compra 100% segura</h6>

            <div class=" col-lg-4 col-md-2 col-sm-4 col-sx-12 text-center hidden-xs" style="background-color:  #040100">
                <span class="text-center wrapper" style="color: #ffffff; font-size: 15px;padding: 6px; border-bottom:solid 2px #ffffff"><img src="<?= Url::to('@assets/'); ?>img/relogio.gif" style="width: 32px; height: 32px; " border="0" alt="relogio-imagem-animada-0148"><h4> &nbsp BLACK NOVEMBER  </h4> </span><br>
                <span class="contagem"><span class="text-center" style="color: #ffffff;"> PREÇO COM <small style="font-size: 16px;color: #ffffff;font-weight: bold;">10% &nbsp</small></span>
                </span>
                <span id="demo"></span>


            </div><br>


            <!-- Cronometro -->

            <script>

                var countDownDate = new Date("November 26, 2019 12:00:15");
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
                        countDownDate.setHours(countDownDate.getHours()+12);
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
            <!-- Cronometro -->

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


            <div class=" col-lg-4 col-md-6 col-sm-4 col-sx-12" style="background-color: #d7d7d7;"><br>
            	<!-- <div class="container header-product-view">-->

                <!-- Quebra de linha 09/10/209-->

                <!-- Botão para acionar modal -->


                <!-- Botão para acionar modal -->
                <span style="text-decoration: underline" data-toggle="modal" data-target="#modalExemplo">
                    <a href="#" style="color: #4A4E5A"> Veja a política de troca</a> <i class="fa fa-check-square-o" aria-hidden="true"></i>
                </span>


                <!-- Modal COMPRA GARANTIDA -->
                <div class="modal fade" id="modalExemplo" aria-hidden="true" style="z-index: 999999">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <img class="logo" alt="Peca Agora" title="Peça Agora"
                                     src="<?= Url::to('@assets/'); ?>img/pecaagora_azul.png">
                                <h2 class="modal-title" id="exampleModalLabel">Compra Garantida </h2>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <!-- politica de troca -->
                            <div class="modal-body">
                                <p>O Compra Garantida é para dar mais tranquilidade aos compradores no Peça Agora. Receba o seu produto ou devolvemos o seu dinheiro !</p>



                                <p>Problemas com a Compra?</p>

                                <p>Se o seu produto veio defeituoso. Entre em contato com o SAC através do e-mail sac@pecaagora.com que devolvemos o dinheiro da sua compra e do frete. Afinal, sua satisfação é o mais importante para nós.</p>



                                <p>Prazo para Reclamação?

                                <p>Você tem o prazo de 7 dias corridos, a partir da data que receber o produto, para iniciar uma reclamação. Se não recebeu o produto, você tem até 21 dias para reclamar. Encerrado este prazo, não há como iniciar a reclamação e as informações e solicitações referente à compra deverão ser redirecionada à loja onde a compra foi realizada.</p>



                                <p>O Dinheiro é devolvido?

                                <p>Sim, devolvemos o dinheiro da sua compra e do frete desde que estejam no prazo hábil para reclamação.</p>
                            </div>
                            <!-- politica de troca -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div><br>
                <!-- Modal -->




                <div class="panel panel-primary price lead text-center">
                    <?php $depor=0.9?>

                    <!-- preço com corte  -->
                    <span style=" font-size: 16px">De: </span> <span style="text-decoration: line-through; font-size: 16px">R$<?= number_format(($minValue->getValorFinal($juridica)/ $depor), 2, ',', '') ?></span> <span style=" font-size: 16px"> por</span>

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


                                <a href="" aria-disabled="true"  onclick="addProdutoCarrinho('<?= $minValue->produtoFilial->id ?>', this);return false;"
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
                        <small>Vendido e entregue por <b><?= $minValue->produtoFilial->filial->nome ?></b></small>
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

                <!-- divide de 6x -->

                <!-- divide de 6x -->
                <div><i class="fa fa-credit-card" aria-hidden="true"></i> <?= $minValue->labelTitulo($juridica) ?> em até 6x de <?= number_format(($minValue->getValorFinal($juridica)/ $divide), 2, ',', '') ?>   c/ juros no cartão de crédito.
                    <span style="text-decoration: underline" data-toggle="modal" data-target="#ExemploModalCentralizado">
                    <a href="#" style="color: #4A4E5A"> Mais formas de pagamentos</a></span>
                    <!-- Modal -->
                    <div class="modal fade" id="ExemploModalCentralizado" tabindex="-1" style="z-index: 999999" role="dialog" aria-labelledby="TituloModalCentralizado" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <img class="logo" alt="Peca Agora" title="Peça Agora"
                                     src="<?= Url::to('@assets/'); ?>img/pecaagora_azul.png">
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
                <!-- divide de 6x -->

                <!-- Botão para acionar modal -->


                
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
                    <i class="fa fa-plus-circle" aria-hidden="true"> Informações sobre o produto</i>

                </a>
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
                                <font color="white"><strong style="color: white">Fabricante: </strong><?= $produto->fabricante->nome ?></font>
                            </div>
                        <?php } ?>
                        <?php if (!empty($produto->codigo_global)) { ?>
                            <div class="product-detail">
                                <font color="white"><strong style="color: white">Código Global: </strong><?= $produto->codigo_global ?></font>
                            </div>
                        <?php } ?>
                        <?php if (!empty($produto->codigo_fabricante)) { ?>
                            <div class="product-detail">
                                <font color="white"><strong style="color: white">Código Fabricante: </strong><?= $produto->codigo_fabricante ?></font>

                            </div>
                        <?php } ?>
                        <div class="product-detail">
                            <small style="color: #ffffff">Vendido e entregue por <b><?= $minValue->produtoFilial->filial->nome ?></b></small>

                        </div>
                        <?php if (!empty($produto->codigo_barras)) { ?>
                            <div class="product-detail">
                                <font color="white"><strong style="color: white">Código de barras: </strong><?= $produto->codigo_barras ?></font>
                            </div>
                        <?php } ?>

                    </div>
                </div>

            </div>

        </div>
    </div>

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

<!-- BOTAO MOBILE -->

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

<!-- BOTAO MOBILE -->







