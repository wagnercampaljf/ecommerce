<?php


use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap;

/* @var $this yii\web\View */
/* @var $comprador common\models\Comprador */

$this->title = 'Cadastrar Cliente';
$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);
$this->params['breadcrumbs'][] = ['label' => 'Compradores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);

$view = '_formJuridica';
$activeJuridica = "nav-item active";
$activeFisica = "nav-item";
if ($tipoEmpresa == 'fisica') {
    $view = '_formFisica';
    $activeJuridica = "";
    $activeFisica = "active";
}
?>

<div class="comprador-create">
    <h2><?= ""//Html::encode($this->title) ?></h2>

    <div role="tabpanel" class="clearfix">
        <!-- <div class="pull-right text-center col-xs-12 col-sm-4 col-md-4 col-lg-4" style="margin: 0 0 10px 0;">
            <a href="<?= ""//yii::$app->urlManager->baseUrl . '/lojista/intro' ?>" class="btn btn-primary"><i
                    style="color: #f2f2f2;" class="fa fa-briefcase"></i> Quero abrir Minha Loja</a>
        </div>-->
        <div class="col-lg-3 "></div>
        	<div class="row col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <ul class="nav nav-tabs nav-justified">
                    <li role="presentation" class="nav-item <?= $activeFisica ?>">
                        <a class="nav-link"  href="<?= Url::to(['/comprador/create', 'tipoEmpresa' => 'fisica']) ?>">Pessoa Física</a>
                    </li>
                    <li role="presentation" class="nav-item <?= $activeJuridica ?>">
                        <a class="nav-link" href="<?= Url::to(['/comprador/create']) ?>">Pessoa Jurídica</a>
                    </li>
                </ul>
            </div>
        <div class="col-lg-3 "></div>
    </div>
    <?php \yii\widgets\Pjax::begin(['id' => 'form-create']) ?>
    <?= $this->render($view, [
        'comprador' => $comprador,
        'empresa' => $empresa,
        'grupo' => $grupo,
        'EnderecoEmpresa' => $EnderecoEmpresa,
    ]) ?>
    <?php \yii\widgets\Pjax::end() ?>
</div>
