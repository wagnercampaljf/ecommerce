<?php
use yii\helpers\Html;
use yii\helpers\Url;

if (!isset($this->params['url_image_footer'])) {
    $this->params['url_image_footer'] = "http://i1068.photobucket.com/albums/u449/pecaagora/email1_zpsevmgkfvy.jpg";
}


?>
<?php $this->beginPage() ?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table id="Tabela_01" width="770" height="auto" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <img style="display:block;"
                 src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_01_zps577lasul.jpg" width="1"
                 height="72" alt=""></td>

        <td colspan="10"><img style="display:block;"
                              src="http://i1068.photobucket.com/albums/u449/pecaagora/fatias_2_zpscryqavdc.jpg"
                              width="769" height="72" alt=""></td>
    </tr>
    <tr>
        <td><img style="display:block;"
                 src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_03_zps6qy7hb8r.jpg" width="1"
                 height="30" alt=""></td>
        <td rowspan="3"><img style="display:block;"
                             src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_04_zpsztsufx3u.jpg"
                             width="20" height="243" alt=""></td>

        <td colspan="8"><img style="display:block;"
                             src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_05_zpsjgofnou7.jpg"
                             width="723" height="30" alt=""></td>

        <td rowspan="4"><img style="display:block;"
                             src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_06_zpshvbidrxe.jpg"
                             width="26" height="294" alt=""></td>
    </tr>
    <tr>

        <td><img style="display:block;"
                 src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_07_zpsf7mx9gwu.jpg" width="1"
                 height="175" alt=""></td>
        <td colspan="8">
            <div class="campoEditavel" style="font-family:sans-serif;  font-weight:lighter; color:#027977;">

                <?php $this->beginBody() ?>
                <?= $content ?>
                <?php $this->endBody() ?>


        </td>
    </tr>
    <tr>

        <td><img style="display:block;"
                 src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_09_zpsuznitryu.jpg" width="1"
                 height="38" alt=""></td>

        <td colspan="8"><img style="display:block;"
                             src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_10_zpsduo0eba3.jpg"
                             width="723" height="38" alt=""></td>
    </tr>
    <tr>
        <td colspan="10"><img style="display:block;"
                              src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_11_zpsyehmkf8y.jpg"
                              width="744" height="51" alt=""></td>
    </tr>
    <tr>
        <td colspan="11"><img style="display:block;"
                              src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_12_zpshs9iltgs.jpg"
                              width="770" height="49" alt=""></td>
    </tr>
    <tr>

        <td colspan="11"><img style="display:block;"
                              src="<?= $this->params['url_image_footer'] ?>"
                              width="770" height="118" alt=""></td>
    </tr>
    <tr>

        <td colspan="11"><img style="display:block;"
                              src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_14_zpswy8px58w.jpg"
                              width="770" height="18" alt=""></td>
    </tr>
    <tr>

        <td colspan="3"><img style="display:block;"
                             src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_15_zpsarwx5hfn.jpg"
                             width="167" height="14" alt=""></td>

        <td><a href="<?= Yii::$app->params['dominio'] . Url::to('/site/about') ?>"><img style="display:block;"
                                                                    src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_16_zps68v5ztup.jpg"
                                                                    width="71" height="14" alt=""></a></td>

        <td><img style="display:block;"
                 src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_17_zpsw0thtrnn.jpg" width="62"
                 height="14" alt=""></td>

        <td><a href="<?= Yii::$app->params['dominio'] . Url::to('/site/politicas') ?>"><img style="display:block;"
                                                                        src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_18_zpszonpox9n.jpg"
                                                                        width="143" height="14" alt=""></a></td>

        <td><img style="display:block;"
                 src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_19_zps0xnhrq6i.jpg" width="69"
                 height="14" alt=""></td>

        <td><img style="display:block;"
                 src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_20_zpsvi6s0x6m.jpg" width="1"
                 height="14" alt=""></td>

        <td><a href="<?= Yii::$app->params['dominio'] . Url::to('/search/') ?>"><img style="display:block;"
                                                                                     src="http://i1068.photobucket.com/albums/u449/pecaagora/compreagora3_zpsiese4n6j.jpg"
                                                                      width="110" height="14" alt=""></a></td>
        <td colspan="2"><img style="display:block;"
                             src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_22_zps6mdmiqud.jpg"
                             width="147" height="14" alt=""></td>
    </tr>
    <tr>
        <td colspan="11"><img style="display:block;"
                              src="http://i1068.photobucket.com/albums/u449/pecaagora/Bem-Vindo---fatias_23_zps1p4zs6ia.jpg"
                              width="770" height="31" alt=""></td>
    </tr>
    <tr>

        <td><img style="display:block;" src="http://i1068.photobucket.com/albums/u449/pecaagora/spacer_zpswof9akek.gif" width="1"
                 height="1" alt=""></td>
        <td><img style="display:block;" src="http://i1068.photobucket.com/albums/u449/pecaagora/spacer_zpswof9akek.gif" width="20"
                 height="1" alt=""></td>
        <td><img style="display:block;" src="http://i1068.photobucket.com/albums/u449/pecaagora/spacer_zpswof9akek.gif" width="146"
                 height="1" alt=""></td>
        <td><img style="display:block;" src="http://i1068.photobucket.com/albums/u449/pecaagora/spacer_zpswof9akek.gif" width="71"
                 height="1" alt=""></td>
        <td><img style="display:block;" src="http://i1068.photobucket.com/albums/u449/pecaagora/spacer_zpswof9akek.gif" width="62"
                 height="1" alt=""></td>
        <td><img style="display:block;" src="http://i1068.photobucket.com/albums/u449/pecaagora/spacer_zpswof9akek.gif" width="143"
                 height="1" alt=""></td>
        <td><img style="display:block;" src="http://i1068.photobucket.com/albums/u449/pecaagora/spacer_zpswof9akek.gif" width="69"
                 height="1" alt=""></td>
        <td><img style="display:block;" src="http://i1068.photobucket.com/albums/u449/pecaagora/spacer_zpswof9akek.gif" width="1"
                 height="1" alt=""></td>
        <td><img style="display:block;" src="http://i1068.photobucket.com/albums/u449/pecaagora/spacer_zpswof9akek.gif" width="110"
                 height="1" alt=""></td>
        <td><img style="display:block;" src="http://i1068.photobucket.com/albums/u449/pecaagora/spacer_zpswof9akek.gif" width="121"
                 height="1" alt=""></td>
        <td><img style="display:block;" src="http://i1068.photobucket.com/albums/u449/pecaagora/spacer_zpswof9akek.gif" width="26"
                 height="1" alt=""></td>
    </tr>
</table>
</body>
</html>
<?php $this->endPage() ?>
