<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Produto;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoEstoqueDetalhe */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="movimentacao-estoque-detalhe-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'descricao')->textarea(['rows' => 6]) ?>

    
    <div class="movimentacao-estoque-detalhe-form">
    <?php
    $produto = $model->produto_id ? Produto::findOne($model->produto_id)->nome : null;
    echo  $form->field($model, 'produto_id')->widget(Select2::className(), [
            'initValueText' => $produto,
            'pluginOptions' => ['allowClear' => true,
                                'minimumInputLength' => 3,
                                'ajax' => ['url' => Url::to(['produto-filial/get-produto']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                            ],
                                ],
                                'options' => ['placeholder' => 'Selecione um Produto']
            ])->label("Produto:")
    ?>
    </div>    

    <?= $form->field($model, 'quantidade')->textInput() ?>

    <?= ''//$form->field($model, 'id_ajuste_omie_entrada')->textInput() ?>

    <?= ''//$form->field($model, 'id_ajuste_omie_saida')->textInput() ?>

    <?= $form->field($model, 'movimentacao_estoque_mestre_id')->hiddenInput(['readonly' => true, 'value' => $model->movimentacao_estoque_mestre_id])->label('') ?>
    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
