<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Fornecedor;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\PedidoCompra */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pedido-compra-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-md-12">
                <?= $form->field($model, 'data')->hiddenInput(['value' => date('d/m/Y')])->label(false) ?>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?php
                    echo  $form->field($model, 'filial_id')->widget(Select2::class, [
                        'data' => [94 => 'Peça Agora MG', 95 => 'Peça Agora Filial', 96 => 'Peça Agora São Paulo'],
                        'id' => 'filial_id',
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                        'options' => ['placeholder' => 'Selecione uma Filial']
                    ])->label("Filial de Estoque:")
                    ?>
                </div>
                <div class="col-md-4">
                    <?php
                    $fornecedor = $model->fornecedor_id ? Fornecedor::findOne($model->fornecedor_id)->nome : null;
                    echo  $form->field($model, 'fornecedor_id')->widget(Select2::class, [
                        'initValueText' => $fornecedor,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => Url::to(['fornecedor/get-fornecedor']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                        ],
                        'options' => ['placeholder' => 'Fornecedor']
                    ])->label("Fornecedor:")
                    ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'descricao')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'observacao')->textInput(['maxlength' => true]); ?>
                </div>
            </div>

            <?php
            $js = <<< JS
        $('#pedidocompra-fornecedor_id').change(
        function(){
            
            var fornecedor_id = $(this).val();

            $.ajax(
            {
                type:"GET",url:baseUrl+"/fornecedor/get-email-fornecedor",
                data:{id:fornecedor_id},
                success:function(retorno)
                {
                    var retorno = $.parseJSON(retorno);
                    document.getElementById('pedidocompra-email').value = ' compras.pecaagora@gmail.com; ' + retorno.email;
                },
            })

       }
   );

JS;

            $this->registerJs($js);
            ?>
            <div class="col-md-12">
                <div class="form-group">
                    <?= Html::submitButton('Inserir Produto', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
            <?php
            ActiveForm::end();
            ?>
        </div>
    </div>
</div>