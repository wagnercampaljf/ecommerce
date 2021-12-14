<?php

/* @var $this yii\web\View */
/* @var $model common\models\EnderecoFilial */
/* @var $form ActiveForm */

use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = Yii::t('app', 'Editar Endereço');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Minha Conta'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Editar Endereço');

$cityDesc = empty($model->cidade) ? '' : $model->cidade->getLabel();
?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 usuario-update">
    <div class="portlet light grey">
        <div class="portlet-title">
            <div class="caption caption-md caption-subject">
                <?= Yii::$app->user->identity->filial->nome ?>
            </div>
        </div>
        <div class="portlet-body text-muted">
            <?php $form = ActiveForm::begin([]) ?>

            <?= $form->field($model, 'cep')->textInput() ?>
            <?= $form->field($model, 'logradouro')->textInput() ?>
            <?= $form->field($model, 'numero')->textInput() ?>
            <?= $form->field($model, 'complemento')->textInput() ?>
            <?= $form->field($model, 'bairro')->textInput() ?>
            <?= $form->field($model, 'cidade_id')->widget(Select2::className(), [
                'initValueText' => $cityDesc, // set the initial display text
                'options' => ['placeholder' => 'Search for a city ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => Url::to(['get-cidade']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
//                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
//                    'templateResult' => new JsExpression('function(city) { return city.text; }'),
//                    'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                ],
            ]) ?>
            <?= $form->field($model, 'referencia')->textarea() ?>

            <hr>

            <div class="form-actions text-right">
                <?= Html::a('<i class="fa fa-arrow-circle-left"></i> Voltar', ['/minha-conta'],
                    ['class' => 'btn btn-warning']) ?>
                <?= Html::submitButton('<i class="fa fa-check-circle"></i> Editar', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
