<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
use common\models\ProdutoFilial;
use yii\web\JsExpression;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Filial;
use common\models\Transportadora;
use Mpdf\Utils\Arrays;

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
                    <?= $form->field($modelComprador, 'nome')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelComprador, 'cpf')->textInput(['maxlength' => true])->label('CPF') ?>
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
                    echo  $form->field($modelPedido, 'filial_id')->widget(Select2::className(), [
                        'data' => [94 => 'Peça Agora MG', 95 => 'Peça Agora Filial', 96 => 'Peça Agora São Paulo'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                        'options' => ['placeholder' => 'Selecione uma Filial']
                    ])->label("Filial:")
                    ?>
                </div>

                <div class="col-md-3">
                    <?php
                    $fretes = Transportadora::find()->all();
                    $data = [];

                    foreach ($fretes as $frete) {
                        $data[$frete->id] = $frete->nome;
                    }

                    echo  $form->field($modelPedido, 'transportadora_id')->widget(Select2::className(), [
                        'data' => $data,
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'options' => ['placeholder' => 'Selecione a transportadora']
                    ])->label("Transportadora:");
                    ?>
                </div>

                <div class="col-md-6">
                    <?php
                    echo  $form->field($modelPedido, 'tipo_frete')->widget(Select2::className(), [
                        'data' => [0 => 'Contratação do frete por conta do remetente', 1 => 'Contratação do frete por conta do destinatário', 9 => 'Sem ocorrência de Transporte'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'options' => ['placeholder' => 'Selecione o tipo de frete']
                    ])->label("Tipo de Frete:");
                    ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelPedido, 'data_prevista')->textInput() ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelPedido, 'valor_frete')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-md-12">
                    <?= $form->field($modelPedido, 'observacao')->textarea(['rows' => 6]) ?>
                </div>
            </div>
            <hr>

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
                                'url' => Url::to(['valor-produto-filial/get-produto-filial']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                        ],
                        'options' => ['placeholder' => 'Selecione um Produto']
                    ])->label("Produto:");
                    ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelPedProdFilial, 'valor')->textInput(['maxlength' => true]); ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelPedProdFilial, 'quantidade')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-2">
                    <?= Html::a('Adicionar Produto', ['pedidos/create', 'id' => $modelPedido->id], ['class' => 'btn btn-success', 'data-method' => 'POST']) ?>
                </div>
                <?= $form->field($modelPedido, 'id')->hiddenInput()->label(false) ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::a('Criar Pedido', ['pedidos/index'], ['class' => 'btn btn-success']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>