<?php

/* @var $this yii\web\View */
/* @var $model common\models\Filial */
/* @var $form ActiveForm */

use common\models\TipoEmpresa;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Editar Empresa: {nome}', [
    'nome' => $model->nome
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Minha Conta'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Editar Empresa');
?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 filial-update">
    <div class="portlet light grey">
        <div class="portlet-title">
            <div class="caption caption-md caption-subject">
                <?= $model->nome ?>
            </div>
        </div>
        <div class="portlet-body text-muted">
            <?php $form = ActiveForm::begin([]) ?>

            <?= $form->field($model, 'nome')->textInput() ?>
            <?= $form->field($model, 'razao')->textInput() ?>
            <?= $form->field($model, 'documento')->textInput() ?>
            <?= $form->field($model,
                'id_tipo_empresa')->dropDownList(
                ArrayHelper::map(TipoEmpresa::find()->orderBy(['nome' => SORT_ASC])->all(), 'id', 'nome')) ?>
            <?= $form->field($model, 'telefone')->textInput() ?>
            <?= $form->field($model, 'telefone_alternativo')->textInput() ?>

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
