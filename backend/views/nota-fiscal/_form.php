<?php

use backend\models\NotaFiscal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MarcaProduto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="observacao-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'observacao')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
    .container-xxl {
        width: 90%;
        margin: auto;
    }
</style>