<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$comprador = Yii::$app->user->isGuest;

?>
<div class="minhaconta-index container">
    <div class="tabs-minhaconta">
        <?php \yii\widgets\Pjax::begin(['id' => 'form-create']) ?>
        <?php
        if (!isset($pagina)) {
            $pagina = "home";
        }
        if (!isset($pageData)) {
            $data = '';
        }
        ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <ul id="tabs" class="nav nav-tabs nav-tabs-view nav-stacked  col-md-3 col-sm-12" data-tabs="tabs">
            <li <?php if ($pagina == "home") {
                echo 'class="active"';
            } ?>><a href="<?= Url::to('/minhaconta/index') ?>">Painel
                    de Controle</a></li>
            <li <?php if ($pagina == "dados") {
                echo 'class="active"';
            } ?>><a href="<?= Url::to('/minhaconta/dados') ?>">Meus dados</a>
            </li>
            <li <?php if ($pagina == "pedidos" || $pagina == "pedido") {
                echo 'class="active"';
            } ?>><a href="<?= Url::to(['/minhaconta/pedidos', 'id' => $comprador->getId()]) ?>">Pedidos</a>
            </li>
        </ul>

        <? $pageData['dataProvider'] = '';
        echo $this->render($pagina, [
            'comprador' => $comprador,
            'pageData' => $pageData,
        ]); ?>

        <?php \yii\widgets\Pjax::end() ?>
    </div>
</div>
</div>