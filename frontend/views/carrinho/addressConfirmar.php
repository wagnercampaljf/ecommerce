<?php

use common\models\Cidade;
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
                    echo $form->field($model, 'cep', ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->textInput([
                        'maxlength' => 10,
                        'id' => 'cep-comprador',
                        'required' => true,
                        'onkeyup' => 'javascript:getEndereco(this.value,"enderecoempresa");',
                        'type' => 'number'
                     ]);
                    ?> 

                    <?php
                    // echo $form->field($model, 'cep', ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->textInput([
                    //     'maxlength' => 10,
                    //     'id' => 'cep-comprador',
                    //     'required' => true,
                    //     'onkeyup' => 'javascript:pesquisacep(this.value,"enderecoempresa");',
                    //     'type' => 'number'
                    // ]);
                ?>
                
            </div>

            <div class="row">
                <?php
                $cpf = str_replace(ArrayHelper::getValue($comprador, 'cpf'), " ", "");

                if (($comprador->cpf == null or str_replace(" ", "", $comprador->cpf) == "") && ArrayHelper::getValue($empresa, 'juridica') == false) {
                    echo '<div class="clearfix" name=teste>';
                    echo $form->field($comprador, 'cpf', ['options' => ['class' => 'form-group col-lg-4 col-md-4 col-sm-4 col-xs-12']])->textInput(['maxlength' => 14, 'type' => 'tel', 'required' => true,])->hint("Somente números")->label("CPF *");
                    echo '</div>';
                }                

                echo $form->field($model, 'logradouro')->textInput(['maxlength' => true, 'required' => true]);
                echo $form->field($model, 'numero', ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->textInput(['maxlength' => true, 'type' => 'number', 'required' => true,]);
                echo $form->field(
                    $model,
                    'complemento',
                    ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']]
                )->textInput(['maxlength' => true]);
                ?>
            </div>
            <div class="row">
                <?php

                $cidade  = Cidade::find()->orderBy('nome',SORT_ASC)->all();
                //   $cidade = Yii::$app->db->createCommand('SELECT nome,id FROM cidade');

                  $list =  ArrayHelper::map($cidade,'id','nome');             

                

                echo $form->field($model, 'bairro')->textInput(['maxlength' => true]);

                
                echo $form->field($model, 'cidade', ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->dropDownList(
                    $list,
                    ['prompt'=>"Escolha a cidade"]
                );
                
               // echo $form->field($model, 'cidade', ['options' => ['class' => 'form-group col-lg-3 col-md-3 col-sm-3 col-xs-12']])->textInput(['value' => $model->cidade, 'required' => true])->label("Cidade");
                
                ?>
            </div>
            <div class="row">
                <?php
                echo $form->field($empresa, 'telefone', ['options' => ['class' => 'form-group col-lg-4 col-md-4 col-sm-4 col-xs-12']])->textInput([
                    'maxlength' => 18,
                    'data-mask' => '00000000000',
                    'type' => 'tel',
                    'required' => true,
                ])->label("Telefone *");
                ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'referencia', ['options' => ['class' => 'form-group col-lg-12 col-md-12 col-sm-12 col-xs-12']])->textarea(['maxlength' => true]) ?>
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
