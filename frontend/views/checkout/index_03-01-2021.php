<?php
/**
 * @var $formPagamento common\models\FormPagamento
 * @var $filiais common\models\ProdutoFilial[]
 * @var $pedido common\models\Pedido
 */
/* @var $model common\models\EnderecoEmpresa */
/* @var $form yii\widgets\ActiveForm */
/* @var $comprador frontend\controllers\CompradorController */
/* @var $empresa common\models\Empresa */

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


//echo "<pre>";print_r($model); echo "</<pre>";


$comprador = Yii::$app->user->getIdentity();
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
$this->params['active'] = 'address';
?>
<?php $form = ActiveForm::begin(['id' => 'checkout-form']); ?>


    <div class="col-xs-12 col-sm-12 col-md-4">
<?php
echo $form->errorSummary($pedido);
$total = 0;


foreach ($filiais as $filial_id => $produtos):
    $filial = Filial::find()->andWhere(['id' => $filial_id])->one();
    ?>

    <!--Painel geral-->
    <div class="col-xs-12 col-sm-12 col-md-12" >
        <div class="panel panel-primary col-sm-12" style="background-color: white !important;"  >
            <div class="panel-heading" style="background-color:#ffffff; border-color: #ffffff">
                <h3 class="panel-title" style="color: #ffffff !important;"><?= $filial->nome ?></h3>
            </div>
            <div class="panel-body" style="padding: 2px !important;" >
            <!-- Painel dos produtos -->
                <div class="col-md-12">
                    <?php
                    $valorTotal = 0;
                    foreach ($produtos as $produto):
                        $valor_prod = $produto->getValorProdutoFilials()->ativo()->one()->getValorFinal($juridica);
                        ?>
                        <div class="">
                            <div class="text-center col-md-10">
                                <?= $produto->produto->getImage(['width' => '70%']) ?>
                            </div>
                            <div class="text-center">
                                <h4 class="fonte">
                                    <?= $produto->produto->getLabel() ?>
                                </h4>
                                <div class="">
                                    <p class="text-">
                                        <?= Yii::$app->formatter->asCurrency($valor_prod) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="">
                                <p class="h4 text-left">
                                    <small>Quantidade:</small> <?= Yii::$app->session['carrinho'][$produto->id]; ?>
                                </p>
                                <p class="h4 text-left">
                                    <small>Total:
                                    </small> <?= Yii::$app->formatter->asCurrency($valor_prod * Yii::$app->session['carrinho'][$produto->id]) ?>
                                </p>
                            </div><hr>
                            <?= Html::activeHiddenInput($pedido,
                                "[$filial_id][pedidoProdutoFilials]" . '[' . $produto->id . ']valor',
                                ['value' => $valor_prod]) ?>

                            <?= Html::activeHiddenInput($pedido,
                                "[$filial_id][pedidoProdutoFilials]" . '[' . $produto->id . ']quantidade',
                                ['value' => Yii::$app->session['carrinho'][$produto->id]]) ?>
                        </div>
                        <?php
                        $valorTotal += $valor_prod * Yii::$app->session['carrinho'][$produto->id];
                    endforeach; ?>

                <!-- Painel do Frete-->
                    <div class=" container-frete">
                        <div id="resultado-frete<?= $filial_id ?>" class="resultado-frete "
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
    </div>


    <?php
    if (is_array($radio_frete) && $frete_selecionado = ArrayHelper::getValue($radio_frete, $filial_id)) {
        echo Html::hiddenInput(
            'frete_selecionado_' . $filial_id,
            $frete_selecionado,
            ['id' => 'frete_selecionado_' . $filial_id]
        );
    }
    $total += $valorTotal;endforeach;

    ?>
    </div>

    <!--Pagamento e endereço-->
    <div class="col-md-8">
        <div class="panel panel-default col-md-12">
            <div class=" panel panel-default col-md-12">
                <div class="panel-body">
                    <h4>Este produto será entregue neste endereço:</h4>
                    <hr>
                    <div>
                        <?php
                        //var_dump($address->estado);
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
                        <!-- <a style=" text-decoration: underline;" href="<?= Url::to("minhaconta/update-address?from=checkout") ?>" >Alterar Endereço</a>-->
                        <a  data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" >Alterar Endereço</a><br>
                    </div><br>

                    <div class="collapse" id="collapseExample">
                        <div class="card card-body">
                            <?php $form = ActiveForm::begin([
                                'fieldConfig' => [
                                    'options' => ['class' => 'form-group col-lg-6 col-md-6 col-sm-6 col-xs-12']
                                ],
                                'errorSummaryCssClass' => 'alert alert-danger error-summary'
                            ]); ?>

                            <div class="row">
                                <?php
                                $cpf = str_replace(ArrayHelper::getValue($comprador, 'cpf'), " ", "");
                                if (($comprador->cpf == null or str_replace(" ","",$comprador->cpf) == "") && ArrayHelper::getValue($empresa, 'juridica') == false) {
                                    echo $form->field($comprador,'cpf')->textInput(['maxlength' => 14])->hint("Somente números")->label("CPF *");
                                }

                                echo $form->field($empresa, 'telefone')->textInput(['maxlength' => true])->label("Telefone *");
                                ?>
                            </div>

                            <div class="row">
                                <?= $form->field($model, 'cep')
                                    ->textInput([
                                        'maxlength' => 10,
                                        'id' => 'cep-comprador',
                                        'type' => 'number'
                                    ])
                                ?>
                                <i class="fa fa-spinner fa-spin" style="display: none;padding-top: 17px"></i>
                            </div>

                            <div class="row">
                                <?= $form->field($model, 'logradouro')->textInput(['maxlength' => true, 'id'=>'logradouro-comprador']) ?>
                                <?= $form->field($model, 'numero',
                                    ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->textInput(['maxlength' => true, 'type' => 'number', 'id'=>'numero-comprador']) ?>
                                <?= $form->field($model, 'complemento',
                                    ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->textInput(['maxlength' => true, 'id'=>'complemento-comprador']) ?>
                            </div>
                            <div class="row">
                                <?= $form->field($model, 'cidade')->textInput(['value' => $model->cidade, 'disabled' => true, 'id'=>'cidade-comprador'])->label("Cidade") ?>
                                <?= Html::activeHiddenInput($model, 'cidade_id') ?>
                                <?= $form->field($model, 'bairro')->textInput(['maxlength' => true, 'id' => 'bairro-comprador']) ?>


                            </div>
                            <div class="row">
                                <?= $form->field($model, 'referencia',
                                    ['options' => ['class' => 'form-group col-lg-12 col-md-12 col-sm-12 col-xs-12']])->textarea(['maxlength' => true]) ?>
                            </div>

                            <div class="form-group text-right row">

                                <?= $form->field($model, 'cidade_id')->hiddenInput()->label('') ?>
                                <?= $form->field($model, 'estado')->hiddenInput()->label("") ?>

                                <?= Html::Button(Yii::t('app', 'Alterar'), ['class' => 'btn btn-primary ', 'value'=>'submit', 'id'=>'botao_alterar_endereco']) ?>
                            </div>
                            <?php ActiveForm::end(); ?>

                        </div>
                    </div>
                </div>
            </div>
            <script>

                $(document).ready(function() {
                    $("#botao_alterar_endereco").prop('disabled', false);
                });

                $("#numero-comprador").change(function(a) {
                    let botao = $("#botao_alterar_endereco");
                    $(this).val() ? botao.prop('disabled', false) : botao.prop('disabled', true);
                });
                $("#cep-comprador").change(function(a) {
                    let botao = $("#botao_alterar_endereco");
                    $(this).val() ? botao.prop('disabled', false) : botao.prop('disabled', true);
                });
                $("#logradouro-comprador").change(function(a) {
                    let botao = $("#botao_alterar_endereco");
                    $(this).val() ? botao.prop('disabled', false) : botao.prop('disabled', true);
                });
                $("#numero-comprador").change(function(a) {
                    let botao = $("#botao_alterar_endereco");
                    $(this).val() ? botao.prop('disabled', false) : botao.prop('disabled', true);
                });
                $("#complemento-comprador").change(function(a) {
                    let botao = $("#botao_alterar_endereco");
                    $(this).val() ? botao.prop('disabled', false) : botao.prop('disabled', true);
                });
                $("#enderecoempresa-cidade_id").change(function(a) {
                    let botao = $("#botao_alterar_endereco");
                    $(this).val() ? botao.prop('disabled', false) : botao.prop('disabled', true);
                });
                $("#bairro-comprador").change(function(a) {
                    let botao = $("#botao_alterar_endereco");
                    $(this).val() ? botao.prop('disabled', false) : botao.prop('disabled', true);
                });


            </script>


            <?php

            // echo "<pre>";print_r($model); echo "</pre>";die;

            $js = <<< JS
       $('#botao_alterar_endereco').click(
           function(){

               //alert($('#enderecoempresa-cidade_id').val());
               
               $.ajax(
               {
               type:"GET",url:baseUrl+"/checkout/update-endereco",
               data:{
                    endereco_empresa_id : $model->id,
                    cep                 : $('#cep-comprador').val(),
                    logradouro          : $('#logradouro-comprador').val(),
                    numero              : $('#numero-comprador').val(),
                    complemento         : $('#complemento-comprador').val(),
                    cidade_id           : $('#enderecoempresa-cidade_id').val(),//$('#cidade-comprador').val(),
                    bairro              : $('#bairro-comprador').val()
                    
                   
                    },
               success:function(retorno)
               {
                   //alert(retorno);

                    var retorno = $.parseJSON(retorno);
                    if(retorno.erro){
                        
                       
                        
                       // alert(retorno.mensagem);
                        
                        
                        

                    }
                    else{
                        alert(retorno.mensagem);
                    }
                   
                    //document.getElementById('valor').readOnly = retorno.e_valor_bloqueado;
               },
           })
               }
           );
JS;

            $this->registerJs($js);

            $js_cep = <<< JS
       $('#cep-comprador').keyup(
           function(){
                //alert($('#cep-comprador').val().length);
                
                if($('#cep-comprador').val().length == 8){
                    $(".fa-spinner").show();
                    $.ajax({
                        type: "GET",
                        url: baseUrl + "/checkout/get-endereco",
                        data: {
                            cep: $('#cep-comprador').val()
                        },
                        dataType: "JSON",
                        error: function(c) {
                            toastr.error("Erro ao processar o cálculo. Tente novamente em alguns segundos.");
                            $(".fa-spinner").hide();
                        },
                        success: function(c) {
                            document.getElementById("logradouro-comprador").value   = c.logradouro;
                            document.getElementById("cidade-comprador").value       = c.localidade;
                            //document.getElementById("cidade_id-comprador").value    = c.ibge;
                            document.getElementById("bairro-comprador").value       = c.bairro;
                            //document.getElementById("estado-comprador").value       = c.uf
                            $(".fa-spinner").hide();
                        }
                    })
                } 
           });


JS;

            $this->registerJs($js_cep);
            ?>

            <div class="panel-body " id="formadepagamento" >
                <div class="row">
                    <div class="col-md-6 text-left h3" >
                        Forma de Pagamento
                    </div>
                </div>
                <br>
                <div class="row text-center" id="">
                    <div class="col-lg-6 col-md-4 col-sm-8 col-xs-12 " >
                        <a href="#moip_creditCard"> <button style="margin-left: 5px;width: 220.83px" type="button" class="btn btn-bubble"
                                data-forma-pagamento="moip_creditCard" id="btn-moip">
                            <i style="color: #fff;" class="fa fa-credit-card"></i> Pagar com<b> Cartão</b>
                            </button></a>
                        <br>
                        <br>
                        <img width="100%" src="https://www.moip.com.br/imgs/banner_2_2.jpg">
                    </div>
                    <div class="col-lg-6 col-md-4 col-sm-8 col-xs-12  " style="align-items: center">
                        <a href="#moip_boleto"> <button style="margin-left: 5px; width: 220.83px" type="button" class="btn btn-bubble"
                                data-forma-pagamento="moip_boleto" id="btn-moip">
                            <i style="color: #fff;" class="fa fa-barcode"></i> <b>Boleto</b>
                        </button></a>
                        <br/>
                        <br/>
                        <img src="https://www.moip.com.br/imgs/banner_1_1.jpg">
                    </div><br>
                    <div class="col-md-12 text-right h3">
                        <div class="container">

                        </div>

                    </div><br><br><br><br>

                    <div class="col-md-12 text-right h3">
                        Total:
                        <span class="grow" id="valorTotal" data-valor-total="<?= $total ?>"><?= Yii::$app->formatter->asCurrency($total) ?></span>
                    </div>
                </div>
                <br> <br>

                <fieldset class="moip-fieldset   <?= $formPagamento->method != 'moip_creditCard' ? 'hide' : '' ?>" style=" max-width: 100%; height: auto; margin: -18px !important;" id="moip_creditCard"  ></a>
                    <h4 style="border-style: solid; text-align: center; width: 100%">Dados do Cartão:</h4><br>
                    <div class="container cartão" style="background-color: rgba(255,255,255,0.05); border-radius: 10px;">
                        <div class="col-sm-6">
                            <div class="col1"  >
                                <div class="card" style="background-color: rgb(145,145,145);">
                                    <div class="front">
                                        <div class="type" >
                                            <img class="bankid"/>
                                        </div >
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
                        <div class="panel-body" >
                            <div class="col2">
                                <?= Html::activeHiddenInput($formPagamento, 'method', ['id' => 'forma_pagamento']); ?>
                                <div class="row">
                                    <?= $form->field($formPagamento, 'ccNumber')->textInput(['maxlength' => 22, 'inputmode'=>'numeric', 'class'=>'number form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']) ?>
                                    <?= $form->field($formPagamento, 'name')->textInput(['class'=> 'inputname form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']); ?>
                                    <?= $form->field($formPagamento, 'cvcNumber')->textInput(['maxlength' => 4,'type'=>'tel', 'class'=>'ccv form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']) ?>

                                </div>

                                <div class="row">
                                    <div class="row">

                                    <div class="row">
                                        <?= Html::label('Data de Nascimento', 'FormPagamento[birthday]',
                                            ['class' => ' row black form-group col-lg-6 col-md-6 col-sm-6 col-xs-12']) ?>
                                    </div>

                                    <div class="row">
                                        <?= MaskedInput::widget([
                                            'name' => 'FormPagamento[birthDay]',
                                            'mask' => '99/99/9999',
                                            'type'=>'tel',
                                        ]);
                                        ?>
                                    </div>
                                </div>



                                    <div class="row">
                                    <div class="row">
                                        <?= Html::label('Data de Vencimento', 'FormPagamento[expirationDate]',
                                            ['class' => 'expire ']) ?>
                                    </div>

                                    <div class="row">
                                        <?= MaskedInput::widget([
                                            'name' => 'FormPagamento[expirationDate]',
                                            'mask' => '99/99',
                                            'type'=>'tel',
                                        ]);
                                        ?>
                                    </div>
                                </div>
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


                            <div class="row ">
                                <?= $form->field($formPagamento, 'installments')->dropDownList([],
                                    ['class' => 'select2', 'id' => 'nrParcelas', 'class' => 'dropdownlist']) ?>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="moip-fieldset <?= $formPagamento->method != 'moip_boleto' ? 'hide' : '' ?>" id="moip_boleto">
                    <h4 style="border-style: solid; text-align: center">&nbspBoleto     :&nbsp</h4><br>
                    <p class="alert alert-dark">
                        O boleto será gerado após a finalização de sua compra. Imprima e pague no banco ou pague pela internet utilizando o código de barras do boleto.
                    </p>
                </fieldset>
            </div>
            <div style="padding-left: 25px;" class="panel-footer text-left <?= empty($formPagamento->method) ? 'hide' : '' ?>"  value="TÍTULO do BOTÃO AQUI" onClick="alert('PEDIDO EFETUADO, AGUARDE...'); return true">
                <?= Html::submitButton('Comprar', ['class' => 'btn btn-success btn-lg']) ?>
            </div>
        </div>
    </div>

<style>
    .fonte{
        font-size: 12px;
    }

    .color{
        background-color: #007576;
        border: #f5f5f5;
    }

    .color:focus{
        background-color: #f5f5f5;
        color: #ffffff;
    }

    .col1{
        width: 100%;
        height: 50%;
    }
    .cartão{
        margin-left: -38px !important;
    }

    .btn-bubble {
        color: white;
        background-color: #77b11c;

    }
    .btn-bubble:hover, .btn-bubble:focus {
        -webkit-animation: bubbles 1s forwards ease-out;
        animation: bubbles 1s forwards ease-out;
        background: radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 59% 125% / 0.93em 0.93em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 69% 149% / 0.83em 0.83em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 42% 108% / 0.83em 0.83em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 101% 107% / 0.99em 0.99em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 9% 128% / 1.14em 1.14em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 73% 100% / 0.71em 0.71em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 25% 86% / 0.55em 0.55em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 23% 103% / 1.08em 1.08em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 30% 126% / 0.67em 0.67em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 36% 98% / 1.04em 1.04em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 44% 130% / 0.61em 0.61em;
        background-color: #77b11c;

    }

    @-webkit-keyframes bubbles {
        100% {
            background-position: 55% -82%, 69% -167%, 44% -194%, 101% -285%, 10% -171%, 70% -126%, 15% -89%, 24% -60%, 22% -140%, 43% -17%, 46% -252%;
            box-shadow: inset 0 -6.5em 0 #0072c4;
        }
    }

    @keyframes bubbles {
        100% {
            background-position: 55% -82%, 69% -167%, 44% -194%, 101% -285%, 10% -171%, 70% -126%, 15% -89%, 24% -60%, 22% -140%, 43% -17%, 46% -252%;
            box-shadow: inset 0 -6.5em 0 #0072c4;
        }
    }


    .btn {
        display: inline-block;
        text-decoration: none;
    }

</style>







<?php ActiveForm::end(); ?>