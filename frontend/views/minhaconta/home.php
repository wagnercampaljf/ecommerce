<?php
use frontend\widgets\BannerWidget;
use yii\helpers\Html;

$this->title = 'Minha Conta';
$this->params['active'] = 'home';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-pane active  col-md-9 col-sm-12" id="home">
    <?= BannerWidget::widget([
        'cidade' => Yii::$app->getLocation->getCidade(),
        'posicao_banner' => 'center'
    ]) ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
        </div>
    </div>
</div>