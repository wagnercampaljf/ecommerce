<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SubCategoria */

$this->title = 'Editar Subcategoria: ' . ' ' . $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Sub Categorias', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nome, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sub-categoria-update">


    <?= $this->render('_form', [
        'model' => $model,
        'categorias' => $categorias

    ]) ?>

</div>
