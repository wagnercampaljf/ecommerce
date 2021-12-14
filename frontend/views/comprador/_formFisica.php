<?php

use yii\helpers\html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $comprador common\models\Comprador */
/* @var $form yii\widgets\ActiveForm */
/* @var $this yii\web\View */
/* @var $model common\models\Empresa */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs("jQuery('#reveal-password').change(function(){jQuery('#passwordFormInput').attr('type',this.checked?'text':'password');})");
?>
</br>
<div class=" col-lg-10 col-lg-offset-1 padmobile col-sm-12 col-md-10 " style="padding-bottom: 10px; background: #fff !important;box-shadow: 0px 0px 5px #00000059; ">
    <div class="comprador-form comprador-form-fisica" >
<!--    <h2>Form da Física</h2>-->
    <?php $form = ActiveForm::begin(); ?>
    <!--    EMAIL E SENHA -->
    <!-- <h3>Dados login</h3>-->
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3" style="border-bottom: 3px solid #007576; padding-top: 10px">
                <h3>Dados login</h3>
            </div>
        </div><br>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($comprador, 'nome')->textInput(['maxlength' => 150])->label("Nome completo*") ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 offset-lg-4">
            <?= $form->field($comprador,'email')->input('email')->textInput(['maxlength' => 255])->label("Login / Email *")->hint("Ex: joao@yahoo.com.br") ?>

        </div>


        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-4">
            <?= $form->field($comprador,'password')->passwordInput(['maxlength' => 255, 'id' => 'passwordFormInput'])->hint('Senha deve conter no minimo 6 caracteres de A-Za-z0-9')->label("Senha *") ?>
            <div id="senhaBarra" class="progress "style="display: none;">
                <div id="senhaForca" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
            <span id="olho" class="glyphicon glyphicon glyphicon-eye-open form-control-feedback olho hidden-xs"> </span><label style="padding-left: 20px; padding-top: 30px" class="hidden-xs"> Exibir Senha</label>
        </div>
    </div>


    <script>
        document.getElementById('olho').addEventListener('mousedown', function() {
            document.getElementById('passwordFormInput').type = 'text';
        });

        document.getElementById('olho').addEventListener('mouseup', function() {
            document.getElementById('passwordFormInput').type = 'password';
        });

        // Para que o password não fique exposto apos mover a imagem.
        //document.getElementById('olho').addEventListener('mousemove', function() {
            //document.getElementById('passwordFormInput').type = 'password';
        //})

    </script>


    <style>
        .olho {
            cursor: pointer;
            /*left: 160px;*/
            position: absolute;
            width: 100px;
            color: #007475;
            padding-top: 30px;

        }
    </style>


    <!--   DADOS DA COMPRADOR-->
    <!-- <h3>Dados Cliente</h3>-->



</div><br>
<div class="row">
    <div class="mobile col-xs-12 col-sm-3 col-md-3 col-lg-12 " style=" width: 100%;!important;">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4"></div>
            <div class="form-group">
                <?= Html::submitButton('Cadastrar', [
                    'class' => 'btn-lg btn-success col-xs-4 col-sm-3 col-md-3 col-lg-4 clearfix',
                    'style' => 'margin: 15px;'
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-2"></div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-8">
                * Ao clicar Cadastrar, você reconhece que concorda com os <a href="<?= yii::$app->urlManager->baseUrl . "/site/politicas" ?>" target="_blank">Termos de Uso</a> e que
                leu e entendeu as <a href="<?= yii::$app->urlManager->baseUrl . '/site/politicas' ?>" target="_blank">Políticas
                    de Privacidade</a> do Peça Agora. <br><br>
            </div>
        </div>
    </div>
</div>
    <?php ActiveForm::end(); ?>



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

        if(tam) forca += 7;
        if(tamM) forca += 7;
        if(letrasMa) forca += 7;
        if(letrasMi) forca += 7;
        if(letrasMa && letrasMi) forca += 16;
        if(numero) forca += 16;
        if(especial) forca += 16;

//    console.log('força: '+forca);

        return forca;
    }
</script>


<style>
    @media screen and (min-width: 768px){
        .col-lg-10{
            float: left;
        }
    }
</style>

