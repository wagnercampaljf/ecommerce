<?php

use common\models\CategoriaBanner;
use common\models\PosicaoBanner;
use common\models\Banner;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\redactor\widgets\Redactor;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Banner */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs('
    $(".form_datetime").datepicker({
        format: "dd/mm/yyyy",
        autoclose: true,
        todayHighlight: true
    });
    $("#tipo-link").on(\'switchChange.bootstrapSwitch\', function(event,state){
        if(state){
            $("#divPdf").addClass("hide");
            $("#divLink").removeClass("hide");
        }else{
            $("#divLink").addClass("hide");
            $("#divPdf").removeClass("hide");
        }
    });
    if($("#tipo-link").bootstrapSwitch(\'state\')){
        $("#divPdf").addClass("hide");
        $("#divLink").removeClass("hide");
    }else{
        $("#divLink").addClass("hide");
        $("#divPdf").removeClass("hide");
    }
');

$configSelect2 = [
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 3,
        'ajax' => [
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {q:params.term}; }')
        ],
    ],
];
$initValue = [];
if (!$model->isNewRecord) {
    $initValue = [
        'cidade' => ($model->cidade ? $model->cidade->nome : ''),
        'fabricante' => ($model->fabricante ? $model->fabricante->nome : ''),
        'produto' => ($model->produto ? $model->produto->nome : ''),
    ];
    $model->subcategoria_id = ArrayHelper::getColumn($model->subcategorias, 'id');
    $model->categoriaBanner_id = ArrayHelper::getColumn($model->categoriaBanners, 'id');
}
?>

<div class="banner-form">
    <div class="portlet light">
        <div class="portlet-body">
            <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'fieldConfig' => [
                    'options' => [
                        'class' => 'form-group col-lg-6 col-md-6 col-sm-12 col-xs-12',
                    ]
                ]
            ]); ?>
            <!--            --><?php //echo "<pre>";var_dump(Url::to(['get-categoria-banner']));die;?>
            <div class="row">
                <?= $form->field($model, 'nome')->textInput(['maxlength' => 300]) ?>

                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                    <label class="control_label">Data do Banner</label>

                    <div class="input-group date-timepicker input-daterange">
                        <?= $form->field($model, 'data_inicio',
                            ['template' => '{input}{hint}', 'options' => ['class' => 'form-group']])
                            ->textInput([
                                'class' => 'form-control date form_datetime',
                                'placeholder' => 'Início'
                            ]) ?>
                        <span class="input-group-addon">até</span>
                        <?= $form->field($model, 'data_fim',
                            ['template' => '{input}{hint}', 'options' => ['class' => 'form-group']])
                            ->textInput([
                                'class' => 'form-control date form_datetime',
                                'placeholder' => 'Fim'
                            ]) ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-lg-3 col-md-3 col-sm-12">
                    <label class="control-label">Link</label>

                    <p>
                        <?= Html::checkbox('tipo-link', ($model->link ? true : false), [
                            'class' => "make-switch form-control margin-top-10",
                            'data-handle-width' => '60',
                            'data-on-text' => "<i class='fa fa-link'></i> URL",
                            'data-on-color' => "primary",
                            'data-off-text' => "<i class='fa fa-file-pdf-o'></i> PDF",
                            'data-off-color' => "success",
                            'id' => 'tipo-link'
                        ]); ?>
                    </p>
                </div>
                <?= $form->field($model, 'link', [
                    'options' => [
                        'class' => 'form-group col-lg-9 col-md-9 col-sm-12 hide',
                        'id' => 'divLink'
                    ]
                ])->textInput(['maxlength' => 400]) ?>

                <?= $form->field($model, 'pdf', [
                    'options' => [
                        'class' => 'form-group col-lg-9 col-md-9 col-sm-12 hide',
                        'id' => 'divPdf'
                    ]
                ])->widget(FileInput::className(), [
                    'options' => ['accept' => '.pdf'],
                    'pluginOptions' => [
                        'showUpload' => false,
                        'showPreview' => false,
                        'browseIcon' => '<i class="fa fa-file-pdf-o"></i> ',
                        'browseLabel' => 'Selecione um pdf',
                        'initialPreview' => empty($model->pdf) ? '' : Html::tag('object', null,
                            [
                                'class' => 'file-preview-text',
                                'data' => 'data:pdf;base64,' . stream_get_contents($model->pdf)
                            ]),
                        'overwriteInitial' => true,
                    ]
                ]) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'categoriaBanner_id')
                    ->widget(Select2::className(), ArrayHelper::merge($configSelect2, [
                        'data' => ArrayHelper::map($model->categoriaBanners, 'id', 'nome'),
                        'options' => ['placeholder' => 'Procure uma Categoria de Banner...'],
                        'pluginOptions' => [
                            'ajax' => [
                                'url' => Url::to(['get-categoria-banner']),
                                'data' => new JsExpression('function(params) {
                                    return {
                                        q:params.term
                                    };
                                }')
                            ],
                            'multiple' => true
                        ]
                    ])) ?>

                <?= $form->field($model, 'posicao_id')->widget(Select2::className(), [
                    'data' => ArrayHelper::map(PosicaoBanner::find()->all(), 'id', 'nome'),
                    'options' => ['placeholder' => 'Selecione uma Posição']
                ]) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'cidade_id')
                    ->widget(Select2::className(), ArrayHelper::merge($configSelect2, [
                        'initValueText' => ArrayHelper::getValue($initValue, 'cidade'),
                        'options' => ['placeholder' => 'Procure uma Cidade...'],
                        'pluginOptions' => ['ajax' => ['url' => Url::to(['get-cidade'])]]
                    ])) ?>

                <?= $form->field($model, 'subcategoria_id')
                    ->widget(Select2::className(), ArrayHelper::merge($configSelect2, [
                        'data' => ArrayHelper::map($model->subcategorias, 'id', 'nome'),
                        'options' => ['placeholder' => 'Procure uma SubCategoria...'],
                        'pluginOptions' => [
                            'ajax' => [
                                'url' => Url::to(['get-sub-categoria']),
                                'data' => new JsExpression('function(params) {
                                    return {
                                        q:params.term
                                    };
                                }')
                            ],
                            'multiple' => true
                        ]
                    ])) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'fabricante_id')
                    ->widget(Select2::className(), ArrayHelper::merge($configSelect2, [
                        'initValueText' => ArrayHelper::getValue($initValue, 'fabricante'),
                        'options' => ['placeholder' => 'Procure um Fabricante...'],
                        'pluginOptions' => [
                            'ajax' => [
                                'url' => Url::to(['get-fabricante']),
                                'data' => new JsExpression('function(params) {
                                    return {
                                        q:params.term,
                                        subcategoria_id: $("#' . Html::getInputId($model, 'subcategoria_id') . '").val(),
                                    };
                                }')
                            ]
                        ]
                    ])) ?>

                <?= $form->field($model, 'produto_id')
                    ->widget(Select2::className(), ArrayHelper::merge($configSelect2, [
                        'initValueText' => ArrayHelper::getValue($initValue, 'produto'),
                        'options' => ['placeholder' => 'Procure um Produto...'],
                        'pluginOptions' => [
                            'ajax' => [
                                'url' => Url::to(['get-produto']),
                                'data' => new JsExpression('function(params) {
                                    return {
                                        q:params.term,
                                        fabricante_id: $("#' . Html::getInputId($model, 'fabricante_id') . '").val(),
                                        subcategoria_id: $("#' . Html::getInputId($model, 'subcategoria_id') . '").val(),
                                    };
                                }')
                            ]
                        ]
                    ])) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'descricao')->widget(Redactor::className()) ?>

                <?= $form->field($model, 'imagem',
                    ['options' => ['class' => 'form-group col-lg-6 col-md-6 col-sm-12']])->widget(FileInput::className(),
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
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Criar Anúncio') : Yii::t('app', 'Salvar Alterações'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
