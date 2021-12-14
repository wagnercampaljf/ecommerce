<?php
use yii\helpers\Url;

?>

<h2 style="font-weight:lighter;">
    Olá <?= $nome ?>, o status do seu pedido de número <?= $id ?> foi alterado para <?= $status ?>!
</h2>

<h3>Para ver os dados do pedido, utilize o link abaixo:</h3>

<div style="line-height:24px;" align="center">
    <a href="<?= Yii::$app->params['dominio'] . Url::base('minhaconta') ?>"
       target="_blank"
        >
        <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3"
              color="#596167">
            <a href="<?= $url = Yii::$app->params['dominio'] . Yii::$app->urlManagerLojista->createUrl('') . Url::to('pedidos/view?id=' . $id); ?>"
               target="_blank"
               style="color: #5b9bd1; text-decoration: none;align-content: center"
                >Ver Pedido</a></div>
