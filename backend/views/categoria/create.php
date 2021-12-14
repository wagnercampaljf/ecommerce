<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Categoria */

$this->title = 'Criar Categoria';
?>
<div class="categoria-create">


    <?= $this->render('_form', [
        'model' => $model,
        'categorias' => $categorias
    ]) ?>

</div>
