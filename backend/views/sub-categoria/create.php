<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SubCategoria */

$this->title = 'Create Sub Categoria';
$this->params['breadcrumbs'][] = ['label' => 'Sub Categorias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-categoria-create">


    <?= $this->render('_form', [
        'model' => $model,
        'categorias' => $categorias

    ]) ?>

</div>
