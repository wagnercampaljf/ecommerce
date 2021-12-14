<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\MudarSenhaForm */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Mudar Senha');
$this->params['active'] = 'dados';
$this->params['breadcrumbs'][] = ['label' => 'Minha Conta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-lg-12 col-md-12 col-sm-12 mudar-senha-update">
    <div class="portlet light grey mudar-senha-form">
        <div class="portlet-title">
            <div class="caption caption-md caption-subject">
                <?= Html::encode($this->title) ?>
            </div>
        </div>
        <div class="portlet-body text-muted">
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'options' => ['class' => 'form-group col-lg-6 col-md-6 col-sm-6 col-xs-12']
                ],
                'errorSummaryCssClass' => 'alert alert-danger error-summary',
                'enableAjaxValidation' => true
            ]); ?>
            <?= $form->errorSummary([$model],
                [
                    'header' => Html::button('', [
                            'class' => 'close',
                            'data-dismiss' => 'alert',
                            'aria-hidden' => true
                        ]) . Html::tag('p', Yii::t('yii', 'Please fix the following errors:'))
                ]); ?>

            <div class="row">
                <?= $form->field($model, 'password',
                    [
                        'options' => ['class' => 'form-group col-lg-12 col-md-12 col-sm-12 col-xs-12']
                    ])->passwordInput() ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'new_password')->passwordInput() ?>
                <?= $form->field($model, 'repeat_password')->passwordInput() ?>
            </div>

            <div class="form-actions text-right">
                <?= Html::a('<i class="fa fa-arrow-circle-left"></i> Voltar', ['/minha-conta'],
                    ['class' => 'btn btn-warning']) ?>
                <?= Html::submitButton('<i class="fa fa-check-circle"></i> Mudar', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
