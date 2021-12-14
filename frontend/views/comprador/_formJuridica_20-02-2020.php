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
</br>
<div class="comprador-form comprador-form-juridica clearfix">
    

    <?php $form = ActiveForm::begin(); ?>
    
    <!--<div class="row">
        <div class="col-lg-3 "></div>
        <h2 class="col-xs-12 col-sm-6 col-md-6 col-lg-6">Jurídica</h2>
        <div class="col-lg-3 "></div>
	</div>
	<div class="row">
        <div class="col-lg-3 "></div>
        <h3 class="col-xs-12 col-sm-6 col-md-6 col-lg-6">Dados login</h3>
        <div class="col-lg-3 "></div>
	</div>-->

    <div class="row">
        <div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?= $form->field($comprador,
                'email')->input('email')->textInput(['maxlength' => 255])->label("Email / Login *")->hint("Ex: joao@yahoo.com.br") ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <?= $form->field($comprador,
                'password')->passwordInput(['maxlength' => 255])->hint('Senha de conter no minimo 6 caracteres de A-Z a-z 0-9')->label("Senha *") ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <?= $form->field($comprador,
                'repeat_password')->passwordInput(['maxlength' => 255])->label("Repetir Senha *") ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>

	<div class="row">
        <div class="col-lg-3 "></div>
            <!--   DADOS DA COMPRADOR-->
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            	<h3>Dados representante</h3>
            </div>
        <div class="col-lg-3 "></div>
	</div>

    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($comprador, 'nome')->textInput(['maxlength' => 150])->label("Nome do representante *") ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix">
            <?= $form->field($comprador, 'cpf')->textInput(['maxlength' => 14])->hint("Somente números")->label("CPF *") ?>
        </div>
		<div class="col-lg-3 "></div>
    </div>
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($comprador, 'cargo')->textInput(['maxlength' => 50]) ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    <!--    DADOS EMPRESA-->
    
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <h3>Dados empresa</h3>
        </div>
        <div class="col-lg-3 "></div>
    </div>

    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($empresa, 'nome')->textInput(['maxlength' => 150])->label("Nome da empresa *") ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($empresa, 'razao')->textInput(['maxlength' => 150])->label("Razão Social *") ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix ">
            <?= $form->field($empresa, 'documento')->textInput(['maxlength' => 18])->label("CNPJ *") ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($empresa, 'email')->input('email')->textInput(['maxlength' => 150])->label("Email da empresa")->hint("Ex: contato@empresa.com.br") ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
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
        <div class="col-lg-3 "></div>
    </div>
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 clearfix">
            <?= $form->field($empresa, 'telefone')->textInput(['maxlength' => 20])->label("Telefone *") ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 clearfix">
            <?= $form->field($empresa, 'telefone_alternativo')->textInput(['maxlength' => 20]) ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($empresa, 'observacao')->textInput(['maxlength' => 400]) ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    <!--    DADOS ENDERECO EMPRESA-->
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <h3>Endereço de Entrega</h3>
        </div>
        <div class="col-lg-3 "></div>
    </div>

    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 clearfix form-inline">
            <?= $form->field($EnderecoEmpresa, 'cep')->textInput([
                'maxlength' => 9,
                'id' => 'cep-comprador',
                'onkeyup' => 'javascript:getEndereco(this.value,"enderecoempresa");'
            ])->label("CEP *") ?>
            <i class="fa fa-spinner fa-spin" style="display: none"></i>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($EnderecoEmpresa, 'logradouro')->textInput(['maxlength' => 255])->label("Logradouro *") ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($EnderecoEmpresa, 'bairro')->textInput()->label("Bairro *") ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>

    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($EnderecoEmpresa, 'cidade')->textInput(['disabled' => false])->label("Cidade *") ?>
            <?= ""//$form->field($EnderecoEmpresa, 'cidade_id')->hiddenInput()->label(false) ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($EnderecoEmpresa, 'estado')->textInput()->label("Estado *") ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>

    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 clearfix">
            <?= $form->field($EnderecoEmpresa, 'numero')->textInput(['maxlength' => 50])->label("Número *") ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 clearfix">
            <?= $form->field($EnderecoEmpresa, 'complemento')->textInput(['maxlength' => 50]) ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($EnderecoEmpresa, 'referencia')->textInput(['maxlength' => 255])->label("Referência")->hint("Lembre-se de deixar claro o local de recebimento como blocos, apto, fundos, etc. Caso o entregador não encontre o lugar da entrega, outro frete poderá ser cobrado.  ") ?>
        </div>
        <div class="col-lg-3 "></div>
    </div>
    
    <div class="row">
        <div class="col-lg-4 "></div>
        <div class="form-group">
            <?= Html::submitButton('Cadastrar', [
                'class' => 'btn-lg btn-success col-xs-4 col-sm-4 col-md-4 col-lg-4 clearfix',
                'style' => 'margin: 15px;'
            ]) ?>
        </div>
        <div class="col-lg-4 "></div>
    </div>
    
    <div class="row">
    	<div class="col-lg-3 "></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            * Ao clicar Cadastrar, você reconhece que concorda com os <a
                href="<?= yii::$app->urlManager->baseUrl . "/site/politicas" ?>" target="_blank">Termos de Uso</a> e que
            leu e entendeu as <a href="<?= yii::$app->urlManager->baseUrl . '/site/politicas' ?>" target="_blank">Políticas
                de Privacidade</a> do Peça Agora. <br><br>
        </div>
        <div class="col-lg-3 "></div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

