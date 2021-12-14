c<?php

use common\models\CategoriaModelo;
use common\models\Marca;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Modelo */
/* @var $form yii\widgets\ActiveForm */
$data = [
    '2018' => '2018', '2017' => '2017', '2016' => '2016', '2015' => '2015', '2014' => '2014', '2013' => '2013', '2012' => '2012', '2011' => '2011', '2010' => '2010', '2009' => '2009',
    '2008' => '2008', '2007' => '2007', '2006' => '2006', '2005' => '2005', '2004' => '2004', '2003' => '2003', '2002' => '2002', '2001' => '2001',
    '2000' => '2000', '1999' => '1999', '1998' => '1998', '1997' => '1997', '1996' => '1996', '1995' => '1995', '1994' => '1994', '1993' => '1993',
    '1992' => '1992', '1991' => '1991', '1990' => '1990', '1989' => '1989', '1988' => '1988', '1987' => '1987', '1986' => '1986', '1985' => '1985', '1984' => '1984',
    '1983' => '1983', '1982' => '1982', '1981' => '1981', '1980' => '1980', 'Todos' => 'Todos', '-' => '-'
];
?>

<div class="modelo-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'marca_id')->widget(Select2::className(), [
                'data' => ArrayHelper::map(
                    Marca::find()->orderBy(['marca.nome' => SORT_ASC])->all(),
                    'id',
                    'nome'
                ),
                'options' => ['placeholder' => 'Selecione uma Montadora']
            ]) ?>

            <?= $form->field($model, 'categoria_modelo_id')->widget(Select2::className(), [
                'data' => ArrayHelper::map(
                    CategoriaModelo::find()->orderBy(['categoria_modelo.nome' => SORT_ASC])->all(),
                    'id',
                    'nome'
                ),
                'options' => ['placeholder' => 'Selecione uma Categoria de Modelo']
            ]) ?>
            <?php

            ?>
            <?php
            $model->anoModelo = ArrayHelper::getColumn($model->anoModelos, 'nome');

            echo $form->field($model, 'anoModelo')
                ->widget(Select2::className(), [
                    'data' => $data,
                    'options' => ['placeholder' => 'Selecione os Anos do Modelos', 'multiple' => true],
                    'pluginOptions' => [
                        'tags' => true,
                        'tokenSeparators' => [',', ' '],
                    ]
                ])->label("Anos do Modelo") ?>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Criar Modelo' : 'Salvar Altereções', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
