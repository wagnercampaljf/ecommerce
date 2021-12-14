<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
$this->params['url_image_footer'] = "http://i1068.photobucket.com/albums/u449/pecaagora/email-lojista_zps7jisnrqk.jpg";
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>

<h2 style="font-weight:lighter;">Olá <?= Html::encode($user->nome) ?>,</h2>

Utilize o Link abaixo para trocar sua senha:
<br/>
<?= Html::a(Html::encode($resetLink), $resetLink) ?>
