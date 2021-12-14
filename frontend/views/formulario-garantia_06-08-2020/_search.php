<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\FormularioCarantiaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="formulario-garantia-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'data_compra') ?>

    <?= $form->field($model, 'razao_social') ?>

    <?php // echo $form->field($model, 'nr_nf_compra') ?>

    <?php // echo $form->field($model, 'codigo_peça_seis_digitos') ?>

    <?php // echo $form->field($model, 'modelo_do_veiculo') ?>

    <?php // echo $form->field($model, 'ano') ?>

    <?php // echo $form->field($model, 'chassi') ?>

    <?php // echo $form->field($model, 'numero_de_serie_do_motor') ?>

    <?php // echo $form->field($model, 'data_aplicação') ?>

    <?php // echo $form->field($model, 'km_montagem') ?>

    <?php // echo $form->field($model, 'km_defeito') ?>

    <?php // echo $form->field($model, 'contato') ?>

    <?php // echo $form->field($model, 'telefone') ?>

    <?php // echo $form->field($model, 'descrição_do_defeito_apresentado') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
