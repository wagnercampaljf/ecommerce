<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\redactor\widgets\Redactor;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model common\models\SubCategoria */
/* @var $form yii\widgets\ActiveForm */
$model->categoria_id = null;
?>
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js" type="text/javascript"></script>
<div class="sub-categoria-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'descricao')->widget(Redactor::className()) ?>

    <?= $form->field($model, 'ativo')->checkbox() ?>

    <?php
    echo $form->field($model, 'categoria_id')->dropDownList(
        $categorias,
        [
            'class' => 'form-control select2',
            'id' => 'cat-id',
            'prompt' => 'Selecione uma categoria'
        ])->label("Categoria *");
    ?>


    <?= $form->field($model, 'meli_id', [
        'inputOptions' => [
            'class' => 'form-control',
            'name' => 'myCustomId'
        ]
    ])->widget(DepDrop::classname(), [
        'options' => ['id' => 'subcat-id', 'name' => 'subcat-id[]'],
        'pluginOptions' => [
            'depends' => ['cat-id'],
            'loading' => false,
            'prompt' => 'Selecione uma categoria',
            'placeholder' => 'Selecione uma subcategoria',
            'url' => Url::to(['/sub-categoria/get-categorias-meli']),
        ]
    ])->label(false) ?>

    <?= $form->field($model, 'meli_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'subcat2-id', 'name' => 'subcat-id[]'],
        'pluginOptions' => [
            'depends' => ['subcat-id'],
            'loading' => false,
            'prompt' => 'Selecione uma subcategoria',
            'placeholder' => 'Selecione uma subcategoria',
            'url' => Url::to(['/sub-categoria/get-sub-categorias-meli'])
        ]
    ])->label(false) ?>

    <?= $form->field($model, 'meli_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'subcat3-id', 'name' => 'subcat-id[]'],
        'pluginOptions' => [
            'depends' => ['subcat2-id'],
            'loading' => false,
            'prompt' => 'Selecione uma subcategoria',
            'placeholder' => 'Selecione uma subcategoria',
            'url' => Url::to(['/sub-categoria/get-sub-categorias-meli'])
        ]
    ])->label(false) ?>

    <?= $form->field($model, 'meli_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'subcat4-id', 'name' => 'subcat-id[]'],
        'pluginOptions' => [
            'depends' => ['subcat3-id'],
            'loading' => false,
            'placeholder' => 'Selecione uma subcategoria',
            'prompt' => 'Selecione uma subcategoria',
            'url' => Url::to(['/sub-categoria/get-sub-categorias-meli']),
        ]
    ])->label(false) ?>

    <?= $form->field($model, 'meli_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'subcat5-id', 'name' => 'subcat-id[]'],
        'pluginOptions' => [
            'depends' => ['subcat4-id'],
            'loading' => false,
            'placeholder' => 'Selecione uma subcategoria',
            'prompt' => 'Selecione uma subcategoria',
            'url' => Url::to(['/sub-categoria/get-sub-categorias-meli']),
        ]
    ])->label(false) ?>

    <?= $form->field($model, 'meli_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'subcat6-id', 'name' => 'subcat-id[]'],
        'pluginOptions' => [
            'depends' => ['subcat5-id'],
            'loading' => false,
            'placeholder' => 'Selecione uma subcategoria',
            'prompt' => 'Selecione uma subcategoria',
            'url' => Url::to(['/sub-categoria/get-sub-categorias-meli']),
        ]
    ])->label(false) ?>

    <?= $form->field($model, 'meli_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'subcat7-id', 'name' => 'subcat-id[]'],
        'pluginOptions' => [
            'depends' => ['subcat6-id'],
            'loading' => false,
            'placeholder' => 'Selecione uma subcategoria',
            'prompt' => 'Selecione uma subcategoria',
            'url' => Url::to(['/sub-categoria/get-sub-categorias-meli']),
        ]
    ])->label(false) ?>

    <?= $form->field($model, 'meli_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'subcat8-id', 'name' => 'subcat-id[]'],
        'pluginOptions' => [
            'depends' => ['subcat7-id'],
            'loading' => false,
            'placeholder' => 'Selecione uma subcategoria',
            'prompt' => 'Selecione uma subcategoria',
            'url' => Url::to(['/sub-categoria/get-sub-categorias-meli']),
        ]
    ])->label(false) ?>

    <?= $form->field($model, 'meli_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'subcat9-id', 'name' => 'subcat-id[]'],
        'pluginOptions' => [
            'depends' => ['subcat8-id'],
            'loading' => false,
            'placeholder' => 'Selecione uma subcategoria',
            'prompt' => 'Selecione uma subcategoria',
            'url' => Url::to(['/sub-categoria/get-sub-categorias-meli']),
        ]
    ])->label(false) ?>

    <?= $form->field($model, 'meli_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'subcat10-id', 'name' => 'subcat-id[]'],
        'pluginOptions' => [
            'depends' => ['subcat9-id'],
            'loading' => false,
            'placeholder' => 'Selecione uma subcategoria',
            'prompt' => 'Selecione uma subcategoria',
            'url' => Url::to(['/sub-categoria/get-sub-categorias-meli']),
        ]
    ])->label(false) ?>

    <?= $form->field($model, 'meli_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'subcat11-id', 'name' => 'subcat-id[]'],
        'pluginOptions' => [
            'depends' => ['subcat10-id'],
            'loading' => false,
            'placeholder' => 'Selecione uma subcategoria',
            'prompt' => 'Selecione uma subcategoria',
            'url' => Url::to(['/sub-categoria/get-sub-categorias-meli']),
        ]
    ])->label(false) ?>

    <?= $form->field($model, 'meli_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'subcat12-id', 'name' => 'subcat-id[]'],
        'pluginOptions' => [
            'depends' => ['subcat11-id'],
            'loading' => false,
            'placeholder' => 'Selecione uma subcategoria',
            'prompt' => 'Selecione uma subcategoria',
            'url' => Url::to(['/sub-categoria/get-sub-categorias-meli']),
        ]
    ])->label(false) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Criar' : 'Editar',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>