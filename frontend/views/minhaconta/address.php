<?php

use kartik\select2\Select2;
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
<div class="tab-pane active col-md-9 col-sm-12 endereco-empresa-update">
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
//                var_dump(ArrayHelper::getValue($comprador, 'cpf'));
//                var_dump($empresa);
//                var_dump(ArrayHelper::getValue($comprador, 'cpf'));
//                var_dump($comprador->validate());
//                var_dump($model);
                if (!$comprador->validate() && ArrayHelper::getValue($empresa, 'juridica') == false) {
                    echo '<div class="clearfix">';
                    echo $form->field($comprador,'cpf')->textInput(['maxlength' => 14])->hint("Somente números")->label("CPF *");
                    echo '</div>';
                }

		echo $form->field($empresa, 'telefone')->textInput(['maxlength' => 20])->label("Telefone *");
            ?>
                <div class="clearfix">
                    <?= $form->field($model, 'cep', ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->textInput([
                        'maxlength' => 10,
                        'id' => 'cep-comprador',
                        'onkeyup' => 'javascript:getEndereco(this.value,"enderecoempresa");',
                        'type' => 'number'
                    ]) ?><br>
                    <i class="fa fa-spinner fa-spin" style="display: none;padding-top: 17px"></i>
                </div>
            </div>

            <div class="row">
                <?= $form->field($model, 'logradouro')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'numero',
                    ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->textInput(['maxlength' => true, 'type' => 'number']) ?>
                <?= $form->field($model, 'complemento',
                    ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->textInput(['maxlength' => true]) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'cidade')->textInput(['value' => $model->cidade])->label("Cidade") ?>
                <?= $form->field($model, 'bairro')->textInput(['maxlength' => true]) ?>


            </div>
            <div class="row">
                <?= $form->field($model, 'referencia',
                    ['options' => ['class' => 'form-group col-lg-12 col-md-12 col-sm-12 col-xs-12']])->textarea(['maxlength' => true]) ?>

            </div>
            <div class="form-group text-right row">
                <?= $form->field($model, 'cidade_id')->hiddenInput()->label('') ?>
                <?= $form->field($model, 'estado')->hiddenInput()->label("") ?>
                <?= Html::submitButton(Yii::t('app', 'Alterar'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
