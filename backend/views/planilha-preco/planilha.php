<?php
/* @var $this yii\web\View */

use common\models\Filial;
use vendor\iomageste\Moip\Moip;
use yii\bootstrap\Progress;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\select2\Select2;
use kartik\file\FileInput;
use yii\widgets\MaskedInput;


$this->title = 'Presificar Preço Planilha';
$this->params['breadcrumbs'][] = ['label' => ' / ' . $this->title];

/* @var $this yii\web\View */
/* @var $model common\models\Filial */
/* @var $form yii\widgets\ActiveForm */

?>



<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Importar Planilha para Presificação</strong></div>
        <div class="panel-body">
            <h4>Selecione uma Filial:</h4>
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <?php
            $filial = $model->id ? Filial::findOne($model->id)->nome : null;
            echo  $form->field($model, 'id')->widget(Select2::className(), [
                'initValueText' => $filial,
                'pluginOptions' => ['allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => ['url' => Url::to(['produto-filial/get-filial']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                ],
                'options' => ['placeholder' => 'Selecione uma Filial']
            ])->label("")
            ?>
            <br>

            <div class="container">
                <h4>Selecione a Coluna de Acordo:</h4>
                <div class="row">
                    <div class="col-sm-2">
                        <?php
                        echo  $form->field($model, 'coluna_codigo_fabricante')->widget(Select2::className(), [
                            'data' => [0=>0, 1=>1, 2=>2, 3=>3,4=>4, 5=>5,6=>6, 7=>7,8=>8, 9=>9,10=>10, 11=>11,12=>12, 13=>13,14=>14, 15=>15,16=>16, 17=>17,18=>18, 19=>19,20=>20,],

                            'options' => ['placeholder' => '']
                        ])->label("Codigo Fabricante")
                        ?>
                    </div>
                    <div class="col-sm-2">
                        <?php
                        echo  $form->field($model, 'coluna_estoque')->widget(Select2::className(), [
                            'data' => [0=>0, 1=>1, 2=>2, 3=>3,4=>4, 5=>5,6=>6, 7=>7,8=>8, 9=>9,10=>10, 11=>11,12=>12, 13=>13,14=>14, 15=>15,16=>16, 17=>17,18=>18, 19=>19,20=>20,],
                            'options' => ['placeholder' => '']
                        ])->label("Estoque")
                        ?>
                    </div>
                    <div class="col-sm-2">
                        <?php
                        echo  $form->field($model, 'coluna_preco')->widget(Select2::className(), [
                            'data' => [0=>0, 1=>1, 2=>2, 3=>3,4=>4, 5=>5,6=>6, 7=>7,8=>8, 9=>9,10=>10, 11=>11,12=>12, 13=>13,14=>14, 15=>15,16=>16, 17=>17,18=>18, 19=>19,20=>20,],
                            'options' => ['placeholder' => '']
                        ])->label("Preço Compra")
                        ?>
                    </div>
                </div>
            </div>



            <br><br>


            <?= $form->field($model, 'file_planilha',
                ['options' => ['class' => 'form-group col-lg-6 col-md-6 col-sm-6']])->widget(FileInput::className(),
                [
                    'options' => ['accept' => 'file_planilha/csv,'],
                    'pluginOptions' => [
                        //'showUpload' => false,
                        //'browseIcon' => '<i class="glyphicon glyphicon-picture"></i> ',
                        'allowedFileExtensions'=>['csv'],
                       'showPreview' => false,
                        'showCaption' => true,
                        'showRemove' => true,
                        'showUpload' => false,
                        'browseLabel' => 'Subir Planilha Preço',
                        //'initialPreview' => $model->getImg(['class' => 'file-preview-image'],false),
                        'overwriteInitial' => true
                    ]
                ]);
            ?>

            <button class="btn btn-primary" type="submit">Salva e presificar</button><br><br>
            <?php ActiveForm::end() ?>


            <br><br>

            <!-- Progress Bar -->
            <h3>Progresso</h3>
            <div class="progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
                </div>
            </div>

            <!-- Upload Finished -->
            <div class="js-upload-finished">
                <h3>Arquivos processados</h3>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-success"><span class="badge alert-success pull-right">Success</span>atualizacao-preco.csv</a>
                    <a href="#" class="list-group-item list-group-item-success"><span class="badge alert-success pull-right">Success</span>atualizacao-preco.csv</a>
                </div>
            </div>


        </div>
    </div>
</div>

<br><br>
<!-- /container -->





