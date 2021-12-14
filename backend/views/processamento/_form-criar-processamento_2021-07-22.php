<?php

use kartik\file\FileInput;
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

    <?php $form = ActiveForm::begin(
        ['options' => ['enctype'=>'multipart/form-data']]); ?>

    <div class="container">
        <?= $form->field($model, 'funcao_id')->widget(Select2::className(), [
            'data' => ArrayHelper::map(Funcao::find()->orderBy(['nome' => SORT_ASC])->all(),
                'id',
                'nome'
            ),
        'options' => ['placeholder' => 'Selecione uma Função']])
        ?>
    </div>

    <div class="container">
        <h4>Selecione a Coluna de Acordo:</h4>
        <div class="row">
            <div class="col-sm-2">
                <?=
                $form->field($model, 'coluna_codigo_fabricante')->widget(Select2::className(), [
                    'data' => [0=>0, 1=>1, 2=>2, 3=>3,4=>4, 5=>5,6=>6, 7=>7,8=>8, 9=>9,10=>10, 11=>11,12=>12, 13=>13,14=>14, 15=>15,16=>16, 17=>17,18=>18, 19=>19,20=>20,],
                    'options' => ['placeholder' => '','required'=>true,]
                ])->label("Codigo Fabricante")
                ?>
            </div>
            <div class="col-sm-2">
                <?=
                  $form->field($model, 'coluna_estoque')->widget(Select2::className(), [
                    'data' => [0=>0, 1=>1, 2=>2, 3=>3,4=>4, 5=>5,6=>6, 7=>7,8=>8, 9=>9,10=>10, 11=>11,12=>12, 13=>13,14=>14, 15=>15,16=>16, 17=>17,18=>18, 19=>19,20=>20,],
                    'options' => ['placeholder' => '','required'=>true,]
                ])->label("Estoque")
                ?>
            </div>
            <div class="col-sm-2">
                <?=
                 $form->field($model, 'coluna_preco_compra')->widget(Select2::className(), [
                    'data' => [0=>0, 1=>1, 2=>2, 3=>3,4=>4, 5=>5,6=>6, 7=>7,8=>8, 9=>9,10=>10, 11=>11,12=>12, 13=>13,14=>14, 15=>15,16=>16, 17=>17,18=>18, 19=>19,20=>20,],
                    'options' => ['placeholder' => '','required'=>true,]
                ])->label("Preço Compra")
                ?>
            </div>
            <div class="col-sm-2">
                <?=
                 $form->field($model, 'coluna_preco')->widget(Select2::className(), [
                    'data' => [0=>0, 1=>1, 2=>2, 3=>3,4=>4, 5=>5,6=>6, 7=>7,8=>8, 9=>9,10=>10, 11=>11,12=>12, 13=>13,14=>14, 15=>15,16=>16, 17=>17,18=>18, 19=>19,20=>20,],
                    'options' => ['placeholder' => '']
                ])->label("Preço Venda")
                ?>
            </div>
            <div class="col-sm-2">
                <?=
                 $form->field($model, 'coluna_nome')->widget(Select2::className(), [
                    'data' => [0=>0, 1=>1, 2=>2, 3=>3,4=>4, 5=>5,6=>6, 7=>7,8=>8, 9=>9,10=>10, 11=>11,12=>12, 13=>13,14=>14, 15=>15,16=>16, 17=>17,18=>18, 19=>19,20=>20,],
                    'options' => ['placeholder' => '']
                ])->label("Nome")
                ?>
            </div>
            <div class="col-sm-2">
                <?=
                 $form->field($model, 'coluna_capas')->widget(Select2::className(), [
                    'data' => [0=>0, 1=>1, 2=>2, 3=>3,4=>4, 5=>5,6=>6, 7=>7,8=>8, 9=>9,10=>10, 11=>11,12=>12, 13=>13,14=>14, 15=>15,16=>16, 17=>17,18=>18, 19=>19,20=>20,],
                    'options' => ['placeholder' => '']
                ])->label("Grupo (Capa Dib)")
                ?>
            </div>
        </div>
    </div>

    <div class="container">
        <?= $form->field($model, 'file_planilha',
        ['options' => ['class' => 'form-group col-lg-6 col-md-6 col-sm-6']])->widget(FileInput::className(),
        [
            'options' => [ 'enctype' => 'multipart/form-data'],
            'pluginOptions' => [
                //'showUpload' => false,
                //'browseIcon' => '<i class="glyphicon glyphicon-picture"></i> ',
                'allowedFileExtensions'=>['csv'],
                'showPreview' => false,
                'showCaption' => true,
                'showRemove' => true,
                'showUpload' => false,
                'browseLabel' => 'Subir Planilha',
                //'initialPreview' => $model->getImg(['class' => 'file-preview-image'],false),
                'overwriteInitial' => true
            ]
        ]);
        ?>
    </div>

    <div class="form-group">

        <?= Html::submitButton('Criar Processamento', ['class' => 'btn btn-success']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
