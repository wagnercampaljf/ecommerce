<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Filial;
use common\models\Produto;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\ProdutoFilial;

/* @var $this yii\web\View */
/* @var $model backend\models\PedidoCompra */

$this->title = 'Autorização Pedido';
$this->params['breadcrumbs'][] = ['label' => 'Pedido Compras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-compra-view">

    <h1><?= Html::encode('Pedido ' . $model->id) ?></h1>
    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-md-12">
                <?= $form->field($model, 'descricao')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'data')->textInput(['value' => date('d/m/Y')]) ?></div>
            <div class="col-md-3">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-3">
                <?= $form->field($model, 'observacao')->textInput(['maxlength' => true]); ?></div>
            <div class="col-md-4">
                <?php
                $filial = $model->filial_id ? Filial::findOne($model->filial_id)->nome : null;
                echo  $form->field($model, 'filial_id')->widget(Select2::className(), [
                    'initValueText' => $filial,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'ajax' => [
                            'url' => Url::to(['produto-filial/get-filial']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                    ],
                    'options' => ['placeholder' => 'Selecione uma Filial']
                ])->label("Filial:")
                ?></div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

    <?php $form = ActiveForm::begin(['action' => '../pedido-compra-produto-filial/validar-pedido', 'method' => 'post',]); ?>
    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'quantidade',
            'valor_compra',
            'valor_venda',
            [
                'attribute' =>   '',
                'header' => 'Produto',
                'content' => function ($dataProvider) {
                    $filial = ProdutoFilial::find()->andWhere(['=', 'id', $dataProvider->produto_filial_id])->one();
                    $produto = Produto::find()->andWhere(['=', 'id', $filial->produto_id])->one();
                    return $produto['nome'];
                }
            ],
            // 'e_verificado:boolean',
            // [
            //     'class' => 'yii\grid\CheckboxColumn',
            // ],
            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'template' => '{update}{delete}',
            //     'buttons' => [
            //         'update' => function ($url, $dataProvider) {
            //             $url = "../pedido-compra-produto-filial/update?id=" . $dataProvider->id;
            //             return Html::a('<span class="glyphicon glyphicon-pencil"></span>  ', $url, ['title' => Yii::t('yii', 'Alterar'),]);
            //         },
            //         'delete' => function ($url, $dataProvider) {
            //             $url = "../pedido-compra-produto-filial/delete?id=" . $dataProvider->id;
            //             return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('yii', 'Excluir'), 'data-confirm' => 'Confirma a exclusão deste item?', 'data-method' => 'post']);
            //         },
            //     ],
            // ],
        ]
    ]);

    // if (empty($model->corpo_email)) {
    //     echo Html::a('Enviar Pedido', '../pedido-compra-produto-filial/autorizar-email?id=' . $model->id, ['class' => 'btn btn-success']);
    // } else {
    //     echo Html::submitButton('Validar Pedido', ['class' => 'btn btn-warning']);
    // }
    ?>
    <?php ActiveForm::end(); ?>

</div>