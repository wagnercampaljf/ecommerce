<?php
/**
 * @var $formPagamento common\models\FormPagamento
 * @var $filiais common\models\ProdutoFilial[]
 * @var $pedido common\models\Pedido
 */

use common\models\Filial;
use frontend\assets\AppAsset;
use kartik\date\DatePicker;
use vendor\iomageste\Moip\Moip;
use yii\debug\components\search\matchers\Base;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = 'Checkout (' . count(Yii::$app->session['carrinho']) . ')';
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
];
$this->registerJsFile(
    Url::to(['frontend/web/js/checkout.js']),
    ['depends' => [frontend\assets\AppAsset::className()]]
);

$juridica = Yii::$app->params['isJuridica']();
$radio_frete = Yii::$app->request->post('radio_frete');

echo "<script> fbq('track', 'InitiateCheckout');</script>";

?>
<?php $form = ActiveForm::begin(['id' => 'checkout-form']); ?>
<?php
echo $form->errorSummary($pedido);
$total = 0;
foreach ($filiais as $filial_id => $produtos):
    $filial = Filial::find()->andWhere(['id' => $filial_id])->one();
    ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?= $filial->nome ?></h3>
        </div>
        <div class="panel-body">
            <div class="col-md-8">
                <div class="col-md-12">
                    <?php
                    $valorTotal = 0;
                    foreach ($produtos as $produto):
                        $valor_prod = $produto->getValorProdutoFilials()->ativo()->one()->getValorFinal($juridica);
                        ?>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="text-center col-md-2">
                                    <?= $produto->produto->getImage([
                                        'width' => '100%'
                                    ]) ?>
                                </div>
                                <div class="col-md-6 text-left">
                                    <div class="container">
                                        <h4 class="">
                                            <?= $produto->produto->getLabel() ?>
                                        </h4>
                                        <div class="col-md-12">
                                            <p class="text-left">
                                                <?= Yii::$app->formatter->asCurrency($valor_prod) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <p class="h4 text-left">
                                        <small>Quantidade:</small> <?= Yii::$app->session['carrinho'][$produto->id]; ?>
                                    </p>
                                    <p class="h4 text-left">
                                        <small>Total:
                                        </small> <?= Yii::$app->formatter->asCurrency($valor_prod * Yii::$app->session['carrinho'][$produto->id]) ?>
                                    </p>
                                </div>
                                <?= Html::activeHiddenInput($pedido,
                                    "[$filial_id][pedidoProdutoFilials]" . '[' . $produto->id . ']valor',
                                    ['value' => $valor_prod]) ?>
                                <?= Html::activeHiddenInput($pedido,
                                    "[$filial_id][pedidoProdutoFilials]" . '[' . $produto->id . ']quantidade',
                                    ['value' => Yii::$app->session['carrinho'][$produto->id]]) ?>

                            </div>
                        </div>
                        <?php
                        $valorTotal += $valor_prod * Yii::$app->session['carrinho'][$produto->id];
                    endforeach; ?>
                </div>
                <div class="col-md-12">
                    <div class=" panel panel-default">
                        <div class="panel-body">
                            <h4>Este produto será entregue neste endereço:</h4>
                            <hr>
                            <div>
                                <?php
//                                var_dump($address->estado);
                                if ($address->cidade == null) {
                                    echo 'Favor inserir um endereço em sua conta';
                                    echo "<br>";
                                } else {
                                    $slash = (empty($address->complemento) ? ' ' : '/');
                                    echo $address->logradouro . ' - ' . $address->numero . " " . $slash . " " . $address->complemento;
                                    echo "<br>";
                                    echo $address->bairro;
                                    echo "<br>";
                                    echo $address->cidade;
                                    echo "<br>";
                                    echo Yii::$app->formatter->asCEP($address->cep);
                                    echo "<br>";
                                    echo "<br>";
                                }
                                ?>
                                <a href="<?= Url::to("minhaconta/update-address?from=checkout") ?>" class="btn btn-primary">Alterar
                                    Endereço</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="container container-frete">
                    <div id="resultado-frete<?= $filial_id ?>" class="resultado-frete"
                         data-filial-id="<?= $filial_id ?>"
                         data-cep="<?= Yii::$app->params['getCepComprador'](true) ?>">
                        <p class="text-center h4">
                            <i class="fa fa-spinner fa-spin"></i>
                            Calculando o frete...
                        </p>
                    </div>
                </div>

            </div>

        </div>
        <div class="panel-footer">
            <p class="text-success text-right h3"> Total:
                <span class="grow total-filial" id="valorTotal_<?= $filial_id ?>"
                      data-valor-total="<?= $valorTotal ?>"
                      data-filial-id="<?= $filial_id ?>">
                    <?= Yii::$app->formatter->asCurrency($valorTotal) ?>
                </span>
                <?= Html::activeHiddenInput(
                    $pedido,
                    "[$filial_id]" . 'valor_total',
                    ['value' => $valorTotal, 'class' => 'pedido_valor_total']
                ); ?>
                <?= Html::activeHiddenInput($pedido, "[$filial_id]" . 'transportadora_id') ?>
                <?= Html::activeHiddenInput($pedido, "[$filial_id]" . 'data_prevista') ?>
            </p>
        </div>
    </div>
    <?php
    if (is_array($radio_frete) && $frete_selecionado = ArrayHelper::getValue($radio_frete, $filial_id)) {
        echo Html::hiddenInput(
            'frete_selecionado_' . $filial_id,
            $frete_selecionado,
            ['id' => 'frete_selecionado_' . $filial_id]
        );
    }
    $total += $valorTotal;
endforeach;
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6 text-left h3">
                Forma de Pagamento
            </div>
            <div class="col-md-6 text-right h3">
                Total:
                <span class="grow" id="valorTotal" data-valor-total="<?= $total ?>">
                <?= Yii::$app->formatter->asCurrency($total) ?>
            </span>
            </div>
        </div>
        <br>

        <div class="row text-center">
            <div class="col-lg-4 col-md-4 col-sm-8 col-xs-12 text-left">
                <button style="margin-left: 5px;" type="button" class="btn btn-success"
                        data-forma-pagamento="moip_creditCard" id="btn-moip">
                    <i style="color: #fff;" class="fa fa-credit-card"></i> Pagar com<b> Cartão</b>
                </button>
                <br>
                <br>
                <img width="100%" src="https://www.moip.com.br/imgs/banner_2_2.jpg">
            </div>
            <div class="col-lg-4 col-md-4 col-sm-8 col-xs-12 text-left">
                <button style="margin-left: 5px;" type="button" class="btn btn-success"
                        data-forma-pagamento="moip_boleto" id="btn-moip">
                    <i style="color: #fff;" class="fa fa-barcode"></i> <b>Boleto</b>
                </button>
                <br/>
                <br/>
                <img src="https://www.moip.com.br/imgs/banner_1_1.jpg">
            </div>
        </div>
        <br>

        <fieldset class="moip-fieldset <?= $formPagamento->method != 'moip_creditCard' ? 'hide' : '' ?>" id="moip_creditCard">
            <legend>Dados do Cartão:</legend>
            <p class="alert alert-warning">
                <i class="glyphicon glyphicon-exclamation-sign"></i>
                ATENÇÃO - Taxa Fixa por Loja: <?= Yii::$app->formatter->asCurrency(Moip::TAXMOIP) ?>.
            </p>

            <div class="panel-body">
                <?= Html::activeHiddenInput($formPagamento, 'method', ['id' => 'forma_pagamento']); ?>
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <?= $form->field($formPagamento, 'name')->textInput(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-6">
                        <?= Html::label('Data de Nascimento', 'FormPagamento[birthday]',
                            ['class' => 'control-label']) ?>
                        <?= MaskedInput::widget([
                            'name' => 'FormPagamento[birthDay]',
                            'mask' => '99/99/9999'
                        ]);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <?= $form->field($formPagamento, 'ccNumber')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                        <?= $form->field($formPagamento, 'cvcNumber')->textInput(['maxlength' => 4]) ?>
                    </div>
                </div>
		<div class="row hidden">
                    <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-6">
                        <?= Html::label('Data de Vencmento', 'FormPagamento[expirationDate]',
                            ['class' => 'control-label']) ?>
                        <?= MaskedInput::widget([
                            'name' => 'FormPagamento[expirationDate]',
                            'mask' => '99/99'
                        ]);
                        ?>
                    </div>
                </div>
		<div class="row">
                    <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-6">
                        <?= Html::label('Data de Vencimento', 'FormPagamento[expirationDate]',
                            ['class' => 'control-label']) ?>
                        <?= MaskedInput::widget([
                            'name' => 'FormPagamento[expirationDate]',
                            'mask' => '99/99'
                        ]);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                        <?= $form->field($formPagamento, 'installments')->dropDownList([],
                            ['class' => 'select2', 'id' => 'nrParcelas']) ?>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset class="moip-fieldset <?= $formPagamento->method != 'moip_boleto' ? 'hide' : '' ?>" id="moip_boleto">
            <legend>Boleto:</legend>

            <p class="alert alert-warning">
                <i class="glyphicon glyphicon-exclamation-sign"></i>
                ATENÇÃO - Taxa Fixa por Loja: <?= Yii::$app->formatter->asCurrency(Moip::TAXMOIP) ?>.
            </p>
        </fieldset>
    </div>
    <div style="padding-left: 25px;" class="panel-footer text-left <?= empty($formPagamento->method) ? 'hide' : '' ?>">
        <?= Html::submitButton('Comprar', ['class' => 'btn btn-success btn-lg']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
