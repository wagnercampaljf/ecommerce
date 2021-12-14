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

<!-- app id pra teste: 710943719280425 -->
<!-- app id produçao: 1940571362675909 -->

<!-- SCRIPT PARA LOGIN COM O FACEBOOK -->
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '1940571362675909',
            cookie     : true,
            xfbml      : true,
            version    : 'v3.2'
        });

        FB.AppEvents.logPageView();

    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

<script>
    function checkLoginState() {
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
            console.log(response);
        });
    }
</script>
<script>
    function statusChangeCallback(response) {
        console.log('statusChangeCallback ', response);

        if (response.status === 'connected') {
            FB.api('/me?fields=name,email', function (response) {
                // console.log(response);

                document.getElementById('email').value = response.email;
                document.getElementById('passwordFormInput').value = response.id;
                document.getElementById('nameFormInput').value = response.name;

                document.forms['_formFisica'].submit();

            });
        } else {
            // The person is not logged into your app or we are unable to tell.
            document.getElementById('status').innerHTML = 'Please log ' +
                'into this app.';
        }
    }
</script>
<!-- SCRIPT PARA LOGIN COM O FACEBOOK -->

<div class="comprador-form comprador-form-fisica">
<!--    <h2>Form da Física</h2>-->
    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => '_formFisica'
        ]]); ?>

    <div class="row">
        <div class="col-lg-3 "></div><div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
		<!--    FACEBOOK-->
		    <br />
		    <fb:login-button
		            scope="public_profile,email"
		            onlogin="checkLoginState();">
		        Cadastrar com Facebook
		    </fb:login-button>
		    <br />
		    <br />
		<!--    FACEBOOK-->

        </div><div class="col-lg-3 "></div>
    </div>

    <!--    EMAIL E SENHA -->
    <!--<h3>Dados login</h3>-->

    <div class="row">
        <div class="col-lg-3 "></div><div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?= $form->field($comprador,
                'email')->input('email')->textInput(['maxlength' => 255, 'id' => 'email'])->label("Login / Email *")->hint("Ex: joao@yahoo.com.br") ?>
        </div><div class="col-lg-3 "></div>
    </div>
    <div class="row">
        <div class="col-lg-3 "></div><div class="col-xs-12 col-sm-3 col-md-3 col-lg-4">
            <?= $form->field($comprador,
                'password')->passwordInput(['maxlength' => 255, 'id' => 'passwordFormInput'])->hint('Senha de conter no minimo 6 caracteres de A-Za-z0-9')->label("Senha *") ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
            <br class="hidden-xs"><br class="hidden-xs"><?= Html::checkbox('reveal-password', false, ['id' => 'reveal-password']) ?> <?= Html::label('Exibir Senha', 'reveal-password') ?>
        </div><div class="col-lg-3 "></div>
    </div>
    <!--   DADOS DA COMPRADOR-->
    <!--<h3>Dados Cliente</h3>-->

    <div class="row">
        <div class="col-lg-3 "></div><div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix">
            <?= $form->field($comprador, 'nome')->textInput(['maxlength' => 150, 'id' => 'nameFormInput'])->label("Nome completo*") ?>
        </div><div class="col-lg-3 "></div>
    </div>

    <div class="row">
        <div class="col-lg-4 "></div><div class="form-group">
            <?= Html::submitButton('Cadastrar', [
                'class' => 'btn-lg btn-success col-xs-4 col-sm-3 col-md-3 col-lg-4 clearfix',
                'style' => 'margin: 15px;'
            ]) ?>
        </div><div class="col-lg-4 "></div>
    </div>


    <div class="row">
        <div class="col-lg-3 "></div><div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 clearfix ">
            * Ao clicar Cadastrar, você reconhece que concorda com os <a
                href="<?= yii::$app->urlManager->baseUrl . "/site/politicas" ?>" target="_blank">Termos de Uso</a> e que
            leu e entendeu as <a href="<?= yii::$app->urlManager->baseUrl . '/site/politicas' ?>" target="_blank">Políticas
                de Privacidade</a> do Peça Agora. <br><br>
        </div><div class="col-lg-3 "></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
