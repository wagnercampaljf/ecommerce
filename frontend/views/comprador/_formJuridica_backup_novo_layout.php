<?php

use yii\helpers\ArrayHelper;
use yii\helpers\html;
use yii\widgets\ActiveForm;
use common\models\TipoEmpresa;


/* @var $this yii\web\View */
/* @var $comprador common\models\Comprador */
/* @var $form yii\widgets\ActiveForm */
/* @var $this yii\web\View */
/* @var $model common\models\Empresa */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comprador-form comprador-form-juridica clearfix">
    <h2 class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Jurídica</h2>

    <?php $form = ActiveForm::begin(); ?>
    <!--    EMAIL E SENHA -->
    <h3 class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Dados login</h3>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
            <?= $form->field($comprador,
                'email')->input('email')->textInput(['maxlength' => 255])->label("Email / Login *")->hint("Ex: joao@yahoo.com.br") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
            <?= $form->field($comprador,
                'password')->passwordInput(['maxlength' => 255])->hint('Senha de conter no minimo 6 caracteres de A-Z a-z 0-9')->label("Senha *") ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
            <?= $form->field($comprador,
                'repeat_password')->passwordInput(['maxlength' => 255])->label("Repetir Senha *") ?>
        </div>
    </div>

    <!--   DADOS DA COMPRADOR-->
    <h3>Dados representante</h3>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($comprador, 'nome')->textInput(['maxlength' => 150])->label("Nome do representante *") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($comprador,
                'cpf')->textInput(['maxlength' => 14])->hint("Somente números")->label("CPF *") ?>
        </div>

    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($comprador, 'cargo')->textInput(['maxlength' => 50]) ?>
        </div>
    </div>
    <!--    DADOS EMPRESA-->
    <h3>Dados empresa</h3>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($empresa, 'nome')->textInput(['maxlength' => 150])->label("Nome da empresa *") ?>
            <?= $form->field($empresa, 'razao')->textInput(['maxlength' => 150])->label("Razão Social *") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix ">
            <?= $form->field($empresa, 'documento')->textInput(['maxlength' => 18])->label("CNPJ *") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($empresa,
                'email')->input('email')->textInput(['maxlength' => 150])->label("Email da empresa")->hint("Ex: contato@empresa.com.br") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?php
            $tipoempresa = TipoEmpresa::find()->getTipoEmpresaComprador()->andWhere(['juridica' => "t"])->all();
            $tipoempresa = ArrayHelper::map($tipoempresa, 'id', 'nome');
            echo $form->field($empresa, 'id_tipo_empresa')->dropDownList(
                $tipoempresa,
                [
                    'class' => 'form-control select2',
                    'prompt' => 'Tipo de Empresa',
                    'id' => 'select_tipoempresa',
                ])->label("Tipo empresa *");
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2 clearfix">
            <?= $form->field($empresa, 'telefone')->textInput(['maxlength' => 20])->label("Telefone *") ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2 clearfix">
            <?= $form->field($empresa, 'telefone_alternativo')->textInput(['maxlength' => 20]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($empresa, 'observacao')->textInput(['maxlength' => 400]) ?>
        </div>
    </div>
    <!--    DADOS ENDERECO EMPRESA-->
    <h3>Endereço de Entrega</h3>

    <div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2 clearfix form-inline">
            <?= $form->field($EnderecoEmpresa, 'cep')->textInput([
                'maxlength' => 9,
                'id' => 'cep-comprador',
                'onkeyup' => 'javascript:getEndereco(this.value,"enderecoempresa");'
            ])->label("CEP *") ?>
            <i class="fa fa-spinner fa-spin" style="display: none"></i>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($EnderecoEmpresa, 'logradouro')->textInput(['maxlength' => 255])->label("Logradouro *") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($EnderecoEmpresa, 'bairro')->textInput()->label("Bairro *") ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($EnderecoEmpresa, 'cidade')->textInput(['disabled' => false])->label("Cidade *") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($EnderecoEmpresa, 'estado')->textInput()->label("Estado *") ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($EnderecoEmpresa, 'cidade_id')->hiddenInput()->label('') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2 clearfix">
            <?= $form->field($EnderecoEmpresa, 'numero')->textInput(['maxlength' => 50])->label("Número *") ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2 clearfix">
            <?= $form->field($EnderecoEmpresa, 'complemento')->textInput(['maxlength' => 50]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($EnderecoEmpresa, 'referencia')->textInput(['maxlength' => 255])->label("Referência")->hint("Lembre-se de deixar claro o local de recebimento como blocos, apto, fundos, etc. Caso o entregador não encontre o lugar da entrega, outro frete poderá ser cobrado.  ") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            * Ao clicar Cadastrar, você reconhece que concorda com os <a
                href="<?= yii::$app->urlManager->baseUrl . "/site/politicas" ?>" target="_blank">Termos de Uso</a> e que
            leu e entendeu as <a href="<?= yii::$app->urlManager->baseUrl . '/site/politicas' ?>" target="_blank">Políticas
                de Privacidade</a> do Peça Agora. <br><br>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <?= Html::submitButton('Cadastrar', [
                'class' => 'btn btn-success col-xs-4 col-sm-3 col-md-3 col-lg-2 clearfix',
                'style' => 'margin: 15px;'
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

