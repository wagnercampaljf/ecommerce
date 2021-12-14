<?php
//1111
use common\models\Fabricante;
use common\models\MarcaProduto;
use common\models\Subcategoria;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\redactor\widgets\Redactor;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use common\models\Produto;
use common\models\ProdutoCondicao;

// echo '<pre>'; print_r($model); echo '</pre>'; die;


/* @var $this yii\web\View */
/* @var $model common\models\Produto */
/* @var $form yii\widgets\ActiveForm */

$configSelect2 = [
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 3,
        'ajax' => [
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {q:params.term}; }')
        ],
    ],
];
if (!$model->isNewRecord) {
    $model->anoModelo_id = ArrayHelper::getColumn($model->anosModelo, 'id');
}
?>

<div class="produto-form">
    <div class="row">
        <div id="mensagem">Alterações salvas com sucesso!</div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'fieldConfig' => [
                    'options' => [
                        'class' => 'form-group',
                    ]
                ]
            ]); ?>
            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">


                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'nome')->textInput(['maxlength' => true])->hint("O Nome deve conter no máximo 60 caracteres.", ['id' => 'characterLeft']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class=" col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'e_ativo')->checkbox() ?>
                    </div>
                    <div class=" col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'e_valor_bloqueado')->checkbox(['onclick' => "desabilitar2(this.checked)", 'id' => "conferida",])  ?>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'codigo_global')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'codigo_fabricante')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'codigo_montadora')->textInput(['maxlength' => true])->label("NCM") ?>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'codigo_barras')->textInput(['maxlength' => true])->label("Código de Barras (EAN)") ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'codigo_fornecedor')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'codigo_similar')->textarea(['rows' => 1])->hint("Separar por < br>") ?>
                    </div>
                </div>
                
				<hr>

                <div class="row">

                    <div class=" col-lg-3 col-md-6 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'peso')->textInput(['id' => "Peso", 'readOnly' => $model->e_medidas_conferidas])->hint("Separe as casa decimais com ponto.")->label("Peso (Kg)") ?>
                    </div>
                    <div class=" col-lg-3 col-md-6 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'altura')->textInput(['id' => "Altura", 'readOnly' => $model->e_medidas_conferidas])->label("Altura (cm)") ?>
                    </div>
                    <div class=" col-lg-3 col-md-6 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'largura')->textInput(['id' => "Largura", 'readOnly' => $model->e_medidas_conferidas])->label("Largura (cm)") ?>
                    </div>
                    <div class=" col-lg-3 col-md-6 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'profundidade')->textInput(['id' => "Profundidade", 'readOnly' => $model->e_medidas_conferidas])->label("Profundidade (cm)") ?>
                    </div>
                </div>
                <div class="row">
                	<div class=" col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'e_medidas_conferidas')->checkbox(['onclick' => "desabilitar(this.checked)", 'id' => "conferida",])  ?>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
					<div class=" col-lg-3 col-md-2 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'produto_condicao_id')->widget(Select2::className(), [
                            'data' => ArrayHelper::map(
                                ProdutoCondicao::find()->orderBy(['produto_condicao.nome' => SORT_ASC])->all(),
                                'id',
                                'nome'
                            ),
                            'options' => ['placeholder' => 'Selecione uma Condição']
                        ]) ?>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'fabricante_id')->widget(Select2::className(), [
                            'data' => ArrayHelper::map(
                                Fabricante::find()->orderBy(['fabricante.nome' => SORT_ASC])->all(),
                                'id',
                                'nome'
                            ),
                            'options' => ['placeholder' => 'Selecione um Fabricante']
                        ]) ?>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'subcategoria_id')->widget(Select2::className(), [
                            'data' => ArrayHelper::map(
                                Subcategoria::find()->orderBy(['subcategoria.nome' => SORT_ASC])->all(),
                                'id',
                                'nome'
                            ),
                            'options' => ['placeholder' => 'Selecione uma Subcategoria']
                        ]) ?>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'marca_produto_id')->widget(Select2::className(), [
                            'data' => ArrayHelper::map(
                                MarcaProduto::find()->orderBy(['marca_produto.nome' => SORT_ASC])->all(),
                                'id',
                                'nome'
                            ),
                            'options' => ['placeholder' => 'Selecione uma Marca']
                        ]) ?>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class=" col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'aplicacao')->widget(Redactor::className()) ?>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'aplicacao_complementar')->widget(Redactor::className()) ?>
                    </div>
                </div>
                <div class="row">
                    <div class=" col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <?= $form->field($model, 'descricao')->widget(Redactor::className()) ?>
                    </div>
                    <div class=" col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <?= $form->field($model, 'micro_descricao')->widget(Redactor::className()) ?>
                    </div>
                </div>                
                
                <hr>
                
                <div class="row">
                    <div class=" col-lg-3 col-md-6 col-sm-6 col-xs-6">
                        <?= $form->field($model, 'cest')->textInput()->label('CEST') ?>
                    </div>
                    <div class=" col-lg-3 col-md-6 col-sm-6 col-xs-6">
                        <?= $form->field($model, 'ipi')->textInput()->label('IPI') ?>
                    </div>
                    <div class=" col-lg-3 col-md-6 col-sm-6 col-xs-6">
                        <?= $form->field($model, 'pis_cofins')->textInput()->label('PIS/COFINS') ?>
                    </div>
                    <div class=" col-lg-3 col-md-6 col-sm-6 col-xs-6">
                        <?= $form->field($model, 'st')->textInput()->label('ST') ?>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                	<div class=" col-lg-3 col-md-6 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'localizacao')->textInput(['id' => "localizacao", 'readOnly' => $model->e_localizacao_bloqueado, 'style' => 'text-transform: capitalize'])->label("Localização do Produto em SP") ?>
                    </div>
                    <div class=" col-lg-3 col-md-6 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'localizacao_mg')->textInput(['id' => "localizacao_mg", 'readOnly' => $model->e_localizacao_bloqueado, 'style' => 'text-transform: capitalize'])->label("Localização do Produto em MG") ?>
                    </div>
                    <div class=" col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <?= $form->field($model, 'e_localizacao_bloqueado')->checkbox(['onclick' => "desabilitar3(this.checked)", 'id' => "conferida",])  ?>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class=" col-lg-3 col-md-6 col-sm-6 col-xs-6">
                        <?= $form->field($model, 'multiplicador')->textInput()->label("Multiplicador") ?>
                    </div>
                    <div class=" col-lg-3 col-md-6 col-sm-6 col-xs-6">
                        <?= $form->field($model, 'dias_expedicao')->textInput()->label('Dias de Expedição') ?>
                    </div>
                    <div class=" col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <?= $form->field($model, 'video')->textInput()->label('Vídeo') ?>
                    </div>
                </div>

                <div class="form-group pull-left">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Criar Produto') : Yii::t('app', 'Salvar Alterações'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
                </div>

                <div class="form-group pull-left">
                    <?php
                    if (!$model->isNewRecord) {
                    ?>
                        <div class="col-lg-12 col-md-12">

                            <?php $urlml = "duplicarproduto?id=" . $model->id; ?>
                            <a class="btn  btn-warning" id="sinc-ml2" href="<?php echo $urlml; ?>">
                                <i class="fa fa-shopping-cart" aria-hidden="true"></i> Duplicar Produto
                            </a>

                        </div>
                    <?php } ?>
                </div>

            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>


</div>
<style>
    #mensagem {
        transition: all 0.52s;
        background: #f0950d;
        border: #777 solid 1px;
        box-shadow: 1px 1px 5px rgba(0, 0, 0, .52);
        position: fixed;
        border-radius: 15px;
        top: -150px;
        width: 50%;
        padding: 25px 0;
        text-align: center;
        left: 25%;
        opacity: 0;
        z-index: 10000;
        color: black;
    }

    #mensagem.ver {
        top: 75px;
        opacity: 10;
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        $('#characterLeft').text('O Nome deve conter no máximo 60 caracteres');
        $('#produto-nome').keydown(function() {
            var max = 60;
            var len = $(this).val().length;
            if (len >= max) {
                $('#characterLeft').text('Quantidade máxima de caracteres');
                $('#characterLeft').addClass('red');
                $('#btnSubmit').addClass('disabled');
            } else {
                var ch = max - len;
                $('#characterLeft').text(ch + ' caracteres restantes');
                $('#btnSubmit').removeClass('disabled');
                $('#characterLeft').removeClass('red');
            }
        });

        function desabilitar(selecionado) {
            document.getElementById('Peso').readOnly = selecionado;
            document.getElementById('Altura').readOnly = selecionado;
            document.getElementById('Largura').readOnly = selecionado;
            document.getElementById('Profundidade').readOnly = selecionado;

            /*localStorage.setItem("checked", selecionado);*/ // cria o localStorage
        }

        function desabilitar3(selecionado) {

            document.getElementById('localizacao').readOnly = selecionado;
        }

        function msg() {
            $("#mensagem").addClass('ver');
            setTimeout(function() {
                $("#mensagem").removeClass('ver');
            }, 3000);
        }
    });
</script>
