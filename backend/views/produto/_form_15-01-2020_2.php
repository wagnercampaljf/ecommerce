<?php

use common\models\Fabricante;
use common\models\Filial;
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
use common\models\ProdutoFilial;

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
<script>
    $(document).ready(function(){
        $('#characterLeft').text('O Nome deve conter no máximo 60 caracteres');
        $('#produto-nome').keydown(function () {
            var max = 60;
            var len = $(this).val().length;
            if (len >= max) {
                $('#characterLeft').text('Quantidade máxima de caracteres');
                $('#characterLeft').addClass('red');
                $('#btnSubmit').addClass('disabled');
            }
            else {
                var ch = max - len;
                $('#characterLeft').text(ch + ' caracteres restantes');
                $('#btnSubmit').removeClass('disabled');
                $('#characterLeft').removeClass('red');
            }
        });
    });
</script>

<div class="produto-form">

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
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?= ""//$form->field($model, 'nome')->textInput(['maxlength' => true])->hint("O Nome deve conter no máximo 60 caracteres.") ?>
                <?= $form->field($model, 'nome')->textInput(['maxlength' => true])->hint("O Nome deve conter no máximo 60 caracteres.",['id'=>'characterLeft']) ?>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class=" col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <?= $form->field($model, 'peso')->textInput()->hint("Separe as casa decimais com ponto.")->label("Peso (Kg)") ?>
            </div>
            <div class=" col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <?= $form->field($model, 'altura')->textInput()->label("Altura (cm)") ?>
            </div>
            <div class=" col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <?= $form->field($model, 'largura')->textInput()->label("Largura (cm)") ?>
            </div>
            <div class=" col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <?= $form->field($model, 'profundidade')->textInput()->label("Profundidade (cm)") ?>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <?= $form->field($model, 'codigo_global')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <?= $form->field($model, 'codigo_similar')->textarea(['rows' => 2])->hint("Separar por < br>") ?>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <?= $form->field($model, 'codigo_montadora')->textInput(['maxlength' => true])->label("NCM") ?>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <?= $form->field($model, 'codigo_fabricante')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?= $form->field($model, 'fabricante_id')->widget(Select2::className(), [
                    'data' => ArrayHelper::map(
                        Fabricante::find()->orderBy(['fabricante.nome' => SORT_ASC])->all(),
                        'id',
                        'nome'
                    ),
                    'options' => ['placeholder' => 'Selecione um Fabricante']
                ]) ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?= $form->field($model, 'subcategoria_id')->widget(Select2::className(), [
                    'data' => ArrayHelper::map(
                        Subcategoria::find()->orderBy(['subcategoria.nome' => SORT_ASC])->all(),
                        'id',
                        'nome'
                    ),
                    'options' => ['placeholder' => 'Selecione uma Subcategoria']
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
        <hr>
        <div class="row">
            <div class=" col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <?= $form->field($model, 'descricao')->widget(Redactor::className()) ?>
            </div>
            <div class=" col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <?= $form->field($model, 'multiplicador')->textInput()->label("Multiplicador") ?>
            </div>
        </div>

        <div class="row">
            <div class=" col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <?= $form->field($model, 'video')->textInput()->label('Vídeo') ?>
            </div>
            <div class=" col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <?= $form->field($model, 'codigo_barras')->textInput()->label('Código de Barras') ?>
            </div>
        </div>

        <div class="row">
            <div class=" col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <?= $form->field($model, 'cest')->textInput()->label('CEST') ?>
            </div>
            <div class=" col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <?= $form->field($model, 'ipi')->textInput()->label('IPI') ?>
            </div>
        </div>

        <!-- Novos Campos -->
		
		<div class="row">
            <div class="col-md-12">
                <h2><b>Estoque</b></h2>
            </div>
        </div>
		
        <div class="row">
            <div class="col-xs-12">
                <?php
                $filial = $model->filial_id ? Filial::findOne($model->filial_id)->nome : null;
                echo  $form->field($model, 'filial_id')->widget(Select2::className(), [
                    'initValueText' => $filial,
                    'pluginOptions' => ['allowClear' => true,
                        'minimumInputLength' => 3,
                        'ajax' => [
                            'url' => Url::to(['produto-filial/get-filial']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                    ],
                    'options' => ['placeholder' => 'Selecione uma Filial']
                ])->label("Escolha uma filial:")
                ?>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                <?= $form->field($model, 'quantidade')->textInput() ?>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                <?= $form->field($model, 'status_b2w')->checkbox() ?>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                <?php
                echo $form->field($model, 'envio')->widget(Select2::className(), [
                    'options' => ['placeholder' => 'Selecione uma forma de envio'],
                    'data' => array(
                        1           => 'Mercado Envios',
                        2           => 'Não especificado',
                    ),
                ])->label("Escolha uma forma de envio:")
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                <?= $form->field($model, 'valor')->textInput() ?>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                <?= $form->field($model, 'valor_cnpj')->textInput() ?>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                <?= $form->field($model, 'promocao')->checkbox() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h2><b>Imagens</b></h2>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-1 col-md-2 col-sm-4 col-xs-4">
                <?= $form->field($model, 'ordem')->dropDownList(['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-4 col-sm-12 col-xs-12">
                <?= $form->field($model, 'imagem',
                    ['options' => ['class' => 'form-group col-lg-12 col-md-12 col-sm-12']])->widget(FileInput::className(),
                    [
                        'options' => ['accept' => 'image/jpeg, image/png'],
                        'pluginOptions' => [
                            'showUpload' => false,
                            'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                            'browseLabel' => 'Selecione uma Imagem',
                            'initialPreview' => $model->getImg(['class' => 'file-preview-image', 'style' => 'width: 100%']),
                            'overwriteInitial' => true
                        ]
                    ]); ?>
            </div>

            <div class="col-lg-6 col-md-4 col-sm-12 col-xs-12">
                <?= $form->field($model, 'imagem_sem_logo',
                    ['options' => ['class' => 'form-group col-lg-12 col-md-12 col-sm-12']])->widget(FileInput::className(),
                    [
                        'options' => ['accept' => 'image/jpeg, image/png'],
                        'pluginOptions' => [
                            'showUpload' => false,
                            'browseIcon' => '<i class="glyphicon glyphicon-picture"></i> ',
                            'browseLabel' => 'Subir Imagem Sem Logo',
                            'initialPreview' => $model->getImg(['class' => 'file-preview-image', 'style' => 'width: 100%'],false),
                            'overwriteInitial' => true
                        ]
                    ]); ?>
            </div>
        </div>

        <div class="form-group pull-left">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Criar Produto') :
                Yii::t('app', 'Salvar Alterações'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
            <?php if(!$model->isNewRecord) {?>
                <?php $urlml = "atualizarml?id=".$model->id; ?>
                <a class="btn  btn-warning" id="sinc-ml2" href="<?php echo $urlml; ?>">
                    <i class="fa fa-shopping-cart" aria-hidden="true" ></i> Atualizar (Mercado Livre)
                </a>
            <?php } ?>
        </div>

        <div class="form-group pull-left">
            <?php
            if(!$model->isNewRecord) {
                ?>
                <div class="col-lg-12 col-md-12">
                    <?php //$urlml = "updateml?id=".$model->id."&acao=1"; ?>
                    <!--<a class="btn  btn-warning" id="sinc-ml" href="<?php //echo $urlml; ?>">
                	<i class="fa fa-shopping-cart" aria-hidden="true" ></i> Entrega Mercado envios
                </a>-->
                    <?php //$urlml = "updateml?id=".$model->id."&acao=2"; ?>
                    <!--<a class="btn  btn-danger" id="sinc-ml2" href="<?php //echo $urlml; ?>">
                	<i class="fa fa-shopping-cart" aria-hidden="true" ></i> Entrega a combinar
                </a>-->
                    <?php $urlml = "duplicarproduto?id=".$model->id; ?>
                    <a class="btn  btn-warning" id="sinc-ml2" href="<?php echo $urlml; ?>">
                        <i class="fa fa-shopping-cart" aria-hidden="true" ></i> Duplicar Produto
                    </a>
                    <?php $urlml = "criaalteraomie?id=".$model->id; ?>
                    <a class="btn  btn-info" id="sinc-ml2" href="<?php echo $urlml; ?>">
                        <i class="fa fa-shopping-cart" aria-hidden="true" ></i> Sincronizar Omie
                    </a>
                </div>
            <?php } ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>


</div>

<script language="JavaScript">

    document.getElementById('produto-valor').onkeyup = function(evt){
        return formatarMoeda(this);
    }

    document.getElementById('produto-valor_cnpj').onkeyup = function(evt){
        return formatarMoeda(this);
    }

    function formatarMoeda(i) {
        var v = i.value.replace(/\D/g,'');
        v = (v/100).toFixed(2) + '';
        v = v.replace(".", ".");
        v = v.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
        v = v.replace(/(\d)(\d{3}),/g, "$1.$2,");
        i.value = v;
    }


</script>





