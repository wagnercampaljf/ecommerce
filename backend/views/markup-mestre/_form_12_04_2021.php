<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\common\models\MarkupMestre */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="markup-mestre-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-12">
        <?= $form->field($model, 'descricao')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'data_inicio')->textInput() ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'margem_padrao')->textInput() ?>
    </div>
    <div class="col-md-12">
        <?= $form->field($model, 'e_margem_absoluta_padrao')->checkbox() ?>
    </div>
    <div class="col-md-12">
        <?= $form->field($model, 'e_markup_padrao')->checkbox() ?>
    </div>
    <div class="col-md-12">
        <?= $form->field($model, 'observacao')->textarea(['rows' => 6]) ?>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

    <div class="col-md-12">

        <?php if (isset($dataProviderDetalhe)) {

            echo GridView::widget([
                'dataProvider' => $dataProviderDetalhe,
                'filterModel' => $searchModelDetalhe,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'id',
                    'margem',
                    'e_margem_absoluta:boolean',
                    'valor_minimo',
                    'valor_maximo',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update}{delete}',
                        'buttons' => [
                            'update' => function ($url, $dataProviderDetalhe) {
                                $url = "../markup-detalhe/update?id=" . $dataProviderDetalhe->id;
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>  ', $url, ['title' => Yii::t('yii', 'Alterar'),]);
                            },
                            'delete' => function ($url, $dataProviderDetalhe) {
                                $url = "../markup-detalhe/delete?id=" . $dataProviderDetalhe->id;
                                return Html::a('  <span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('yii', 'Excluir'), 'data-confirm' => 'Confirma a exclusão deste item?']);
                            }
                        ]
                    ],
                ]
            ]);

            echo '<div class="col-md-12">
        <div class="form-group">
            ' . Html::a('Criar Markup Detalhe', ['markup-detalhe/create', 'markup_mestre_id' => $model->id], ['class' => 'btn btn-success']) .
                '</div>
    </div>';
        } ?>
    </div>


</div>
</div>