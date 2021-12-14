<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ProdutoFilial;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;
use common\models\Fornecedor;
use frontend\models\MarkupMestre;
use yii\helpers\ArrayHelper;
use yii\web\View;
?>

<style>
    @media screen and (min-width: 768px) {
        .modal-dialog {
            left: 0%;
            right: auto;
            width: 1150px;
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .modal-content {
            -webkit-box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }
    }
</style>

<div class="pedido-compra-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => 'create-update-pedido']]); ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-md-12">
                <?= $form->field($modelCompra, 'data')->hiddenInput(['value' => date('d/m/Y')])->label(false) ?>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?php
                    echo  $form->field($modelCompra, 'filial_id')->widget(Select2::class, [
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
                    $fornecedor = $modelCompra->fornecedor_id ? Fornecedor::findOne($modelCompra->fornecedor_id)->nome : null;
                    echo  $form->field($modelCompra, 'fornecedor_id')->widget(Select2::class, [
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
                    <?= $form->field($modelCompra, 'descricao')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($modelCompra, 'email')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($modelCompra, 'observacao')->textInput(['maxlength' => true]); ?>
                </div>
                <div class='col-md-3'>
                    <?php
                    echo  $form->field($modelCompra, 'status')->widget(Select2::class, [
                        'data' => [1 => 'Aberto', 2 => 'Enviado', 3 => 'Recebido/Conferido'],
                        'options' => ['placeholder' => 'Selecione Status'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label("Status:")
                    ?> </div>
            </div>
            <?php
            $js = <<< JS

                $('.edit_modal').click(function(e){
                    var id_produto = $(this).attr("id");

                $('#produto-modal').modal('show').find('#modalContent').load($(this).attr('value'));

                $.ajax(
                    {
                        type:"GET",url:baseUrl+"/pedido-compra-produto-filial/get-modal",
                        data:{id:id_produto},
                        dataType: "JSON",
                        success:function(retorno) {
                            document.getElementById('produto_modal').value = retorno[1]['produto'];
                            document.getElementById('id_modal').value = retorno[0]['id'];
                            document.getElementById('quantidade_modal').value = retorno[0]['quantidade'];
                            document.getElementById('valor_compra_modal').value = retorno[0]['valor_compra'];
                            document.getElementById('valor_venda_modal').value = retorno[0]['valor_venda'];
                            document.getElementById('observacao_modal').value = retorno[0]['observacao'];
                            document.getElementById('e_atualizar_site_modal').value = retorno[0]['e_atualizar_site'];
                            document.getElementById('select_modal').value = retorno[0]['valor_markup'];
                        },
                    })
                
                return false;

                });

                $('#btn_atualizar').click(function(e){
                    var id = $('#id_modal').attr("id");

                $.ajax(
                    {
                        type:"POST",url:baseUrl+"/pedido-compra-produto-filial/update-modal",
                        data:{id:id},
                        sucess: function (resposta) {
                            Materialize.toast(resposta, 2000);
                        },
                        error: function (erro) {                
                            Materialize.toast(erro.responseText, 3000, 'red-text');

                        }
                    })
                
                return false;

                });

            $('#pedidocompra-filial_id').change(
            function(){
                
                var filial_id = $(this).val();

                $.ajax(
                {
                    type:"GET",url:baseUrl+"/filial/get-email-filial",
                    data:{id:filial_id},
                    success:function(retorno)
                    {
                        var retorno = $.parseJSON(retorno);
                        document.getElementById('pedidocompra-email').value = ' compras.pecaagora@gmail.com; entregasp.pecaagora@gmail.com; ' + retorno.email;
                    },
                })

           }
       );

JS;

            $this->registerJs($js);
            ?>
            <div class="col-md-3">
                <?= Html::submitButton('Atualizar', ['class' => 'btn btn-primary']) ?>

                <?= Html::a('Duplicar Pedido', ['duplicata', 'id' => $modelCompra->id], ['class' => 'btn btn-primary']) ?>
            </div>

            <?php
            ActiveForm::end();
            ?>


        </div>
    </div>
</div>

<?php
$formatJs = <<< 'JS'
var formatRepo = function (repo) {
    if (repo.loading) {
        return repo.test;
    }
    var markup =
'<div class="row">' + 
    '<div class="col-sm-4">' +
        '<b style="margin-left:5px">' + repo.nome + '</b>' + 

    '</div>' +
    '<div class="col-sm-2"> ' + repo.codigo_global + '</div>' +
    '<div class="col-sm-2">' + repo.filial + '</div>' +
    '<div class="col-sm-1"> ' + repo.quantidade + '</div>' +
    '<div class="col-sm-1">' + repo.codigo_fabricante + '</div>' +
    '<div class="col-sm-1">' + repo.valor + '</div>' +
    '<div class="col-sm-1">' + repo.compra + '</div>' +
'</div>';
    
    return '<div>' + markup + '</div>';
};
JS;

// Register the formatting script
$this->registerJs($formatJs, View::POS_HEAD);

// script to parse the results into the format expected by Select2
$resultsJs = <<< JS

JS;


?>

<div class="pedido-compra-produto-filial-form">
    <?php $url = 'pedido-compra-produto-filial/update?id=' . $modelProduto->pedido_compra_id . '&idProduto=' . $modelProduto->id; ?>

    <?php $form = ActiveForm::begin([
        'action' => [$modelProduto->isNewRecord ? 'pedido-compra-produto-filial/create?id=' . $modelCompra->id : $url],
        'method' => 'post',
        'options' => ['id' => 'create-update-produto']
    ]); ?>


    <div class="panel panel-default">
        <div class="panel-body">
            <?= $form->field($modelProduto, 'id')->hiddenInput()->label(false) ?>

            <?= $form->field($modelProduto, 'pedido_compra_id')->hiddenInput(['value' => Yii::$app->request->get('id')])->label(false); ?>

            <div class="col-md-12">

                <?php
                $produto_filial = $modelProduto->produto_filial_id ? ProdutoFilial::findOne($modelProduto->produto_filial_id)->filial->nome . " - " . ProdutoFilial::findOne($modelProduto->produto_filial_id)->produto->nome . "(" . ProdutoFilial::findOne($modelProduto->produto_filial_id)->produto->codigo_global . ")" . "(" . ProdutoFilial::findOne($modelProduto->produto_filial_id)->produto->codigo_fabricante . ")" : null;
                echo  $form->field($modelProduto, 'produto_filial_id')->widget(Select2::class, [
                    'initValueText' => $produto_filial,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'ajax' => [
                            'url' => Url::to(['valor-produto-filial/get-produto-filial']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('formatRepo'),
                        'templateSelection' => new JsExpression('formatRepoSelection'),
                    ],
                    'options' => ['placeholder' => 'Selecione um Produto']
                ])->label("Produto:");
                ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($modelProduto, 'quantidade')->textInput() ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($modelProduto, 'valor_compra')->textInput() ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($modelProduto, 'valor_venda')->textInput() ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($modelProduto, 'observacao')->textarea() ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($modelProduto, 'e_atualizar_site')->checkbox() ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($modelProduto, 'markup_id')->widget(Select2::class, [
                    'data' => ArrayHelper::map(
                        MarkupMestre::find()->andWhere(['e_markup_padrao' => false])->orderBy(['descricao' => SORT_ASC])->all(),
                        'id',
                        'descricao'
                    ),
                    'pluginOptions' => ['allowClear' => true],
                    'options' => ['placeholder' => 'Selecione um Markup']
                ]) ?>
            </div>
            <?php

            $js = <<< JS
            $('#pedidocompraprodutofilial-markup_id').change(function(){
                
                var markup_id = $(this).val();
                var valor_compra = $('#pedidocompraprodutofilial-valor_compra').val();

                $.ajax(
                {
                    type:"GET",url:baseUrl+"/markup-mestre/get-markup",
                    data:{id:markup_id, valor: valor_compra},
                    success:function(retorno)
                    {
                        var retorno = $.parseJSON(retorno);
                        document.getElementById('pedidocompraprodutofilial-valor_venda').value = retorno.markup_valor;
                    },
                })

           }
       );

       $('#pedidocompraprodutofilial-valor_compra').focusout(function(){
            var valor_compra = $(this).val();

            $.ajax(
                {
                    type:"GET",url:baseUrl+"/markup-mestre/get-markup",
                    data:{id:'', valor: valor_compra},
                    success:function(retorno)
                    {
                        var retorno = $.parseJSON(retorno);
                        document.getElementById('pedidocompraprodutofilial-valor_venda').value = retorno.markup_valor;
                        $('#pedidocompraprodutofilial-markup_id').val('');
                    },
                })

       }
       );

       

JS;

            $this->registerJs($js);

            $formatJs = <<< 'JS'
            var formatRepoSelection = function (repo) {
            return repo.text ;
            }
JS;

            // Register the formatting script
            $this->registerJs($formatJs, View::POS_HEAD);

            // script to parse the results into the format expected by Select2
            $resultsJs = <<< JS

JS;
            ?>
            <div class="col-md-12">
                <div class="form-group">
                    <?= Html::submitButton($modelProduto->isNewRecord ? 'Inserir Produto' : 'Atualizar', ['class' => $modelProduto->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>


            <?php
            ActiveForm::end();
            ?>
        </div>
    </div>
</div>