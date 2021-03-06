<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Login';
$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);
$this->params['breadcrumbs'][] = $this->title;

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


<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <!--    FACEBOOK-->
    <br />
    <fb:login-button
            scope="public_profile,email"
            onlogin="checkLoginState();">
        Entrar com Facebook
    </fb:login-button>
    <br />
    <br />

    <p>Preencha os campos abaixo para acessar a sua conta: </p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'username')->input('email')->textInput(['maxlength' => 255, 'id' => 'email']) ?>
            <?= $form->field($model, 'password')->passwordInput()->passwordInput(['maxlength' => 255, 'id' => 'passwordFormInput']) ?>
            <?= $form->field($model, 'facebookLogin')->hiddenInput(['id' => 'facebookLogin', 'value' => false])->label(false) ?>
            <?= $form->field($model, 'name')->hiddenInput(['id' => 'name', 'value' => false])->label(false) ?>
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
            <div style="color:#999;margin:1em 0">
                <?= Html::a('Esqueci minha senha!', ['site/esqueci']) ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Entrar', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
            <p>Ainda não possui uma conta? <?= ""//Html::a('Clique aqui', ['comprador/create']) ?> <a rel="nofollow" href="<?= Url::to(['/comprador/create?tipoEmpresa=fisica']) ?>"> Clique aqui</a>.
            </p>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
