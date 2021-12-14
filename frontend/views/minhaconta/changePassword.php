<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\MudarSenhaForm */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Meus Dados');
$this->params['active'] = 'dados';
$this->params['breadcrumbs'][] = ['label' => 'Minha Conta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-pane active col-lg-9 col-md-9 col-sm-12 mudar-senha-update">
    <div class="panel panel-primary mudar-senha-form">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
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

            <div class="form-group text-right">
                <?= Html::submitButton(Yii::t('app', 'Mudar'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app', 'Cancelar'), ['dados'], ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
