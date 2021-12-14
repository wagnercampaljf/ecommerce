<?php

use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\EnderecoEmpresa */
/* @var $form yii\widgets\ActiveForm */
/* @var $comprador frontend\controllers\CompradorController */
/* @var $empresa common\models\Empresa */

$this->title = 'Meu Endereço';
$this->params['active'] = 'address';
$this->params['breadcrumbs'][] = ['label' => 'Minha Conta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-pane active col-md-12 col-sm-12 endereco-empresa-update">
    <div class="panel panel-primary comprador-view endereco-empresa-form">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'options' => ['class' => 'form-group col-lg-6 col-md-6 col-sm-6 col-xs-12']
                ],
                'errorSummaryCssClass' => 'alert alert-danger error-summary'
            ]); ?>
            <div class="row">
                <?php
                    $cpf = str_replace(ArrayHelper::getValue($comprador, 'cpf'), " ", "");

                    if (($comprador->cpf == null or str_replace(" ","",$comprador->cpf) == "") && ArrayHelper::getValue($empresa, 'juridica') == false) {
                        echo '<div class="clearfix" name=teste>';
                        echo $form->field($comprador,'cpf',['options' => ['class' => 'form-group col-lg-4 col-md-4 col-sm-4 col-xs-12']])->textInput(['maxlength' => 14])->hint("Somente números")->label("CPF *");
                        echo '</div>';
                    }
                    
                    echo $form->field($empresa, 'telefone',['options' => ['class' => 'form-group col-lg-4 col-md-4 col-sm-4 col-xs-12']])->textInput(['maxlength' => true])->label("Telefone *");
                ?>
            </div>
            <div class="row">
                <div class="clearfix">
                    <?= $form->field($model, 'cep', ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->textInput([
                        'maxlength' => 10,
                        'id' => 'cep-comprador',
                        'onkeyup' => 'javascript:getEndereco(this.value,"enderecoempresa");',
                        'type' => 'number'
                    ]) ?><br>
                    <i class="fa fa-spinner fa-spin" style="display: none;padding-top: 17px"></i>
                </div>
                <?= $form->field($model, 'logradouro')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'numero',['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->textInput(['maxlength' => true, 'type' => 'number', 'data-mask'=>'(00) 00000-0000' ]) ?>
                <?= $form->field($model, 'complemento',
                    ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->textInput(['maxlength' => true]) ?>
            </div>
            <div class="row">
				<?= $form->field($model, 'bairro')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'cidade')->textInput(['value' => $model->cidade,])->label("Cidade") ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'referencia',['options' => ['class' => 'form-group col-lg-12 col-md-12 col-sm-12 col-xs-12']])->textarea(['maxlength' => true]) ?>
            </div>
            <div class="form-group text-right row">
                <?= $form->field($model, 'cidade_id')->hiddenInput()->label('') ?>
                <?= $form->field($model, 'estado')->hiddenInput()->label("") ?>
                <?= Html::submitButton(Yii::t('app', 'Confirmar Endereço'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
