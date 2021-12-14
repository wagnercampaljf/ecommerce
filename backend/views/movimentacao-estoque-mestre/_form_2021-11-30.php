<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use common\models\Filial;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoEstoqueMestre */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="movimentacao-estoque-mestre-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'descricao')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'e_autorizado')->checkbox() ?>
   
    <?= ''//$form->field($model, 'filial_origem_id')->textInput() ?>

    <?= ''//$form->field($model, 'filial_destino_id')->textInput() ?> 
    
    <div class="movimentacao-estoque-mestre-form">
        <?php
        $filial = $model->filial_origem_id ? Filial::findOne($model->filial_origem_id)->nome : null;
        echo  $form->field($model, 'filial_origem_id')->widget(Select2::className(), [
                'initValueText' => $filial,
            'pluginOptions' => ['allowClear' => true,
                'minimumInputLength' => 3,
                'ajax' => ['url' => Url::to(['produto-filial/get-filial']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
            ],
            'options' => ['placeholder' => 'Selecione uma Filial']
        ])->label("Filial de Origem:")
        ?>
    </div>
    <div class="movimentacao-estoque-mestre-form">
        <?php
        $filial = $model->filial_destino_id ? Filial::findOne($model->filial_destino_id)->nome : null;
        echo  $form->field($model, 'filial_destino_id')->widget(Select2::className(), [
                'initValueText' => $filial,
            'pluginOptions' => ['allowClear' => true,
                'minimumInputLength' => 3,
                'ajax' => ['url' => Url::to(['produto-filial/get-filial']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
            ],
            'options' => ['placeholder' => 'Selecione uma Filial']
        ])->label("Filial de Origem:")
        ?>
    </div>
    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
   
    <?php    
    if(!empty($dataProviderDetalhe)){
        echo GridView::widget([
        'dataProvider'=>$dataProviderDetalhe,
        'filterModel'=>$filterModelDetalhe,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'descricao:ntext',
            'produto_id',
            'salvo_em',
            'salvo_por',
            // 'quantidade',
            // 'id_ajuste_omie_entrada',
            // 'id_ajuste_omie_saida',
            // 'movimentacao_estoque_mestre_id',

            [
                'class' => 'yii\grid\ActionColumn',  
                'template' => '{update}{delete}',
                        'buttons' => [
                            'update' => function ($url, $dataProviderDetalhe) {
                                $url = "../movimentacao-estoque-detalhe/update?id=" . $dataProviderDetalhe->id;
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>  ', $url, ['title' => Yii::t('yii', 'Alterar'),]);
                            },
                            'delete' => function ($url, $dataProviderDetalhe) {
                                $url = "../movimentacao-estoque-detalhe/delete?id=" . $dataProviderDetalhe->id;
                                return Html::a('  <span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('yii', 'Excluir'), 'data-confirm' => 'Confirma a exclusão deste item?']);
                            }
                        ]             
            ],
        ],
    ]);}
    echo '<div class="col-md-12">
    <div class="form-group">
        ' . Html::a('Criar Movimentação de estoque Detalhe', ['movimentacao-estoque-detalhe/create', 'movimentacao_estoque_mestre_id' => $model->id], ['class' => 'btn btn-success']) .
            '</div>
</div>';

 ?>

</div>
