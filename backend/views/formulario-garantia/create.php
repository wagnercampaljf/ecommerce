<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\FormularioGarantia */

$this->title = 'Create Formulario Garantia';
$this->params['breadcrumbs'][] = ['label' => 'Formulario Garantias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="formulario-garantia-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
