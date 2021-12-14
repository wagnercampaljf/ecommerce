<?php

/**
 * @var $produto common\models\Produto
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\ValorProdutoMenorMaior;
use common\models\ProdutoFilial;
use common\models\MarcaProduto;

$juridica = Yii::$app->params['isJuridica']();
$dt_ref = date('Y-m-d H:i:s');

$valor_produto_filial   = false;
$valores[]              = 999999;
$produto_filiais        = ProdutoFilial::find()->andWhere(['=', 'produto_id', $produto->id])
    ->andWhere(['>', 'quantidade', 0])
    ->all();

$ValorProdutoMenorMaior = '';

if ($produto_filiais) {
    foreach ($produto_filiais as $k => $produto_filial) {

        $ValorProdutoMenorMaior   = ValorProdutoMenorMaior::find()->andWhere(['=', 'produto_id', $produto_filial->produto_id])
            ->one();
        if ($ValorProdutoMenorMaior) {
            $valores[]  = $ValorProdutoMenorMaior->menor_valor;
        }
    }
}

$valor_texto = str_replace(',', ',<small>', Yii::$app->formatter->asCurrency(min($valores))) . '</small>';

$this->registerJs("$('[data-toggle=\"tooltip\"]').tooltip({'placement': 'top'});");
$this->registerJs("$('.pagination').click(function(){
$('html, body').animate({scrollTop: 0},'slow');
});");


?>

<style>
    .panel-body {
        padding: 1px !important;
    }

    .toggle {
        padding: 10px !important;
    }

    .marca2 {
        color: white;
    }
</style>



<!-- FOLLOW -->
<a class="Follow" href="https://codepen.io/ZaynAlaoudi/" target="blank_"></a>

<!-- HTML Setup -->

<div class="col-12 col-sm-6 col-md-6 col-lg-4">
    <div class="card h-100 mb-4" style="background-color: white; border-radius: 10px" ;>
        <div class="card-header produto-search-img text-center  margin-bottom-10 "">
            <a href=" <?= $produto->getUrl() ?>">
            <?php
            $alt = $produto->getLabel();
            echo $produto->getImage([
                'class' => "text-center",
                'height' => '115',
                'width' => '115',
                'alt' => $alt,
                'title' => $alt,
                'itemprop' => 'image'
            ]) ?>
            </a>
        </div>





        <div class="card-body text-left">


            <span class="font-lead-base font-weight-bold text-muted">

                <!-- AJUSTE AQUI PRODUTO SME VALOR -->
                <div class="card-text" style="text-overflow: ellipsis; width: 100%; padding-left: 15px; height: 64px; overflow: hidden !important">
                    <?php
                    if (!$ValorProdutoMenorMaior  ) {
                        echo ' <a href=' . $produto->getUrl() . ' >
                        <span>' . $produto->getLabel() . ' </span>
                    </a>';
                        $href = Url::base() . '/orcamento?peca=' . $produto->id;
                    } elseif ($ValorProdutoMenorMaior->menor_valor == null ) {
                        echo ' <span href=' . $produto->getUrl() . ' >
                        <span>' . $produto->getLabel() . ' </span>
                    </a>';
                        $href = Url::base() . '/orcamento?peca=' . $produto->id;
                    }

                    else {

                        echo '  <a href=' . $produto->getUrl() . ' >
                        <span>' . $produto->getLabel() . ' </span>
                    </a>';
                    } ?>
                </div>
                <?php
                if (!empty($produto->marca_produto_id)) {
                    echo "Marca: ";
                    $marca_produto = MarcaProduto::find()->andWhere(['=', 'id', $produto->marca_produto_id])->one();
                    echo $marca_produto->nome;
                } else {
                    echo "Marca: ";
                }
                if ($produto->codigo_search != $produto->codigo_global && !empty($produto->codigo_search)) {
                ?>

                <?php
                } elseif (substr($produto->aplicacao_search, 0, 4) != substr($produto->aplicacao, 0, 4) && !empty($produto->aplicacao_search)) {
                ?>

                <?php
                } elseif ($produto->similar_search != $produto->codigo_similar && !empty($produto->similar_search)) {
                ?>
                <?php
                } elseif ($produto->complementar_search != $produto->aplicacao_complementar && !empty($produto->complementar_search)) {
                ?>

                <?php
                } else {
                ?>
                <?php } ?>

            </span>
            <div class="promotion-promo" style="text-align: center; padding-left: 15px">
                <?php //if (is_null($maxValue) && is_null($minValue)) {
                if (!$ValorProdutoMenorMaior || $ValorProdutoMenorMaior->menor_valor == 0) {
                    //if (is_null($minValue)) {
                    $href = Url::base() . '/orcamento?peca=' . $produto->id;
                    echo "<div style='height: 107px'>";
                    echo "<br>Preço Sob Consulta!<br>";
                    echo Html::a("Solicitar um <br/>  Orçamento!", $href, ['class' => 'btn btn-success btn-lg', 'id' => 'avise', 'rel' => 'nofollow']);
                    echo "</div>";
                } else {
                    echo '
                        <span itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer"
                              class="price lead">
                           <small><small>a partir de</small></small><br/>
                           <span itemprop="lowPrice" style="font-weight: bold">' . /*$minValue->menor_valor*/ $valor_texto  /*$minValue->labelTitulo($juridica)*/ . '</span>
                            <br/>' .
                        //<small> <small>Em <b>' . count($filiais) . '</b> Loja(s)</small> </small>
                        '<meta itemprop="priceCurrency" content="BRL"/>
                        </span>
                        <br>
                <div class="produto-search-button text-center col-xs-12 col-sm-12 col-md-12 col-lg-12 clearfix">
                    <a href="' . $produto->getUrl() . '" class="btn btn-danger">
                        <i class="no-color fa fa-shopping-cart "></i>
                        Comprar
                    </a>
                </div>';
                } ?></div>
        </div>

    </div><br>
</div>




<style>
    @import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800%7CRaleway:100,200,300,400,600,700,800,900%7CMontserrat:100,200,300,400,500,600,700,800,900");


    .card:hover {
        box-shadow: 0px 0px 20px -3px rgba(143, 143, 143, 0.75);
    }


    section {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }

    h6 {
        color: #990000;
    }

    .card-header {
        border-radius: 0;
    }

    .card-header,
    .card-footer {
        -webkit-transition: .5s ease;
        transition: .5s ease;
    }

    .card:hover {
        border-color: #ffc107;
        -webkit-transform: perspective(0em) rotateX(0deg) rotateY(0deg) rotateZ(0deg);
        transform: perspective(0em) rotateX(0deg) rotateY(0deg) rotateZ(0deg);
    }

    .card:hover .card-header,
    .card:hover .card-footer {
        color: #ba8b00;
        border-color: #ffc107;
        background-color: rgba(0, 254, 255, 0);
    }

    .card:hover .promotion-promo {
        -webkit-transform: scale(1.175) translateY(2.5px);
        transform: scale(1.175) translateY(2.5px);
        -webkit-transform-origin: left center;
        transform-origin: left center;
    }

    .card:hover .promotion-price {
        -webkit-transform: translate(2.5px, 17.5px) scale(1.15);
        transform: translate(2.5px, 17.5px) scale(1.15);
        -webkit-transform-origin: center right;
        transform-origin: center right;
    }

    .card-body {
        position: relative;
        z-index: 0;
        overflow: hidden;
        padding-top: 2rem;
        padding-bottom: 2rem;
    }

    .card .btn {
        font-weight: bold;
        text-transform: uppercase;
    }

    .promotion-promo {
        font-weight: 700;
        font-size: 1.15rem;
        color: #000000;
        font-family: "Montserrat", sans-serif;
        -webkit-transition: .25s linear;
        transition: .25s linear;
    }

    .card-animate .card-body:before {
        -webkit-transition: .5s ease;
        transition: .5s ease;
        counter-increment: section;
        content: ""counter(section) "";
        display: block;
        font-size: 15rem;
        font-weight: 900;
        position: absolute;
        bottom: 5rem;
        line-height: 0;
        left: -.85rem;
        padding: 0;
        margin: 0;
        color: rgba(0, 0, 0, 0.1);
        z-index: 0;
    }

    .card-animate .card:hover .card-body:before {
        -webkit-transform: translate(10px, -10px);
        transform: translate(10px, -10px);
    }


    .card-animate .card-title {
        font-weight: 900;
        text-transform: uppercase;
    }
</style>