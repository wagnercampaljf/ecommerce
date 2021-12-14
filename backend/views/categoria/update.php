<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Categoria */

$this->title = 'Editar Categoria: ' . ' ' . $model->nome;

?>
<div class="categoria-update">


    <?= $this->render('_form', [
        'model' => $model,
        'categorias' => $categorias,
    ]) ?>

</div>
