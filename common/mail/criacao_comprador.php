<?php

use yii\helpers\Url;

$url_conta = Yii::$app->params['dominio'] . Url::to('/site/login');
$this->params['url_image_footer'] = "http://i1068.photobucket.com/albums/u449/pecaagora/email1_zpsevmgkfvy.jpg";
?>

<h2 style="font-weight:lighter;">
    Olá <?= $nome ?>, seu cadastro está completo!
</h2>
<br/>
<span style="font-size: 15px;">
Você agora está conectado ao melhor lugar para se encontrar as melhores peças para veículos leves e pesados.
<br/>O seu Login é <?= $email ?>
    <br/>Para acessar sua conta, utilize o link abaixo:
									</span></font>
<br/>
<br/>
<br/>
<div style="line-height:24px;" align="center">
    <a href="<?= Yii::$app->params['dominio'] . Url::base('minhaconta') ?>"
       target="_blank"
       style="color: #596167; font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
        <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3"
              color="#596167">
            <a href="<?= $url_conta ?>" target="_blank"
               style="color: #5b9bd1; text-decoration: none;align-content: center">Acesse sua Conta
            </a></div>
