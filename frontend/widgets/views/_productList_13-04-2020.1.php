<?php
/**
 * @var $produto common\models\Produto
 */

use common\models\CaracteristicaFilial;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\ValorProdutoMenorMaior;

$juridica = Yii::$app->params['isJuridica']();
//die;
//$dt_ref = date('Y-m-d H:i:s');
//$maxValue = \common\models\ValorProdutoFilial::find()->ativo()->maiorValorProduto($produto->id, $juridica)->one();
//$minValue = \common\models\ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id, $juridica)->one();
$maxValue = ValorProdutoMenorMaior::findOne(['produto_id'=>$produto->id]);//->menor_valor;
$minValue = ValorProdutoMenorMaior::findOne(['produto_id'=>$produto->id]);//->menor_valor;

/*$filiais = $produto->getFiliaisProduto()->join('INNER JOIN', 'valor_produto_filial',
    'valor_produto_filial.produto_filial_id =produto_filial.id')->with(
    [
        'filial.lojista',
        'filial.enderecoFilial.cidade.estado'
    ]
)->andWhere('valor_produto_filial.dt_inicio <= \'' . $dt_ref . '\'')->andWhere('valor_produto_filial.dt_fim >=\'' . $dt_ref . '\' OR dt_fim IS NULL')->lojistaAtivo()->produtoDisponivel()->all();*/

//$caracteristicas = CaracteristicaFilial::find()->ativo()->byProduto($produto->id)->all();

//if (empty($produto->aplicacao_search)) {
//    $displayAplicacaoSearch = 'none';
//    $displayAplicacao = 'block';
//
//} else {
//    $displayAplicacaoSearch = 'block';
//    $displayAplicacao = 'none';
//    $this->registerJs("
//            // choose text for the show/hide link - can contain HTML (e.g. an image)
//            var showText='Ver Aplicação Completa';
//            var hideText='Ver Menos';
//
//            // initialise the visibility check
//            var is_visible = false;
//
//            // append show/hide links to the element directly preceding the element with a class of toggle
//            $(' .toggle').append($('<a>',{html:showText,href:'#',class:'toggleLink',style:'margin-left:10px'}));
//
//            // hide all of the elements with a class of 'toggle'
//            //$('.toggle').hide();
//
//            // capture clicks on the toggle links
//            $('.toggleLink').click(function() {
//
//                // switch visibility
//                is_visible = !is_visible;
//
//                // change the link depending on whether the element is shown or hidden
//                $(this).html( (!is_visible) ? showText : hideText);
//
//                // toggle the display - uncomment the next line for a basic accordion style
//                //$(' .toggle').hide();
//
//                //$('a .toggleLink').html(showText);
//                $(this).parent().children('div').toggle();
//
//                // return false so any link destination is not followed
//                return false;
//            });
//          ");
//};
$this->registerJs("$('[data-toggle=\"tooltip\"]').tooltip({'placement': 'top'});");
$this->registerJs("$('.pagination').click(function(){
$('html, body').animate({scrollTop: 0},'slow');
});");

?>
<div class="produto-div clearfix col-xs-12 col-sm-4 col-md-4 col-lg-3" xmlns="http://www.w3.org/1999/html">
    <div itemscope itemtype="http://schema.org/Product" class="panel panel-body produto-search col-xs-12 col-sm-12 col-md-12 col-lg-12">

        <div class="row imagemEcaracteristica">
            <!--<div class="caracteristicas">
                <?php
                /*echo "1234123412341234<br><br>";
                foreach ($caracteristicas as $caracteristica) {
                    echo Html::tag('div',
                        Html::tag('span',
                            Html::tag('i', '', [
                                    'class' => $caracteristica->caracteristica->badge,
                                    'style' => 'color: #fff'
                                ]
                            ) . ' ' . $caracteristica->caracteristica->nome,
                            [
                                'class' => $caracteristica->caracteristica->classe,
                                'title' => $caracteristica->observacao,
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top'
                            ]
                        ),
                        ['style' => 'margin-top:0px !important;cursor: default;']
                    );
                }
                echo "1234123412341234<br><br>";*/
                ?>
            </div>-->
            <div class="produto-search-img text-center margin-bottom-10 ">
                <a href="<?= $produto->getUrl() ?>">
                    <?php
                    $alt = $produto->getLabel();
                    echo $produto->getImage([
                        'class' => "text-center",
                        'width' => '156',
                        'alt' => $alt,
                        'title' => $alt,
                        'itemprop' => 'image'
                    ]) ?>
                </a>
            </div>
        </div>
        <style>
            .by-brand{
                color: #007576;
                margin: 0;
            }
        </style>

        <div itemprop="name" class="produto-search-title clearfix  title col-xs-12 col-sm-12 col-md-12 col-lg-12 toggle">
            <a href="<?= $produto->getUrl();
            if (is_null($maxValue) && is_null($minValue)) {
                    $href = Url::base() . '/orcamento?peca=' . $produto->id;
                } ?>"
                <span><?= $produto->getLabel() ?></span>
            <?php
            if($produto->marca_produto_id != null){
                ?>
                <span style="display: block; margin: -20px 0 10px 0">Marca.: <a class="by-brand" href="#"><?= $produto->marcaProduto->nome ?></a></span>
                <?php
            }
            ?>
            </a>
        </div>

        <?php
        if ($produto->codigo_search != $produto->codigo_global && !empty($produto->codigo_search)) {
            ?>
            <div class="toggle container clearfix margin-bottom-10 col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="aplicacaosearch clearfix">
                <span>
                    <?= $produto->codigo_search; ?>
                </span>
                </div>
            </div>
            <?php
        } elseif (substr($produto->aplicacao_search, 0, 4) != substr($produto->aplicacao, 0, 4) && !empty($produto->aplicacao_search)) {
            ?>
            <div class="toggle container clearfix margin-bottom-10 col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="aplicacaosearch clearfix">
                <span>
                    <?= $produto->aplicacao_search; ?>
                </span>
                </div>
            </div>
            <?php
        } elseif ($produto->similar_search != $produto->codigo_similar && !empty($produto->similar_search)) {
            ?>
            <div class="toggle container clearfix margin-bottom-10 col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="aplicacaosearch clearfix">
                <span>
                    <?= $produto->similar_search; ?>
                </span>
                </div>
            </div>
            <?php
        } elseif ($produto->complementar_search != $produto->aplicacao_complementar && !empty($produto->complementar_search)) {
            ?>
            <div class="toggle container clearfix margin-bottom-10 col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="aplicacaosearch clearfix">
                <span>
                    <?= $produto->complementar_search; ?>
                </span>
                </div>
            </div>
            <?php
        }else {
            ?>
            <div class="toggle container clearfix margin-bottom-10 col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="aplicacaosearch clearfix">
                <span>
               		<?= "" ?>
                </span>
                </div>
            </div>
        <?php } ?>
        <div class="produto-search-details-wrap clearfix preco-busca">
            <div class="produto-search-details text-center col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?php if (is_null($maxValue) && is_null($minValue)) {
                    $href = Url::base() . '/orcamento?peca=' . $produto->id;
                    echo "<div>";
                    echo "<br>Preço Sob Consulta!<br>";
                    echo Html::a("Solicitar um <br/> Orçamento!", $href, ['class' => 'btn btn-success btn-lg', 'id' => 'avise', 'rel' => 'nofollow']);
                    echo "</div>";
                } else {
                    echo '
                        <span itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer"
                              class="price lead">
                           <small><small>a partir de</small></small><br/>
                           <span itemprop="lowPrice">' . $minValue->menor_valor /*$minValue->labelTitulo($juridica)*/ . '</span>
                            <br/>'.
                           //<small><small>Em <b>' . count($filiais) . '</b> Loja(s)</small></small>
                            '<meta itemprop="priceCurrency" content="BRL"/>
                        </span>
                        <br>

                <div class="produto-search-button text-center col-xs-12 col-sm-12 col-md-12 col-lg-12 clearfix">
                    <a href="' . $produto->getUrl() . '" class="btn btn-danger">
                        <i class="no-color fa fa-shopping-cart "></i>
                        Comprar
                    </a>
                </div>';
                } ?>
            </div>

        </div>

    </div>
</div>

<?php
?>
