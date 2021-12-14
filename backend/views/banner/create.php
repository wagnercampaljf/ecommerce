<?php


/* @var $this yii\web\View */
/* @var $model common\models\Banner */

$this->title = Yii::t('app', 'Criar Banner');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banner-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
