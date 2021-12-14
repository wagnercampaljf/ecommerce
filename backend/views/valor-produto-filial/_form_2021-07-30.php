<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ProdutoFilial;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Produto;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\ValorProdutoFilial */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="valor-produto-filial-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'options' => [
                'class' => 'form-group',
            ]
        ]
    ]); ?>

    <?= ""//$form->field($model, 'id')->textInput() ?>
    
    <?= ""
        /*$form->field($model, 'produto_filial_id')->widget(Select2::className(), [
        'data' => ArrayHelper::map(ProdutoFilial::  find()
                                                    ->select(['id'])
                                                    //->joinWith(['filial'])
                                                    //->orderBy(['filial.nome' => SORT_ASC])
                                                    ->all(), 'id', 'id'),
        'options' => ['placeholder' => 'Selecione uma filial']])->label('Filial - Produto') */
    ?>
    
    <!-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <?php 
            /*$produto = null;
            echo  $form->field($model, 'produto_filial_id')->widget(Select2::className(), [
               'initValueText' => $produto,
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => Url::to(['valor-produto-filial/get-produto']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                ],
                'options' => ['placeholder' => 'Selecione um Produto']
            ])->label("Produto:")*/
        ?>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <?php 
            /*$filial = null;
            echo  $form->field($model, 'id')->widget(Select2::className(), [
                'initValueText' => $filial,
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => Url::to(['valor-produto-filial/get-filial']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                ],
                'options' => ['placeholder' => 'Selecione um Produto']
            ])->label("Filial:") */
        ?>
    </div> -->
    
        <?php
    $js = <<< JS
       $('#valorprodutofilial-produto_filial_id').change(
           function(){
               var produto_filial_id = $(this).val();

               $.ajax(
        {
        type:"GET",url:baseUrl+"/produto-filial/get-e-valor-bloqueado",
        data:{id:produto_filial_id},
        success:function(retorno)
        {
                       var retorno = $.parseJSON(retorno);
                       document.getElementById('valor').readOnly = retorno.e_valor_bloqueado;
                       document.getElementById('valor_cnpj').readOnly = retorno.e_valor_bloqueado;
                       document.getElementById('valor_compra').readOnly = retorno.e_valor_bloqueado;
                       document.getElementById('e_valor_bloqueado_bla').checked = retorno.e_valor_bloqueado;
        },
        })
           }
       );
JS;

    $this->registerJs($js);
    ?>

    <script>

        function desabilitar2(selecionado) {
            document.getElementById('valor').readOnly = selecionado;
            document.getElementById('valor_cnpj').readOnly = selecionado;
            document.getElementById('valor_compra').readOnly = selecionado;
        }
    </script>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <?php 
        $produto_filial = $model->produto_filial_id ? ProdutoFilial::findOne($model->produto_filial_id)->filial->nome ." - ". ProdutoFilial::findOne($model->produto_filial_id)->produto->nome. "(". ProdutoFilial::findOne($model->produto_filial_id)->produto->codigo_global.")" : null;
            echo  $form->field($model, 'produto_filial_id')->widget(Select2::className(), [
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
    
    <!-- <pre><?php //print_r($model); die;?>></pre> -->
    <?= ""
        /*$form->field($model, 'id')->widget(Select2::className(), [
        'data' => ArrayHelper::map(Filial::find()->select(['id','nome'])->orderBy(['filial.nome' => SORT_ASC])->all(), 'id', 'nome'),
        'options' => ['placeholder' => 'Selecione uma filial']])->label('Filial')*/ 
    ?>
    
    <?= ""
        /*$form->field($model, 'id')->widget(Select2::className(), [
        'data' => ArrayHelper::map(Produto::find()->select(['id','nome'])->orderBy(['produto.nome' => SORT_ASC])->all(), 'id', 'nome'),
        'options' => ['placeholder' => 'Selecione uma filial']])*/ 
    ?>

    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
		<?= $form->field($model, 'valor')->textInput(['id'=>"valor"]) ?>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
		<?= $form->field($model, 'valor_cnpj')->textInput(['id'=>"valor_cnpj"]) ?>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <?= $form->field($model, 'valor_compra')->textInput(['id'=>"valor_compra"]) ?>
    </div>
    
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
		<?= $form->field($model, 'promocao')->checkbox() ?>
    </div>

   <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
        <?= $form->field($model, 'e_valor_bloqueado')->checkbox(['id'=>'e_valor_bloqueado_bla','onclick'=>"desabilitar2(this.checked)",]) ?>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <?= $form->field($model, 'dias_expedicao')->textInput(['id'=>"dias_expedicao"]) ?>
    </div>
    
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="form-group">
     	   <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    	</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
