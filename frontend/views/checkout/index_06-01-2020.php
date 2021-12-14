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

        <style>
            .dropdownlist{
                font-size: 13px;
                padding: 10px 8px 10px 14px;
                background: #fff;
                border: 1px solid #ccc;
                border-radius: 6px;
                position: relative;
            }

            .container .col1 {
                -webkit-perspective: 1000px;
                perspective: 1000px;
                -webkit-transform-style: preserve-3d;
                transform-style: preserve-3d;
            }
            .container .col1 .card {
                position: relative;
                max-width: 100%;
                height: 250px;
                margin-bottom: 85px;
                margin-right: 10px;
                border-radius: 17px;
                box-shadow: 0 5px 20px -5px rgba(0, 0, 0, 0.1);
                transition: all 1s;
                -webkit-transform-style: preserve-3d;
                transform-style: preserve-3d;
            }
            .container .col1 .card .front {
                position: absolute;
                background: var(--card-color);
                border-radius: 17px;
                padding: 50px;
                width: 100%;
                height: 100%;
                transform: translateZ(1px);
                -webkit-transform: translateZ(1px);
                transition: background 0.3s;
                z-index: 50;
                background-image: repeating-linear-gradient(45deg, rgba(131, 131, 131, 0.02) 1px, rgba(131, 131, 131, 0.03) 2px, rgba(131, 131, 131, 0.04) 3px, rgba(131, 131, 131, 0.05) 4px), -webkit-linear-gradient(-245deg, rgba(255, 255, 255, 0) 40%, rgba(255, 255, 255, 0.2) 70%, rgba(255, 255, 255, 0) 90%);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
            }
            .container .col1 .card .front .type {
                position: absolute;
                width: 75px;
                height: 45px;
                top: 20px;
                right: 20px;
            }
            .container .col1 .card .front .type img {
                width: 100%;
                float: right;
            }
            .container .col1 .card .front .card_number {
                position: absolute;
                font-size: 26px;
                font-weight: 500;
                letter-spacing: -1px;
                top: 110px;
                color: var(--text-color);
                word-spacing: 3px;
                transition: color 0.5s;
            }
            .container .col1 .card .front .date {
                position: absolute;
                bottom: 40px;
                right: 14px;
                width: 90px;
                height: 5px;
                color: var(--text-color);
                transition: color 0.5s;
            }
            .container .col1 .card .front .date .date_value {
                font-size: 12px;
                position: absolute;
                margin-left: 22px;
                margin-top: 12px;
                color: var(--text-color);
                font-weight: 500;
                transition: color 0.5s;
            }
            .container .col1 .card .front .date:after {
                content: 'MÊS / ANO';
                position: absolute;
                display: block;
                font-size: 7px;
                margin-left: 20px;
            }
            .container .col1 .card .front .date:before {
                content: 'Valido \a ';
                position: absolute;
                display: block;
                font-size: 8px;
                white-space: pre;
                margin-top: 8px;
            }
            .container .col1 .card .front .fullname {
                position: absolute;
                font-size: 20px;
                bottom: 40px;
                color: var(--text-color);
                transition: color 0.5s;
            }
            .container .col1 .card .back {
                position: absolute;
                width: 100%;
                border-radius: 17px;
                height: 100%;
                background: var(--card-color);
                -webkit-transform: rotateY(180deg);
                transform: rotateY(180deg);
            }
            .container .col1 .card .back .magnetic {
                position: absolute;
                width: 100%;
                height: 50px;
                background: rgba(0, 0, 0, 0.7);
                margin-top: 25px;
            }
            .container .col1 .card .back .bar {
                position: absolute;
                width: 80%;
                height: 37px;
                background: rgba(0, 0, 0, 0.7);
                left: 10px;
                margin-top: 100px;
            }
            .container .col1 .card .back .seccode {
                font-size: 13px;
                color: var(--text-color);
                font-weight: 500;
                position: absolute;
                top: 100px;
                right: 10px;
            }
            .container .col1 .card .back .chip {
                bottom: 45px;
                left: 10px;
            }
            .container .col1 .card .back .disclaimer {
                position: absolute;
                width: 65%;
                left: 80px;
                color: #000000;
                font-size: 8px;
                bottom: 55px;
            }
            .container .col2 input {
                display: block;
                width: 260px;
                height: 30px;
                padding-left: 10px;
                padding-top: 3px;
                padding-bottom: 3px;
                margin: 7px;
                font-size: 17px;
                border-radius: 20px;
                background: rgba(122, 122, 122, 0.67);
                border: none;
                transition: background 0.5s;
            }
            .container .col2 input:focus {
                outline-width: 0;
                background: rgba(31, 134, 252, 0.15);
                transition: background 0.5s;
            }
            .container .col2 label {
                padding-left: 8px;
                font-size: 15px;
                color: #000000;
            }
            .container .col2 .ccv {
                width: 20%;
            }

            .container .col2 .expire {
                width: 20%;
            }

            .container .col2 .buy {
                width: 260px;
                height: 50px;
                position: relative;
                display: block;
                margin: 20px auto;
                border-radius: 10px;
                border: none;
                background: #42C2DF;
                color: white;
                font-size: 20px;
                transition: background 0.4s;
                cursor: pointer;
            }
            .container .col2 .buy i {
                font-size: 20px;
            }
            .container .col2 .buy:hover {
                background: #3594A9;
                transition: background 0.4s;
            }

            .chip {
                position: absolute;
                width: 55px;
                height: 40px;
                background: #bbb;
                border-radius: 7px;
            }
            .chip:after {
                content: '';
                display: block;
                width: 35px;
                height: 25px;
                border-radius: 4px;
                position: absolute;
                top: 0;
                bottom: 0;
                margin: auto;
                background: #ddd;
            }

        </style>

        <script>
            $(function(){

                var cards = [{
                    nome: "mastercard",
                    colore: "#0061A8",
                    src: "https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png"
                }, {
                    nome: "visa",
                    colore: "#E2CB38",
                    src: "https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/2000px-Visa_Inc._logo.svg.png"
                }, {
                    nome: "dinersclub",
                    colore: "#888",
                    src: "http://www.worldsultimatetravels.com/wp-content/uploads/2016/07/Diners-Club-Logo-1920x512.png"
                }, {
                    nome: "americanExpress",
                    colore: "#000000",
                    src: "https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/American_Express_logo.svg/600px-American_Express_logo.svg.png"
                }, {
                    nome: "discover",
                    colore: "#86B8CF",
                    src: "https://lendedu.com/wp-content/uploads/2016/03/discover-it-for-students-credit-card.jpg"
                }, {
                    nome: "dankort",
                    colore: "#0061A8",
                    src: "https://upload.wikimedia.org/wikipedia/commons/5/51/Dankort_logo.png"
                }];

                var month = 0;
                var html = document.getElementsByTagName('html')[0];
                var number = "";

                var selected_card = -1;

                $(document).click(function(e){
                    if(!$(e.target).is(".ccv") || !$(e.target).closest(".ccv").length){
                        $(".card").css("transform", "rotatey(0deg)");
                        $(".seccode").css("color", "var(--text-color)");
                    }
                    if(!$(e.target).is(".expire") || !$(e.target).closest(".expire").length){
                        $(".date_value").css("color", "var(--text-color)");
                    }
                    if(!$(e.target).is(".number") || !$(e.target).closest(".number").length){
                        $(".card_number").css("color", "var(--text-color)");
                    }
                    if(!$(e.target).is(".inputname") || !$(e.target).closest(".inputname").length){
                        $(".fullname").css("color", "var(--text-color)");
                    }
                });


                //Card number input
                $(".number").keyup(function(event){
                    $(".card_number").text($(this).val());
                    number = $(this).val();

                    if(parseInt(number.substring(0, 2)) > 50 && parseInt(number.substring(0, 2)) < 56){
                        selected_card = 0;
                    }else if(parseInt(number.substring(0, 1)) == 4){
                        selected_card = 1;
                    }else if(parseInt(number.substring(0, 2)) == 36 || parseInt(number.substring(0, 2)) == 38 || parseInt(number.substring(0, 2)) == 39){
                        selected_card = 2;
                    }else if(parseInt(number.substring(0, 2)) == 34 || parseInt(number.substring(0, 2)) == 37){
                        selected_card = 3;
                    }else if(parseInt(number.substring(0, 2)) == 65){
                        selected_card = 4;
                    }else if(parseInt(number.substring(0, 4)) == 5019){
                        selected_card = 5;
                    }else{
                        selected_card = -1;
                    }

                    if(selected_card != -1){
                        html.setAttribute("style", "--card-color: " + cards[selected_card].colore);
                        $(".bankid").attr("src", cards[selected_card].src).show();
                    }else{
                        html.setAttribute("style", "--card-color: #cecece");
                        $(".bankid").attr("src", "").hide();
                    }

                    if($(".card_number").text().length === 0){
                        $(".card_number").html("&#x25CF;&#x25CF;&#x25CF;&#x25CF; &#x25CF;&#x25CF;&#x25CF;&#x25CF; &#x25CF;&#x25CF;&#x25CF;&#x25CF; &#x25CF;&#x25CF;&#x25CF;&#x25CF;");
                    }

                }).focus(function(){
                    $(".card_number").css("color", "white");
                }).on("keydown input", function(){

                    $(".card_number").text($(this).val());

                    if(event.key >= 0 && event.key <= 9){
                        if($(this).val().length === 4 || $(this).val().length === 9 || $(this).val().length === 14){
                            $(this).val($(this).val() +  " ");
                        }
                    }
                });

                //Name Input
                $(".inputname").keyup(function(){
                    $(".fullname").text($(this).val());
                    if($(".inputname").val().length === 0){
                        $(".fullname").text("Nome completo");
                    }
                    return event.charCode;
                }).focus(function(){
                    $(".fullname").css("color", "white");
                });

                //Security code Input
                $(".ccv").focus(function(){
                    $(".card").css("transform", "rotatey(180deg)");
                    $(".seccode").css("color", "white");
                }).keyup(function(){
                    $(".seccode").text($(this).val());
                    if($(this).val().length === 0){
                        $(".seccode").html("&#x25CF;&#x25CF;&#x25CF;");
                    }
                }).focusout(function() {
                    $(".card").css("transform", "rotatey(0deg)");
                    $(".seccode").css("color", "var(--text-color)");
                });


                 //Date expire input
                $(".expire").keypress(function(event){
                    if(event.charCode >= 48 && event.charCode <= 57){
                        if($(this).val().length === 1){
                            $(this).val($(this).val() + event.key + "/");
                        }else if($(this).val().length === 0){
                            if(event.key == 1 || event.key == 0){
                                month = event.key;
                                return event.charCode;
                            }else{
                                $(this).val(0 + event.key + "/");
                            }
                        }else if($(this).val().length > 2 && $(this).val().length < 9){
                            return event.charCode;
                        }
                    }
                    return false;
                }).keyup(function(event){
                    $(".date_value").html($(this).val());
                    if(event.keyCode == 8 && $(".expire").val().length == 4){
                        $(this).val(month);
                    }

                    if($(this).val().length === 0){
                        $(".date_value").text("MM/YYYY");
                    }
                }).keydown(function(){
                    $(".date_value").html($(this).val());
                }).focus(function(){
                    $(".date_value").css("color", "white");
                });



            });
        </script>

        <fieldset class="moip-fieldset   <?= $formPagamento->method != 'moip_creditCard' ? 'hide' : '' ?>" style=" max-width: 100%; height: auto;" id="moip_creditCard"  >
            <legend>Dados do Cartão:</legend>
            <p class="alert alert-warning">
                <i class="glyphicon glyphicon-exclamation-sign"></i>
                ATENÇÃO - Taxa Fixa por Loja: <?= Yii::$app->formatter->asCurrency(Moip::TAXMOIP) ?>.
            </p>
            <div class="container" style="background-color: rgba(255,255,255,0.16); border-radius: 10px">
                <div class="col-sm-6">
                    <div class="col1" >
                        <div class="card">
                            <div class="front">
                                <div class="type">
                                    <img class="bankid"/>
                                </div>
                                <span class="chip"></span>
                                <span class="card_number">&#x25CF;&#x25CF;&#x25CF;&#x25CF; &#x25CF;&#x25CF;&#x25CF;&#x25CF; &#x25CF;&#x25CF;&#x25CF;&#x25CF; &#x25CF;&#x25CF;&#x25CF;&#x25CF; </span>
                                <div class="date"><span class="date_value">MM / YYYY</span></div>
                                <span class="fullname">NOME COMPLETO </span>
                            </div>
                            <div class="back">
                                <div class="magnetic"></div>
                                <div class="bar"></div>
                                <span class="seccode">&#x25CF;&#x25CF;&#x25CF;</span>
                                <span class="chip"></span><span class="disclaimer">This card is property of Random Bank of Random corporation. <br> If found please return to Random Bank of Random corporation - 21968 Paris, Verdi Street, 34 </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="col2">
                        <?= Html::activeHiddenInput($formPagamento, 'method', ['id' => 'forma_pagamento']); ?>
                        <div class="col">
                            <?= $form->field($formPagamento, 'ccNumber')->textInput(['maxlength' => 22, 'class'=>'number']) ?>
                        </div>
                        <div class="col">
                            <?= $form->field($formPagamento, 'name')->textInput(['class'=> 'inputname']); ?>
                        </div>

                        <div class="col">
                            <?= Html::label('Data de Nascimento', 'FormPagamento[birthday]',
                                ['class' => ' black']) ?>
                            <?= MaskedInput::widget([
                                'name' => 'FormPagamento[birthDay]',
                                'mask' => '99/99/9999'
                            ]);
                            ?>
                        </div>

                        <div class="col">
                            <?= $form->field($formPagamento, 'cvcNumber')->textInput(['maxlength' => 4, 'class'=>'ccv']) ?>
                        </div>

                        <div class="col">

                            <?= $form->field($formPagamento, 'expirationDate')->textInput(['maxlength' => 5, 'class'=>'expire']) ?>

                        </div>

                        <div class="col">
                            <?= $form->field($formPagamento, 'installments')->dropDownList([],
                                ['class' => 'select2', 'id' => 'nrParcelas', 'class' => 'dropdownlist']) ?>
                        </div>






                        <!--
                <label>Numero do cartão</label>
                <input class="number" <?=$formPagamento->ccNumber  ?> type="text" ng-model="ncard" maxlength="19"/>

                <label>Nome do cartão</label>
                <input class="inputname" <?=$formPagamento->name  ?>  type="text" placeholder="">


                <label>Data de vencimento</label>
                <input class="expire" <?=$formPagamento->expirationDate ?> type="text" placeholder="MM / YYYY"/>


                <label>Codigo de segurança</label>
                <input class="ccv" <?=$formPagamento->cvcNumber  ?> type="text" placeholder="CVC" maxlength="3" />-->
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
        <?= Html::submitButton('Comprar', ['class' => 'color btn btn-primary btn-lg  ', ]) ?>
    </div>
</div>



<style>
    .color{
        background-color: #007576;

        border: #f5f5f5;
    }


    .color:focus{
        background-color: #f5f5f5;
        color: #ffffff;
        content:'Efetuada';
    }
</style>

<?php ActiveForm::end(); ?>
