<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Produto;

/* @var $this yii\web\View */
/* @var $model common\models\ValorProdutoFilialSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="valor-produto-filial-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'valor') ?>

    <?= $form->field($model, 'dt_inicio') ?>

    <?= $form->field($model, 'produto_filial_id') ?>

    <?= $form->field($model, 'dt_fim') ?>

    <?php // echo $form->field($model, 'promocao')->checkbox() ?>

    <?php // echo $form->field($model, 'valor_cnpj') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
