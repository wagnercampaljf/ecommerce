<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
/* @var $this yii\web\View */
/* @var $model common\models\OperacaoFinanceira */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="imagens-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'options' => [
                'class' => 'form-group',
            ]
        ]
    ]); ?>
    <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="row col-lg-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <?= $form->field($model, 'arquivo',
                    ['options' => ['class' => 'form-group col-lg-12 col-md-12 col-sm-12']])->widget(FileInput::className(),
                    [
                        'options' => ['accept' => 'image/jpeg, image/png'],
                        'pluginOptions' => [
                            'showUpload' => false,
                            'browseIcon' => '<i class="glyphicon glyphicon-collapse-up"></i> ',
                            'browseLabel' => 'Selecione um arquivo',
                            //'initialPreview' => $model->getImg(['class' => 'file-preview-image']),
                            'overwriteInitial' => true
                        ]
                    ]); ?>
            </div>
            <div class="row col-lg-12">
                <div class="form-group">
                    <?= Html::submitButton('Importar Planilha', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div>

