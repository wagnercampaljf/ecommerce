<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>

<h2 style="font-weight:lighter;">Olá <?= Html::encode($user->nome) ?>,</h2>

Utilize o Link abaixo para trocar sua senha:
<br/>
<?= Html::a(Html::encode($resetLink), $resetLink) ?>
