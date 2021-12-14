<?php
use yii\helpers\Url;

$url_conta = Yii::$app->params['dominio'] . Url::to('/lojista/web/site/login');
$this->params['url_image_footer'] = "http://i1068.photobucket.com/albums/u449/pecaagora/email-lojista_zps7jisnrqk.jpg";
?>

<h2 style="font-weight:lighter;">
    Olá <?= $nome ?>, seu cadastro está completo!
</h2>
<br/>
<span style="font-size: 15px;">
Você agora está conectado ao melhor lugar para se encontrar as melhores peças para veículos leves e pesados.
<br/>O prazo para confirmação do cadastro da loja é de até 03 dias úteis. Durante esse período, você pode utilizar a plataforma e conhecer todas as funcionalidades que o Peça Agora tem para a sua loja!!
<br/>Veja algumas vantagens que o Peça Agora tem para a sua loja:<br/>
<ul>
    <li class="vertical">Sua Loja em um shopping de peças online sem custos de adesão;</li>
    <li class="vertical">Vendas 24 horas por dia, 7 dias por semana para todo o Brasil;</li>
    <li class="vertical">Gerencie suas vendas por região, segmentando seus clientes;</li>
    <li class="vertical">Fidelize seus clientes customizando os preços e formas de pagamentos;</li>
    <li class="vertical">Equipe técnica e especializada de apoio;</li>
</ul>
    <br/>O seu Login é <?= $email ?>
    <br/>Qualquer dúvida estamos à disposição.
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
               style="color: #5b9bd1; text-decoration: none;align-content: center">Acesse o site do Peça Agora!</a>
</div>
