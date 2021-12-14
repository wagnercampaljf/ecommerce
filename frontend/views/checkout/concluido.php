<?php
/**
 * @var $pedidos common\models\Pedido[]
 */

use vendor\iomageste\Moip\Moip;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Pedido Concluído!';
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
];

$method = Yii::$app->request->get('method');
$token = Yii::$app->request->get('token');


if ($method === 'moip_boleto' && $token) {
    $moip = new Moip(); //producao
//    $moip = new Moip(Moip::SANDBOX_ENDPOINT); //local
    $moip->payments()->get($token)->getLinks()->checkout->payBoleto->redirectHref;
    ?>
    <div class="panel panel-info">
        <div class="panel-heading">
            <i class="fa fa-barcode"></i> Boleto
        </div>
        <div class="panel-body">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 container">
                <p class="h3 bold">
                    Parabéns pela sua Compra!
                </p>
                <p class="h4 text-justify">
                    Para o pagamento de todos os pedidos abaixo, utilize este mesmo boleto.
                </p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 container">
                <p>
                    <?= Html::a(
                        '<i class="fa fa-print"></i> Imprimir Boleto',
                        $moip->payments()->get($token)->getLinks()->checkout->payBoleto->redirectHref."/print",
                        ['class' => 'btn btn-lg btn-info btn-block', 'target' => '_blank']
                    ) ?>
                </p>
            </div>
        </div>
    </div>
    <?php
}

foreach ($pedidos as $pedido) {
    $valorTotal = Yii::$app->formatter->asDecimal($pedido->valor_total + $pedido->valor_frete, 2);
    $imposto = Yii::$app->formatter->asDecimal($valorTotal * 0.04, 2);
    $frete = Yii::$app->formatter->asDecimal($pedido->valor_frete, 2);

    if (!YII_DEBUG) {
        $this->registerJs("
              ga('require', 'ecommerce');
            
              ga('ecommerce:addTransaction', {
              'id': '" . $pedido->id . "', 
              'affiliation': '" . $pedido->filial->nome . "', 
              'revenue': '" . $valorTotal . "', 
              'shipping': '" . $frete . "', 
              'tax': '" . $imposto . "', 
              'currency': 'BRL' 
              });");
        foreach ($pedido->pedidoProdutoFilials as $v) {
            $this->registerJs("ga('ecommerce:addItem', {
            'id': '" . $v->pedido_id . "',
            'name': '" . $v->produtoFilial->produto->nome . "',
            'sku': '" . $v->produtoFilial->produto->codigo_global . "',
            'category': '" . $v->produtoFilial->produto->subcategoria->nome . "',
            'price': '" . $v->valor . "',
            'quantity': '" . $v->quantidade . "',
            'currency': 'BRL'
          });");
        }


        $this->registerJs("ga('ecommerce:send');");
    }
    ?>
    <div class="panel panel-success">
        <div class="panel-heading">
            <i class="fa fa-check-circle-o fa-2x"></i>
            <span class="h3">Pedido realizado com sucesso!</span>
        </div>
        <div class="panel-body">
            <div class="col-md-8 container">
                <div class="col-md-12 h4">
                    Número do Pedido: <b><?= $pedido->id ?></b>
                </div>
                <br>

                <div class="col-md-12 h4">
                    Nome da Loja:<b><?= $pedido->filial->nome ?></b>
                </div>
                <br>

                <div class="col-md-12 h4 container">
                    <?= $pedido->getTextoPedidoConcluido() ?>
                </div>
                <a id="bannerEbit"><img src="https://www.ebitempresa.com.br/bitrate/banners/b1745365.gif"></a>
                <script type="text/javascript" id="getSelo"
                        src="https://imgs.ebit.com.br/ebitBR/selo-ebit/js/getSelo.js?74536">
                </script>
            </div>
            <div class="col-md-4 container">
                <div class="col-md-12 h4">
                    <ul class="list-group">
                        <li class="list-group-item">
                            Valor: <?= Yii::$app->formatter->asCurrency($pedido->valor_total) ?>
                        </li>
                        <li class="list-group-item">
                            Frete: <?= $pedido->valor_frete ? Yii::$app->formatter->asCurrency($pedido->valor_frete) : 'Grátis' ?>
                        </li>
                        <li class="list-group-item">
                            Valor
                            Total: <?= Yii::$app->formatter->asCurrency(($pedido->valor_total + $pedido->valor_frete)) ?>
                        </li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <?= Html::a(
                        'Mais Informações <i class="fa fa-plus-circle"></i>',
                        ['minhaconta/pedido', 'id' => $pedido->id],
                        ['class' => 'btn btn-default', 'target' => '_blank']
                    ); ?>
                </div>
                <br>
                <!--<a href="<?= Url::to("/portaldasoficinas") ?>" target="_blank">
                <span class="btn btn-primary btn-block">
                        <h4>Precisa de uma Oficina?</h4>Encontre Aqui!
                    </span>
                </a>-->
            </div>

        </div>



    </div>
    <?php
}

