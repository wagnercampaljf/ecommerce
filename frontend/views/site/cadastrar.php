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
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Preencha os campos abaixo para acessar a sua conta: </p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                <?= $form->field($model, 'username') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
                <div style="color:#999;margin:1em 0">
                    <?= Html::a('Esqueci minha senha!', ['site/request-password-reset']) ?>
                </div>
                <div class="form-group">
                    <?= Html::submitButton('Entrar', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            <p>Ainda não possui uma conta? <?= ""//Html::a('Clique aqui', ['site/cadastrar']) ?><a rel="nofollow" href="<?= Url::to(['/comprador/create?tipoEmpresa=fisica']) ?>"> Clique aqui</a>.
            </p>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
