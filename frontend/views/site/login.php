<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Login';
$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);
//$this->params['breadcrumbs'][] = $this->title;

?>

<!-- app id pra teste: 710943719280425 -->
<!-- app id produçao: 1940571362675909 -->

<!-- SCRIPT PARA LOGIN COM O FACEBOOK -->
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '710943719280425',
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
                console.log(response);

                document.getElementById('email').value = response.email;
                document.getElementById('passwordFormInput').value = response.id;
                document.getElementById('facebookLogin').value = true;
                document.getElementById('name').value = response.name;

                document.forms['login-form'].submit();

            });
        } else {
            // The person is not logged into your app or we are unable to tell.
            document.getElementById('status').innerHTML = 'Please log ' +
                'into this app.';
        }
    }
</script>
<!-- SCRIPT PARA LOGIN COM O FACEBOOK -->

<div class=" col-lg-1"></div>
<div class="row">
    <div class="col-lg-3">
    </div>
    <div class="col-lg-4" style="padding-bottom: 10px; background: #fff !important;box-shadow: 0px 0px 5px #00000059;">
        <div class="site-login" >
            <div class="row " style="border-bottom: 3px solid #007576">
                <h1><?= Html::encode($this->title) ?></h1>
            </div><br>
            <div class="row">
                <p>Preencha os campos abaixo para acessar a sua conta: </p>
            </div>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <div class="row">
                <?= $form->field($model, 'username')->input('email')->textInput(['maxlength' => 255, 'id' => 'email']) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'password')->passwordInput()->passwordInput(['maxlength' => 255, 'id' => 'passwordFormInput']) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
                <div style="color:#999;margin:1em 0">
                    <?= Html::a('Esqueci minha senha!', ['site/esqueci']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <?= ""//Html::submitButton('Entrar', ['class' => 'btn-lg btn-primary', 'name' => 'login-button']) ?>
                        <?= Html::submitButton('Entrar', ['class' => 'btn-lg btn-primary col-xs-12 col-sm-12 col-md-12 col-lg-12 clearfix', 'name' => 'login-button']) ?>
                    </div>
                </div>
            </div>


        <!--    FACEBOOK-->
            <div class="center" style="margin: 0 auto;width: 55%">
                <fb:login-button scope="public_profile,email" onlogin="checkLoginState();" size="large">
                    Entrar com Facebook
                </fb:login-button><br><br>
            </div>
        <div class="container" style="align-items: center">
                <p style="text-align: center">Ainda não possui uma conta? <?= ""//Html::a('Clique aqui', ['comprador/create']) ?><br> <a rel="nofollow" href="<?= Url::to(['/comprador/create?tipoEmpresa=fisica']) ?>" style="color: #000000; font-size: 18px; font-weight: bold "> Clique aqui</a>.</p>
        </div>
            <?= $form->field($model, 'facebookLogin')->hiddenInput(['id' => 'facebookLogin', 'value' => false])->label(false) ?>
            <?= $form->field($model, 'name')->hiddenInput(['id' => 'name', 'value' => false])->label(false) ?>
            <?php ActiveForm::end(); ?>
        </div>

    </div>

</div><br><br>


<?php

//$hoje="12-02-2020";
//$hoje=str_replace("-","/",$hoje);
//echo $hoje;





?>
