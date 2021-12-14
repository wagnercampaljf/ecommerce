<?php
use frontend\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
];
?>
<!-- BEGIN LOGIN FORM -->



<?php $form = ActiveForm::begin(['id' => 'login-form']);?>
<h3><img class="logo-lojista" src="<?= Url::to('@frontend_assets/'); ?>img/pecaagora.png"></h3>
<?= Alert::widget() ?>
<?= $form->field($model, 'username')->textInput(['placeholder' => 'Nome de usuário'])->label('') ?>
<?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Senha'])->label('') ?>
<?= $form->field($model, 'rememberMe')->checkbox() ?>
<div style="color:#999;margin:1em 0">
    <?= Html::a('Esqueci minha senha!', ['site/esqueci']) ?>
</div>
<div class="form-group">
    <?= Html::submitButton('Entrar', ['class' => 'btn btn-success uppercase', 'name' => 'login-button']) ?>
</div>
</p>
<?php ActiveForm::end(); ?>

<div class="create-account">
    <p>
        <a href="<?= Url::to(['site/esqueci']); ?>" id="register-btn" class="uppercase">Esqueci Minha Senha!</a>
    </p>
</div>

