<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ProdutoFilial;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivreProdutoProdutoFilial */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pedido-mercado-livre-produto-produto-filial-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= ""//$form->field($model, 'pedido_mercado_livre_produto_id')->textInput() ?>

    <?= 
        $produto_filial = $model->produto_filial_id ?   ProdutoFilial::findOne($model->produto_filial_id)->filial->nome ." - ".
                                                        ProdutoFilial::findOne($model->produto_filial_id)->produto->nome . "(".
                                                        ProdutoFilial::findOne($model->produto_filial_id)->produto->codigo_global.")" : null;
            //echo "<pre>"; print_r($produto_filial); echo "</pre>"; die;
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
        //$form->field($model, 'produto_filial_id')->textInput() 
    ?>

    <?= $form->field($model, 'quantidade')->textInput() ?>
    
    <?= $form->field($model, 'valor')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
