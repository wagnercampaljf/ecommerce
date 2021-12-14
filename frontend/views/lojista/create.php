<?php

use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $lojista common\models\Lojista */
/* @var $usuario common\models\Usuario */
/* @var $filial common\models\Filial */
/* @var $bancos common\models\Banco[] */
/* @var $enderecoFilial common\models\EnderecoFilial */

$this->title = 'Cadastrar Lojista';
$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);
$this->params['breadcrumbs'][] = ['label' => 'Lojistas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$lojista->juridica = 1;
AppAsset::register($this);
?>

<div class="lojista-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php \yii\widgets\Pjax::begin(['id' => 'form-create']) ?>
    <?= $this->render('_form', [
        'lojista' => $lojista,
        'usuario' => $usuario,
        'filial' => $filial,
        'bancos' => $bancos,
        'enderecoFilial' => $enderecoFilial,
    ]) ?>
    <?php \yii\widgets\Pjax::end() ?>
</div>

