<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoEstoqueMestre */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="movimentacao-estoque-mestre-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'descricao')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'e_autorizado')->checkbox() ?>

    <?= $form->field($model, 'autorizado_por')->textInput() ?>

    <?= $form->field($model, 'salvo_em')->textInput() ?>

    <?= $form->field($model, 'salvo_por')->textInput() ?>

    <?= $form->field($model, 'filial_origem_id')->textInput() ?>

    <?= $form->field($model, 'filial_destino_id')->textInput() ?>

    <?= $form->field($model, 'codigo_remessa_omie')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProviderDetalhe,
        'filterModel' => $filterModelDetalhe,
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
    ]);
    echo '<div class="col-md-12">
    <div class="form-group">
        ' . Html::a('Criar Movimentação de estoque Detalhe', ['movimentacao-estoque-detalhe/create', 'movimentacao_estoque_mestre_id' => $model->id], ['class' => 'btn btn-success']) .
            '</div>
</div>';
     ?>

</div>
