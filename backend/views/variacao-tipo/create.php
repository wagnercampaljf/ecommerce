<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VariacaoTipo */

$this->title = 'Create Variacao Tipo';
$this->params['breadcrumbs'][] = ['label' => 'Variacao Tipos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="variacao-tipo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
