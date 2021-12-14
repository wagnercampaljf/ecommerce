<?php

use backend\models\Administrador;
use backend\models\ContaCorrente;
use kartik\form\ActiveForm;
use yii\helpers\Html;
use common\models\ProdutoFilial;
use yii\web\JsExpression;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Filial;
use common\models\Transportadora;
use Mpdf\Utils\Arrays;
use common\models\Empresa;
use kartik\typeahead\Typeahead;

?>

<div class="pedido-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <h3>Dados do Cliente</h3>
                </div>
                <?= $form->field($modelPedido, 'id')->hiddenInput()->label(false) ?>
                <div class="col-md-4">
                    <?php $cliente = $modelEmpresa->nome ? Empresa::findOne(['nome' => $modelEmpresa->nome])->nome : '';
                    echo $form->field($modelEmpresa, 'nome')->widget(Typeahead::classname(), [
                        'options' => ['placeholder' => 'Selecione o Cliente'],
                        'pluginOptions' => ['highlight' => true],
                        'dataset' => [
                            [
                                'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                                'display' => 'value',
                                'remote' => [
                                    'url' => Url::to(['pedidos/get-dados-cliente']) . '?q=%QUERY',
                                    'wildcard' => '%QUERY'
                                ]
                            ]
                        ]
                    ])->label("Cliente:"); ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelEmpresa, 'documento')->textInput(['maxlength' => true])->label('CPF/CNPJ') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelEmpresa, 'email')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($modelEmpresa, 'telefone')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($modelEndEmpresa, 'logradouro')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($modelEndEmpresa, 'cidade_id')->hiddenInput()->label(false) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($modelEndEmpresa, 'complemento')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($modelEndEmpresa, 'numero')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($modelEndEmpresa, 'cep')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($modelEndEmpresa, 'bairro')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h3>Dados do Pedido</h3>
                </div>
                <div class="col-md-3">
                    <?php
                    echo  $form->field($modelPedido, 'filial_id')->widget(Select2::class, [
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

                    $transportadora = $modelPedido->transportadora_id ? Transportadora::findOne($modelPedido->transportadora_id)->nome : null;
                    echo  $form->field($modelPedido, 'transportadora_id')->widget(Select2::class, [
                        'initValueText' => $transportadora,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => Url::to(['pedidos/get-transportadora']),
                                'dataType' => 'json',
                                'data' => new JsExpression("function(params) {
                                    var filial = $('#pedido-filial_id').val();
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

                <div class="col-md-4">
                    <?php
                    echo  $form->field($modelPedido, 'tipo_frete')->widget(Select2::class, [
                        'data' => [0 => 'Contratação do frete por conta do remetente', 1 => 'Contratação do frete por conta do destinatário', 9 => 'Sem ocorrência de Transporte'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'options' => ['placeholder' => 'Selecione o tipo de frete']
                    ])->label("Tipo de Frete:");
                    ?>
                </div>
                <div class="col-md-4">
                    <?php

                    $conta_corrente = $modelPedido->conta_corrente_id ? ContaCorrente::findOne($modelPedido->conta_corrente_id)->descricao : null;
                    echo  $form->field($modelPedido, 'conta_corrente_id')->widget(Select2::class, [
                        'initValueText' => $conta_corrente,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 0,
                            'ajax' => [
                                'url' => Url::to(['pedidos/get-conta-corrente']),
                                'dataType' => 'json',
                                'data' => new JsExpression("function(params) {
                                    var filial = $('#pedido-filial_id').val();
                                    return {  
                                             filial: filial
                                           }; 
                                  }")
                            ],
                        ],
                        'options' => ['placeholder' => 'Selecione uma Conta Bancaria']
                    ])->label("Conta Corrente:");

                    ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelPedido, 'valor_frete')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-5">
                    <?php
                    echo  $form->field($modelPedido, 'origem_pedido')->widget(Select2::className(), [
                        'data' => [0 => 'Venda Pelo Site (whatsapp)', 1 => 'Cliente Pessoal', 2 => 'Rede Social', 3 => 'Disparador', 4 => 'Outros'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'options' => ['placeholder' => 'Selecione a origem do pedido']
                    ])->label("Origem do Pedido:");
                    ?>
                </div>
                <?php
                if (Yii::$app->user->id == 177 || Yii::$app->user->id == 161) {

                ?>

                    <div class="col-md-5">
                        <?php

                        $vendedor = $modelPedido->administrador_id ? Administrador::findOne($modelPedido->administrador_id)->nome : null;
                        echo  $form->field($modelPedido, 'administrador_id')->widget(Select2::class, [
                            'initValueText' => $vendedor,
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'ajax' => [
                                    'url' => Url::to(['pedidos/get-administrador']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                            ],
                            'options' => ['placeholder' => 'Selecione um Vendedor']
                        ])->label("Vendedor:");

                        ?>
                    </div>
                <?php
                }

                ?>

                <div class="col-md-12">
                    <?= $form->field($modelPedido, 'observacao')->textarea(['rows' => 6]) ?>
                </div>
            </div>
            <hr>

            <?php
            if ($id_produto) {
            ?>
                <div class="row">
                    <div class="col-md-12">
                        <h3>Produtos</h3>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $produto_filial = $modelPedProdFilial->produto_filial_id ? ProdutoFilial::findOne($modelPedProdFilial->produto_filial_id)->filial->nome . " - " . ProdutoFilial::findOne($modelPedProdFilial->produto_filial_id)->produto->nome . "(" . ProdutoFilial::findOne($modelPedProdFilial->produto_filial_id)->produto->codigo_global . ")" . "(" . ProdutoFilial::findOne($modelPedProdFilial->produto_filial_id)->produto->codigo_fabricante . ")" : null;
                        echo  $form->field($modelPedProdFilial, 'produto_filial_id')->widget(Select2::className(), [
                            'initValueText' => $produto_filial,
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'ajax' => [
                                    'url' => Url::to(['valor-produto-filial/get-produto-filial-unitario']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                            ],
                            'options' => ['placeholder' => 'Selecione um Produto']
                        ])->label("Produto:");
                        ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($modelPedProdFilial, 'valor')->textInput(['maxlength' => true]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($modelPedProdFilial, 'valor_cotacao')->textInput(['maxlength' => true]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($modelPedProdFilial, 'quantidade')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
            <?php }
            ?>

            <?= Html::a('Voltar', ['pedidos/view', 'id' => $modelPedido->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Atualizar', ['pedidos/pedido-update', 'id' => $modelPedido->id], ['class' => 'btn btn-primary', 'data-method' => 'POST']) ?>
            <?= Html::a('Adicionar Produto', ['pedidos/create', 'id' => $modelPedido->id], ['class' => 'btn btn-success']) ?>

            <?= $form->field($modelPedido, 'id')->hiddenInput()->label(false) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>

<?php

$js_conta = <<< JS
       $('#pedido-filial_id').change(
           function(){
            $('#pedido-conta_corrente_id').val(null).trigger('change');
            $('#pedido-transportadora_id').val(null).trigger('change');
               var filial = $('#pedido-filial_id').val();
                    $.ajax({
                        type: "GET",
                        url: baseUrl + "/pedidos/get-conta-corrente",
                        data: {
                            filial: filial
                        },
                        dataType: "JSON",
                        error: function(c) {
                            toastr.error("Erro ao processar o cálculo. Tente novamente em alguns segundos.");
                            $(".fa-spinner").hide();
                        },
                        success: function(c) {
                            if (c.length > 0){
			                    var option = '<option>Selecione o País </option>';
			                    $.each(c, function(i, obj){
				                    option += '<option value="'+obj.sigla+'">'+obj.nome+'</option>';    
			                    })
                            }
                        }
                })
           });
JS;

$this->registerJs($js_conta);

$js_cep = <<< JS
       $('#enderecoempresa-cep').keyup(
           function(){
                if($('#enderecoempresa-cep').val().length == 8){
                    $(".fa-spinner").show();
                    $.ajax({
                        type: "GET",
                        url: baseUrl + "/pedidos/get-endereco",
                        data: {
                            cep: $('#enderecoempresa-cep').val()
                        },
                        dataType: "JSON",
                        error: function(c) {
                            toastr.error("Erro ao processar o cálculo. Tente novamente em alguns segundos.");
                            $(".fa-spinner").hide();
                        },
                        success: function(c) {
                            document.getElementById("enderecoempresa-logradouro").value   = c.logradouro;
                            document.getElementById("enderecoempresa-cidade_id").value    = c.ibge;
                            document.getElementById("enderecoempresa-bairro").value       = c.bairro;
                            $(".fa-spinner").hide();
                        }
                    })
                } 
           });


JS;

$this->registerJs($js_cep);
?>