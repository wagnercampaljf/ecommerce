<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ProdutoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="produto-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'descricao') ?>

    <?= $form->field($model, 'peso') ?>

    <?= $form->field($model, 'altura') ?>

    <?php // echo $form->field($model, 'largura') ?>

    <?php // echo $form->field($model, 'profundidade') ?>

    <?php // echo $form->field($model, 'imagem') ?>

    <?php // echo $form->field($model, 'codigo_global') ?>

    <?php // echo $form->field($model, 'codigo_montadora') ?>

    <?php // echo $form->field($model, 'codigo_fabricante') ?>

    <?php // echo $form->field($model, 'fabricante_id') ?>

    <?php // echo $form->field($model, 'slug') ?>

    <?php // echo $form->field($model, 'micro_descricao') ?>

    <?php // echo $form->field($model, 'subcategoria_id') ?>

    <?php // echo $form->field($model, 'aplicacao') ?>

    <?php // echo $form->field($model, 'texto_vetor') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
