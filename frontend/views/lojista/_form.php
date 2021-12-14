<?php

use yii\helpers\ArrayHelper;
use yii\helpers\html;
use yii\widgets\ActiveForm;
use common\models\TipoEmpresa;

/* @var $this yii\web\View */
/* @var $lojista common\models\Lojista */
/* @var $usuario common\models\Usuario */
/* @var $filial common\models\Filial */
/* @var $bancos common\models\Banco[] */
/* @var $enderecoFilial common\models\EnderecoFilial */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="comprador-form comprador-form-juridica">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary([$lojista, $usuario, $filial, $enderecoFilial]) ?>
    <!--    EMAIL E SENHA -->
    <h3>Dados login</h3>

    <div class="row">
        <div class="col-xs-6">
            <?= $form->field($usuario,
                'email')->input('email')->textInput(['maxlength' => 255])->label("Login / Email *")->hint("Ex: joao@yahoo.com.br") ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-3">
            <?= $form->field($usuario,
                'password')->passwordInput(['maxlength' => 255])->hint('Senha de conter no minimo 6 caracteres de A-Za-z0-9')->label("Senha *") ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($usuario,
                'repeat_password')->passwordInput(['maxlength' => 255])->label("Repetir Senha *") ?>
        </div>
    </div>

    <!--   DADOS DA COMPRADOR-->
    <h3>Dados representante</h3>

    <div class="row">
        <div class="col-xs-6 clearfix">
            <?= $form->field($usuario, 'nome')->textInput(['maxlength' => 150])->label("Nome do representante *") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3 clearfix">
            <?= $form->field($usuario,
                'cpf')->textInput(['maxlength' => 14])->hint("Somente números")->label("CPF *") ?>
        </div>
        <div class="col-xs-3 clearfix">

        </div>
    </div>
    <div class="row">
        <div class="col-xs-6 clearfix">
            <?= $form->field($usuario, 'cargo')->textInput(['maxlength' => 50]) ?>
        </div>
    </div>

    <!--    DADOS EMPRESA-->
    <h3>Dados empresa</h3>

    <?= $form->field(
        $filial,
        'token_moip',
        ['template' => '{input}'])
        ->hiddenInput(['value' => Yii::$app->session['moipAccountId']]) ?>

    <div class="row">
        <div class="col-xs-6 clearfix">
            <?= $form->field($filial, 'nome')->textInput(['maxlength' => 150])->label("Nome da empresa *") ?>
            <?= $form->field($filial, 'razao')->textInput(['maxlength' => 150])->label("Razão Social *") ?>
        </div>
    </div>
    <div class="row">

        <div class="col-xs-3 clearfix">
            <?= $form->field($filial,
                'documento')->textInput(['maxlength' => 18])->label("CNPJ *")->hint("Somente números") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3 clearfix">
            <?php
            $tipoempresa = TipoEmpresa::find()->getTipoEmpresaLojista()->andWhere(['juridica' => "t"])->all();
            $tipoempresa = ArrayHelper::map($tipoempresa, 'id', 'nome');
            echo $form->field($filial, 'id_tipo_empresa')->dropDownList(
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
        <div class="col-xs-3 clearfix">
            <?= $form->field($filial,
                'telefone')->textInput(['maxlength' => 20])->label("Telefone *") ?>
        </div>
        <div class="col-xs-3 clearfix">
            <?= $form->field($filial,
                'telefone_alternativo')->textInput(['maxlength' => 20])->label("Telefone Alternativo") ?>
        </div>
    </div>

    <!--    DADOS ENDERECO EMPRESA-->
    <h3>Dados endereço empresa</h3>

    <div class="row">
        <div class="col-xs-3 clearfix form-inline">
            <?= $form->field($enderecoFilial, 'cep')->textInput([
                'maxlength' => 10,
                'id' => 'cep-comprador',
                'onkeyup' => 'javascript:getEndereco(this.value,"enderecofilial");'
            ])->label("CEP *") ?>
            <i class="fa fa-spinner fa-spin" style="display: none"></i>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6 clearfix">
            <?= $form->field($enderecoFilial, 'logradouro')->textInput(['maxlength' => 255])->label("Logradouro *") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3 clearfix">
            <?= $form->field($enderecoFilial, 'bairro')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-3 clearfix">
            <?= $form->field($enderecoFilial, 'cidade')->textInput(['disabled' => true]) ?>
        </div>
        <div class="col-xs-3 clearfix">
            <?= $form->field($enderecoFilial, 'estado')->textInput() ?>
        </div>

        <div class="col-xs-3 clearfix">
            <?= $form->field($enderecoFilial, 'cidade_id')->hiddenInput()->label('') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3 clearfix">
            <?= $form->field($enderecoFilial, 'numero')->textInput(['maxlength' => 50])->label("Número *") ?>
        </div>
        <div class="col-xs-3 clearfix">
            <?= $form->field($enderecoFilial, 'complemento')->textInput(['maxlength' => 50]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6 clearfix">
            <?= $form->field($enderecoFilial, 'referencia')->textInput(['maxlength' => 255])->label("Referência") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6 clearfix">
            * Ao clicar Cadastrar, você reconhece que concorda com os <a href="<?= yii::$app->urlManager->baseUrl."/site/politicas"?>" target="_blank" >Termos de Uso</a> e que leu e entendeu as <a href="<?= yii::$app->urlManager->baseUrl.'/site/politicas'?>" target="_blank">Políticas de Privacidade</a> do Peça Agora. <br><br>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Cadastrar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
