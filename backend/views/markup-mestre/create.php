<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\common\models\MarkupMestre */

$this->title = 'Criar Markup Mestre';
$this->params['breadcrumbs'][] = ['label' => 'Markup Mestres', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="markup-mestre-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
