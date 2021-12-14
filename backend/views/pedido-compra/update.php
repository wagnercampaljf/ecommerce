<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use common\models\ProdutoFilial;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use backend\models\PedidoCompraProdutoFilial;

/* @var $this yii\web\View */
/* @var $model backend\models\PedidoCompra */

$this->title = 'Update Pedido Compra: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pedido Compras', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pedido-compra-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="pedido-compra-produto-filial-form">

        <?php $form = ActiveForm::begin();

        $modelPedidoProduto = PedidoCompraProdutoFilial::find()->andWhere('=', 'pedido_compra_id', $model->id);

        ?>

        <div class=" panel panel-default">
            <div class="panel-body">

                <?= $form->field($modelPedidoProduto, 'id')->hiddenInput()->label(false); ?>
                <?= $form->field($modelPedidoProduto, 'pedido_compra_id')->hiddenInput(['value' => Yii::$app->request->get('id')])->label(false); ?>

                <div class="col-md-12">
                    <?php $produto_filial = $modelPedidoProduto->produto_filial_id ? ProdutoFilial::findOne($modelPedidoProduto->produto_filial_id)->produto->nome . "(" . ProdutoFilial::findOne($modelPedidoProduto->produto_filial_id)->produto->codigo_global . ")" : null;
                    echo  $form->field($modelPedidoProduto, 'produto_filial_id')->widget(Select2::className(), [
                        'initValueText' => $produto_filial,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => Url::to(['pedido-compra-produto-filial/get-produto-filial']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                        ],
                        'options' => ['placeholder' => 'Selecione um Produto']
                    ])->label("Produto:");
                    ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelPedidoProduto, 'quantidade')->textInput() ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelPedidoProduto, 'valor_compra')->textInput() ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelPedidoProduto, 'valor_venda')->textInput() ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelPedidoProduto, 'observacao')->textarea() ?>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= Html::submitButton('Inserir Produto', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
                <?php
                ActiveForm::end();
                ?>
            </div>
        </div>

        <?= $this->render('_form', [
            'model' => $modelPedidoProduto,
        ]) ?>

    </div>