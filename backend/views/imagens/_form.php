<?php

use common\models\Produto;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Imagens */
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
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php
//                $loja = Yii::$app->request->get('filial_id') ? Filial::findOne(Yii::$app->request->get('filial_id'))->nome : null;
                $produto = $model->produto_id ? Produto::findOne($model->produto_id)->nome : null;
//                var_dump($produto);
//                die;
//                echo Select2::widget([
//                    'name' => 'produto_id',
//                    'initValueText' => $produto,
//                    'value' => $model->produto_id,
//                    'options' => [
//                        'placeholder' => 'Escolha um Produto',
//                        'id' => 'select_produto',
//                    ],
//                    'pluginOptions' => [
//                        'allowClear' => true,
//                        'minimumInputLength' => 3,
//                        'ajax' => [
//                            'url' => Url::to(['imagens/get-produto']),
//                            'dataType' => 'json',
//                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
//                        ],
//                    ],
//                    'pluginEvents' => [
//                        "select2:select" => 'function(){
//                                $(\'#produto_id\').attr(\'href\', $.urlParamChange(\'produto_id\',$(this).val()));
//                                $(\'#produto_id\').trigger(\'click\');
//                            }'
//                    ]
//                ]);
//                echo Html::a('', $href, ['id' => 'produto_id']);
               echo  $form->field($model, 'produto_id')->widget(Select2::className(), [
                   'initValueText' => $produto,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'ajax' => [
                            'url' => Url::to(['imagens/get-produto']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                    ],
//                    'data' => ArrayHelper::map(
//                        Produto::find()->orderBy(['produto.nome' => SORT_ASC])->all(),
//                        'id',
//                        'nome'
//                    ),
                    'options' => ['placeholder' => 'Selecione um Produto']
                ])->label("Imagem do Produto:") ?>
            </div>
            <div class="col-lg-1 col-md-2 col-sm-4 col-xs-4">
                <?= $form->field($model, 'ordem')->dropDownList(['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10']) ?>
            </div>
        </div>
        <div class="row col-lg-12">
            <div class="col-lg-6 col-md-4 col-sm-12 col-xs-12">
                <?= $form->field($model, 'imagem',
                    ['options' => ['class' => 'form-group col-lg-12 col-md-12 col-sm-12']])->widget(FileInput::className(),
                    [
                        'options' => ['accept' => 'image/jpeg, image/png'],
                        'pluginOptions' => [
                            'showUpload' => false,
                            'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                            'browseLabel' => 'Selecione uma Imagem',
                            'initialPreview' => $model->getImg($model, ['class' => 'file-preview-image', 'style' => 'width:100%']),
                            // 'initialPreview' => $model->getImg(['class' => 'file-preview-image']),
                            'overwriteInitial' => true
                        ]
                    ]); ?>
            </div>
            <div class="col-lg-6 col-md-4 col-sm-12 col-xs-12">
                <?= $form->field($model, 'imagem_sem_logo',
                    ['options' => ['class' => 'form-group col-lg-12 col-md-12 col-sm-12']])->widget(FileInput::className(),
                    [
                        'options' => ['accept' => 'image/jpeg, image/png'],
                        'pluginOptions' => [
                            'showUpload' => false,
                            'browseIcon' => '<i class="glyphicon glyphicon-picture"></i> ',
                            'browseLabel' => 'Subir Imagem Sem Logo',
                            'initialPreview' => $model->getImg($model, [
                                'class' => 'file-preview-image',
                                'class' => 'img-responsive thumbnail',
                                'style' => 'width:100%'
                            ], false),
                            // 'initialPreview' => $model->getImg([
                            //     'class' => 'file-preview-image',
                            //     'class' => 'img-responsive thumbnail',
                            //     'style' => 'width:100%'
                            // ], false),
                            'overwriteInitial' => true
                        ]
                    ]); ?>
            </div>
            <div class="row col-lg-12">

            </div>
            <div class="row col-lg-12">
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Cadastrar Imagem' : 'Atualizar Imagem', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

		    <?php if(!$model->isNewRecord) {?>
                        <?php $urlml = "atualizarml?id=".$model->id; ?>
                        <a class="btn  btn-warning" id="sinc-ml2" href="<?php echo $urlml; ?>">
                        	<i class="fa fa-shopping-cart" aria-hidden="true" ></i> Atualizar (Mercado Livre)
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
