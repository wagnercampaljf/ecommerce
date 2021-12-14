<?php
/**
 * @var $nome string
 * @var $id mixed
 * @var $cod_rastro string
 */

use yii\helpers\Url;

?>

<h2 style="font-weight:lighter;">
    Olá <?= $nome ?>, seu pedido de número <?= $id ?> foi enviado!
</h2>

<h3>Código de rastreio: <?= $cod_rastro ?></h3>

<h4>Para acessar sua conta, utilize o link abaixo:</h4>

<div style="line-height:24px;" align="center">
    <a href="<?= Yii::$app->params['dominio'] . Url::base('minhaconta') ?>" target="_blank"></a>
    <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3" color="#596167">
        <a href="<?= $url = Yii::$app->params['dominio'] . Url::to('/minhaconta'); ?>" target="_blank"
           style="color: #5b9bd1; text-decoration: none;align-content: center">Minha Conta</a>
    </font>
</div>
