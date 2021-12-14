<?php
use yii\helpers\Html;
use yii\helpers\Url;

$juridica = false;
if (!Yii::$app->user->isGuest) {
    $juridica = Yii::$app->user->getIdentity()->empresa->juridica;
}
?>
    <?php
    foreach ($filiais as $filial) {
        $ativo = true;
        if (isset(Yii::$app->session["carrinho"][$filial->id])) {
            $ativo = false;
        }
        ?>
        <div class="panel panel-default panel-lojas-product-view">
            <div class="panel-body">
                <div class="panel-lojas-product-view-img text-center col-lg-2 col-md-2 col-sm-3 col-xs-12 ">
                    <?= $filial->filial->lojista->getImage([
                        'width' => '100%',
                        'alt' => $filial->filial->nome,
                        'title' => $filial->filial->nome
                    ]) ?>
                </div>
                <div class="product-view-div panel-lojas-product-view-preco price lead col-lg-2 col-md-2 col-sm-9 col-xs-12">
                    <?php if ($filial->estoque > 0) { ?>
                        <small>Preço:</small>
                        <br/>
                        <?= $filial->getValorProdutoFilials()->ativo()->one() ? $filial->getValorProdutoFilials()->ativo()->one()->labelTitulo($juridica) : null; ?>
                    <?php } else { ?>
                        Preço Sob Consulta!
                    <?php } ?>
                </div>
                <div class="filial_<?= $filial->filial->id ?> product-view-div panel-lojas-product-view-datails col-lg-4 col-md-4 col-sm-6 col-xs-12 produto-filial"
                     id="">
                    <?php
                    if (Yii::$app->params['getCepComprador']()) {
                        $this->registerJs('
                            $(document).ready(function () {
                                $("#calcula-frete").trigger("click");
                            });
                        ');
                    } else {
                        $this->registerJs('
                            $(document).ready(function () {
                                $(".scrollTo").click(function(){
                                    var alvo = $(this).attr("href").split("#").pop();
                                    $("html, body").animate({scrollTop: $("#"+alvo).offset().top - 150 }, 800);
                                    $("#"+alvo).focus();
                                    return false;
                                });
                            });
                        ');
                        echo \yii\helpers\Html::a('Insira seu CEP', '#seu_cep', ['class' => 'scrollTo']);
                    }
                    ?>
                </div>
                <div class="product-view-div panel-lojas-product-view-frete col-lg-2 col-md-2 col-sm-3 col-xs-12">
                    <small>Localização:</small>
                    <br/>
                    <i class="fa fa-map-marker"></i> <?= $filial->filial->enderecoFilial->cidade ?>

                    <!--                    --><?php //file_get_contents('http://maps.googleapis.com/maps/api/distancematrix/json?origins=Juiz%20de%20Fora,%20(MG)%20%2036025320&destinations=S%C3%A3o%20Paulo&language=pt_br&sensor=false');?>
                </div>
                <div class="product-view-div text-center panel-lojas-product-view-botao col-lg-2 col-md-2 col-sm-3 col-xs-12">
                    <?php if ($filial->estoque > 0) { ?>
                        <a href="" aria-disabled="true" onclick="addProdutoCarrinho('<?= $filial->id ?>', this);return false;"
                           class="btn col-xs-12  btn-primary <?= ($ativo ?: 'disabled') ?>"><i
                                class="no-color fa fa-shopping-cart"></i> <?= ($ativo ? "Adicionar" : 'Já Adicionado') ?>
                        </a>
                    <?php } else {
                        $href = Url::base() . '/orcamento?peca=' . $produto->id . '&filial=' . $filial->filial->id;
                        echo Html::a('Solicite um <br> Orçamento!', $href, ['class' => 'btn btn-success col-xs-12', 'target' => '_blank']);
                    } ?>
                </div>
            </div>
        </div>
    <?php } ?>
