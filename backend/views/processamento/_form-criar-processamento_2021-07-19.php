<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Funcao;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Processamento */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="processamento-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'funcao_id')->widget(Select2::className(), [
                'data' => ArrayHelper::map(
                    Funcao::find()->orderBy(['nome' => SORT_ASC])->all(),
                    'id',
                    'nome'
                ),
                'options' => ['placeholder' => 'Selecione uma Função']
            ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
