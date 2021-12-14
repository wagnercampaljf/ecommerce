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
<div class="comprador-form comprador-form-juridica clearfix" >

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
    <div class=" col-lg-10 col-lg-offset-1 padmobile col-sm-12 col-md-10 col-md-offset-1" style="padding-bottom: 10px; background: #fff !important;box-shadow: 0px 0px 5px #00000059;">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-12 clearfix">
            <p style="text-align: center">Compras feito com CNPJ, o produto poderá ter acréscimo de imposto. Mais informações pelo chat.</p>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3" style="border-bottom: 3px solid #007576; padding-top: 10px">
                <h3>Dados login</h3>
            </div>
        </div><br>

        <div class="col-sm-12 ">
            <div class="row">
                <div class="col-sm-6 padmobile top10 pjuridica" style="display: block;">
                    <?= $form->field($comprador,
                        'email')->input('email')->textInput(['maxlength' => 255])->label("Email / Login *")->hint("Ex: joao@yahoo.com.br") ?>
                </div>
            </div>

            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                <?= $form->field($comprador,
                'password')->passwordInput(['maxlength' => 255, 'id' => 'passwordFormInput'])->hint('Senha deve conter no minimo 6 caracteres de A-Z a-z 0-9')->label("Senha *") ?>
                <div id="senhaBarra" class="progress "style="display: none;">
                    <div id="senhaForca" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                <?= $form->field($comprador,
                    'repeat_password')->passwordInput(['maxlength' => 255])->label("Repetir Senha *") ?>
            </div>
            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3"></div>
        </div>
    </div>
</div><br>


<script>
    //Necessita do bootstrap e jquery
    //forca da senha
    $(function (){
        $('#passwordFormInput').keyup(function (e){
            var senha = $(this).val();
            if(senha == ''){
                $('#senhaBarra').hide();
            }else{
                var fSenha = forcaSenha(senha);
                var texto = "";
                $('#senhaForca').css('width', fSenha+'%');
                $('#senhaForca').removeClass();
                $('#senhaForca').addClass('progress-bar');
                if(fSenha <= 40){
                    texto = 'Fraca';
                    $('#senhaForca').addClass('progress-bar-danger');
                }

                if(fSenha > 40 && fSenha <= 70){
                    texto = 'Media';
                }

                if(fSenha > 70 && fSenha <= 90){
                    texto = 'Boa';
                    $('#senhaForca').addClass('progress-bar-success');
                }

                if(fSenha > 90){
                    texto = 'Muito boa';
                    $('#senhaForca').addClass('progress-bar-success');
                }

                $('#senhaForca').text(texto);

                $('#senhaBarra').show();
            }
        });
    });

    function forcaSenha(senha){
        var forca = 0;

        var regLetrasMa     = /[A-Z]/;
        var regLetrasMi     = /[a-z]/;
        var regNumero       = /[0-9]/;
        var regEspecial     = /[!@#$%&*?]/;

        var tam         = false;
        var tamM        = false;
        var letrasMa    = false;
        var letrasMi    = false;
        var numero      = false;
        var especial    = false;

//    console.clear();
//    console.log('senha: '+senha);

        if(senha.length >= 6) tam = true;
        if(senha.length >= 10) tamM = true;
        if(regLetrasMa.exec(senha)) letrasMa = true;
        if(regLetrasMi.exec(senha)) letrasMi = true;
        if(regNumero.exec(senha)) numero = true;
        if(regEspecial.exec(senha)) especial = true;

        if(tam) forca += 10;
        if(tamM) forca += 10;
        if(letrasMa) forca += 10;
        if(letrasMi) forca += 10;
        if(letrasMa && letrasMi) forca += 20;
        if(numero) forca += 20;
        if(especial) forca += 20;

//    console.log('força: '+forca);

        return forca;
    }
</script>




<!--   DADOS DO COMPRADOR-->
<!--   DADOS DA COMPRADOR
<div class="comprador-form comprador-form-juridica clearfix" >
    <div class=" col-lg-10 col-lg-offset-1 padmobile col-sm-12 col-md-10 col-md-offset-1" style="padding-bottom: 10px; background: #fff !important;box-shadow: 0px 0px 5px #00000059;">

    <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4" style="border-bottom: 3px solid #007576; padding-top: 10px">
            	<h3>Dados representante</h3>
            </div>
        </div><br>

    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($comprador, 'nome')->textInput(['maxlength' => 150])->label("Nome do representante *") ?>
        </div>

    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 clearfix">
            <?= $form->field($comprador, 'cpf')->textInput(['maxlength' => 14])->hint("Somente números")->label("CPF *") ?>
        </div>

    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($comprador, 'cargo')->textInput(['maxlength' => 50]) ?>
        </div>

</div><br>-->




<!--  DADOS EMPRESA-->
<div class="comprador-form comprador-form-juridica clearfix" >
    <div class=" col-lg-10 col-lg-offset-1 padmobile col-sm-12 col-md-10 col-md-offset-1" style="padding-bottom: 10px; background: #fff !important;box-shadow: 0px 0px 5px #00000059;">

        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix" style="border-bottom: 3px solid #007576; padding-top: 10px">
                <h3>Dados empresa</h3>
            </div>
        </div><br>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($empresa, 'nome')->textInput(['maxlength' => 150])->label("Nome da empresa *") ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($empresa, 'razao')->textInput(['maxlength' => 150])->label("Razão Social *") ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix ">
            <?= $form->field($empresa, 'documento')->textInput(['maxlength' => 18])->label("CNPJ *") ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($empresa, 'email')->input('email')->textInput(['maxlength' => 150])->label("Email da empresa")->hint("Ex: contato@empresa.com.br") ?>
        </div>
        <!--<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?php
            /*$tipoempresa = TipoEmpresa::find()->getTipoEmpresaComprador()->andWhere(['juridica' => "t"])->all();
            $tipoempresa = ArrayHelper::map($tipoempresa, 'id', 'nome');
            echo $form->field($empresa, 'id_tipo_empresa')->dropDownList(
                $tipoempresa,
                [
                    'class' => 'form-control select2',
                    'prompt' => 'Tipo de Empresa',
                    'id' => 'select_tipoempresa',
                ])->label("Tipo empresa *");*/
            ?>
        </div>-->
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 clearfix">
            <?= $form->field($empresa, 'telefone')->textInput(['maxlength' => 20])->label("Telefone *") ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 clearfix">
            <?= $form->field($empresa, 'telefone_alternativo')->textInput(['maxlength' => 20]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($empresa, 'observacao')->textInput(['maxlength' => 400]) ?>
        </div>
        <!--<div class="col-xs-12 col-sm-6 col-md-6 col-lg-12 clearfix">
            <p style="text-align: center">Compras feito com CNPJ, o produto poderá ter acréscimo de imposto. Mais informações pelo chat.</p>
        </div>-->
    </div>
</div><br>

<!--    DADOS ENDERECO EMPRESA-->
<div class="comprador-form comprador-form-juridica clearfix" >
    <div class=" col-lg-10 col-lg-offset-1 padmobile col-sm-12 col-md-10 col-md-offset-1" style="padding-bottom: 10px; background: #fff !important;box-shadow: 0px 0px 5px #00000059;">

        <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix" style="border-bottom: 3px solid #007576; padding-top: 10px">
                    <h3>Endereço de Entrega</h3>
                </div>
                <div class="col-lg-3 "></div>
            </div><br>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 clearfix form-inline">
                <?= $form->field($EnderecoEmpresa, 'cep')->textInput([
                        'maxlength' => 9,
                        'id' => 'cep-comprador',
                        'onkeyup' => 'javascript:getEndereco(this.value,"enderecoempresa");'
                    ])->label("CEP *") ?>
                <i class="fa fa-spinner fa-spin" style="display: none"></i>
            </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
                    <?= $form->field($EnderecoEmpresa, 'logradouro')->textInput(['maxlength' => 255])->label("Logradouro *") ?>
                </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
                    <?= $form->field($EnderecoEmpresa, 'bairro')->textInput()->label("Bairro *") ?>
                </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
                    <?= $form->field($EnderecoEmpresa, 'cidade')->textInput(['disabled' => false])->label("Cidade *") ?>
                    <?= ""//$form->field($EnderecoEmpresa, 'cidade_id')->hiddenInput()->label(false) ?>
                </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
                    <?= $form->field($EnderecoEmpresa, 'estado')->textInput()->label("Estado *") ?>
                </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 clearfix">
            <?= $form->field($EnderecoEmpresa, 'numero')->textInput(['maxlength' => 50])->label("Número *") ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 clearfix">
            <?= $form->field($EnderecoEmpresa, 'complemento')->textInput(['maxlength' => 50]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($EnderecoEmpresa, 'referencia')->textInput(['maxlength' => 255])->label("Referência")->hint("Lembre-se de deixar claro o local de recebimento como blocos, apto, fundos, etc. Caso o entregador não encontre o lugar da entrega, outro frete poderá ser cobrado.  ") ?>
        </div>


    </div>
</div>


<div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 clearfix"></div>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-8 clearfix">
    <div class="form-group">
        <?= Html::submitButton('Cadastrar', [
            'class' => 'btn-lg btn-success col-xs-4 col-sm-4 col-md-4 col-lg-4 clearfix',
            'style' => 'margin: 15px;'
        ]) ?>
    </div>
</div>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 clearfix"></div>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-10 clearfix">
    * Ao clicar Cadastrar, você reconhece que concorda com os <a
            href="<?= yii::$app->urlManager->baseUrl . "/site/politicas" ?>" target="_blank">Termos de Uso</a> e que
    leu e entendeu as <a href="<?= yii::$app->urlManager->baseUrl . '/site/politicas' ?>" target="_blank">Políticas
        de Privacidade</a> do Peça Agora. <br><br>
</div></div>
    <?php ActiveForm::end(); ?>


<style>
    @media screen and (min-width: 768px){
        .col-lg-10{
            float: left;
        }
    }
</style