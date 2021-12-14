<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Funcao */

$this->title = 'Create Funcao';
$this->params['breadcrumbs'][] = ['label' => 'Funcaos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="funcao-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
