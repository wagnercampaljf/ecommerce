<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\models\Produto;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\models\Filial;

/* @var $this yii\web\View */
/* @var $model common\models\ProdutoFilial */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="produto-filial-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12 text-right">
                    <?php if (!$model->isNewRecord) { ?>
                        <?php $urlml = "resetarml?id=" . $model->id; ?>
                        <a class="btn  btn-info" id="sinc-ml2" href="<?php echo $urlml; ?>">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i> Resetar (ML)
                        </a>
                    <?php } ?>
                </div>

                <div class="col-md-6">
                    <?php
                    $filial = $model->filial_id ? Filial::findOne($model->filial_id)->nome : null;
                    echo  $form->field($model, 'filial_id')->widget(Select2::class, [
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
                    ?>
                </div>

                <div class="col-md-6">
                    <?php
                    $produto = $model->produto_id ? Produto::findOne($model->produto_id)->nome : null;
                    echo  $form->field($model, 'produto_id')->widget(Select2::class, [
                        'initValueText' => $produto,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => Url::to(['produto-filial/get-produto']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                        ],
                        'options' => ['placeholder' => 'Selecione um Produto']
                    ])->label("Produto:")
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'quantidade')->textInput(['maxlength' => 4]) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'estoque_minimo')->textInput(['maxlength' => 4]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'status_b2w')->checkbox() ?>
                    <?= $form->field($model, 'atualizar_preco_mercado_livre')->checkbox() ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'envio')->textInput() ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'meli_id')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'meli_id_sem_juros')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'meli_id_full')->textInput(['maxlength' => true]) ?>
                </div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
	    	<?= $form->field($model, 'e_atualizar_quantidade_planilha')->checkbox() ?>
		</div>
   		 <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
	    	<?= $form->field($model, 'e_atualizar_preco_planilha')->checkbox() ?>
		</div>	
            </div>
        </div>
    </div>

    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

            <?php if (!$model->isNewRecord) { ?>
                <?php $urlml = "atualizarml?id=" . $model->id; ?>
                <a class="btn  btn-warning" id="sinc-ml2" href="<?php echo $urlml; ?>">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> Atualizar (Mercado Livre)
                </a>
            <?php } ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
