<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
use common\models\PedidoMercadoLivreShipments;
use common\models\Transportadora;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

$modelShip = PedidoMercadoLivreShipments::findOne(['order_id' => $model->pedido_meli_id]);
?>

<div class="pedido-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-md-12">
                <h3>Dados de Envio</h3>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?php
                    echo  $form->field($model, 'filial_id')->widget(Select2::class, [
                        'data' => [94 => 'Peça Agora MG1', 96 => 'Peça Agora SP2', 95 => 'Peça Agora SP3', 93 => 'Peça Agora MG4'],
                        'id' => 'filial_id',
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                        'options' => ['placeholder' => 'Selecione uma Filial']
                    ])->label("Filial:")
                    ?>
                </div>
                <div class="col-md-5">
                    <?php

                    $transportadora = $model->transportadora_id ? Transportadora::findOne($model->transportadora_id)->nome : null;
                    echo  $form->field($model, 'transportadora_id')->widget(Select2::class, [
                        'initValueText' => $transportadora,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => Url::to(['/pedidos-mercado-livre/get-transportadora']),
                                'dataType' => 'json',
                                'data' => new JsExpression("function(params) {
                                    var filial = $('#pedidomercadolivre-filial_id').val();
                                    return { q:params.term, 
                                             filial: filial
                                           }; 
                                  }")
                            ],
                        ],
                        'options' => ['placeholder' => 'Selecione uma transportadora']
                    ])->label("Transportadora:");

                    ?>
                </div>
            </div>
            <div class="row">
                <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
                <div class="col-md-4">
                    <?= $form->field($model, 'receiver_name')->textInput(['maxlength' => true])->label('Nome Contato') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'shipping_option_list_cost')->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'alias' => 'decimal',
                            'digits' => 2,
                            'digitsOptional' => false,
                            'radixPoint' => '.',
                            'groupSeparator' => '',
                            'autoGroup' => true,
                            'removeMaskOnSubmit' => true,
                        ],
                    ])->label('Valor Frete') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'receiver_street_name')->textInput(['maxlength' => true])->label('Logradouro') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'receiver_street_number')->textInput()->label(false)->label('Número') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'receiver_comment')->textInput(['maxlength' => true])->label('Complemento') ?>

                </div>

            </div>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'receiver_neighborhood_name')->textInput(['maxlength' => true])->label('Bairro') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'receiver_zip_code')->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '99999-999'
                    ])->label('CEP') ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'receiver_city_name')->textInput(['maxlength' => true])->label('Cidade') ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'receiver_state_id')->textInput(['maxlength' => true])->label('Estado(Sigla)') ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'receiver_phone')->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '(99) 99999-9999'
                    ])->label('Telefone') ?>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'observacao_envio')->textarea(['rows' => 6]) ?>
                </div>
            </div>
            </br>
            <div class="row">
                <div class="col-md-2">
                    <?= Html::a('Atualizar', ['pedidos-mercado-livre/pedidos-mercado-livre-update', 'id' => $model->id], ['class' => 'btn btn-primary', 'data-method' => 'POST']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
</div>
<?php

$js_cep = <<< JS
       $('#pedidomercadolivre-receiver_zip_code').keyup(
           function(){
               var cep = $('#pedidomercadolivre-receiver_zip_code').val();
               cep.replace("-", "");
                    $(".fa-spinner").show();
                    $.ajax({
                        type: "GET",
                        url: baseUrl + "/pedidos-mercado-livre/get-endereco",
                        data: {
                            cep: $('#pedidomercadolivre-receiver_zip_code').val()
                        },
                        dataType: "JSON",
                        error: function(c) {
                            toastr.error("Erro ao processar o cálculo. Tente novamente em alguns segundos.");
                            $(".fa-spinner").hide();
                        },
                        success: function(c) {
                            document.getElementById("pedidomercadolivre-receiver_street_name").value   = c.logradouro;
                            document.getElementById("pedidomercadolivre-receiver_neighborhood_name").value = c.bairro;
                            document.getElementById("pedidomercadolivre-receiver_city_name").value     = c.localidade;
                            $(".fa-spinner").hide();
                        }
                    })
           });


JS;

$this->registerJs($js_cep);

$js_conta = <<< JS
       $('#pedidomercadolivre-filial_id').change(
           function(){
            $('#pedidomercadolivre-transportadora_id').val(null).trigger('change');
           }
        );
JS;

$this->registerJs($js_conta);
