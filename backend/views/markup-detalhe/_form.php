<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\common\models\MarkupDetalhe */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="markup-detalhe-form">

    <?php $form = ActiveForm::begin();
    ?>

    <?= $form->field($model, 'markup_mestre_id')->hiddenInput(['readonly' => true, 'value' => $model->markup_mestre_id]) ?>

    <?= $form->field($model, 'e_margem_absoluta')->checkbox() ?>

    <?= $form->field($model, 'valor_minimo')->textInput() ?>

    <?= $form->field($model, 'valor_maximo')->textInput() ?>

    <?= $form->field($model, 'margem')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
