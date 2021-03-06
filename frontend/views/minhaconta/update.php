<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Comprador */
/* @var $form yii\widgets\ActiveForm */


$this->title = Yii::t('app', 'Meus Dados');
$this->params['active'] = 'dados';
$this->params['breadcrumbs'][] = ['label' => 'Minha Conta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-pane active col-lg-9 col-md-9 col-sm-12 comprador-update">
    <div class="panel panel-primary comprador-form">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'options' => ['class' => 'form-group col-lg-6 col-md-6 col-sm-6 col-xs-12']
                ],
                'errorSummaryCssClass' => 'alert alert-danger error-summary'
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
                <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'cpf')->textInput(['maxlength' => 14])->hint("Somente números")->label("CPF *") ?>
                <?= ""//$form->field($model, 'cpf')->textInput(['maxlength' => true, 'type' => 'number']) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'cargo')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="form-group text-right">
                <?= Html::submitButton(Yii::t('app', 'Alterar'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app', 'Cancelar'), ['dados'], ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
