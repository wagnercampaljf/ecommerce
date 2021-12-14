<?php

use kartik\file\FileInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Marca;

/* @var $this yii\web\View */
/* @var $model common\models\Marca */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="marca-form">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'options' => [
                'class' => 'form-group',
            ]
        ]
    ]); ?>

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'imagem',
            ['options' => ['class' => 'form-group col-lg-12 col-md-12 col-sm-12']])->widget(FileInput::className(),
            [
                'options' => ['accept' => 'image/jpeg, image/png'],
                'pluginOptions' => [
                    'showUpload' => false,
                    'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                    'browseLabel' => 'Selecione uma Imagem',
                    'initialPreview' => $model->getImg(['class' => 'file-preview-image']),
                    'overwriteInitial' => true
                ]
            ]); ?>
    </div>

    <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12 form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Criar Marca' : 'Salvar Alterações', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
