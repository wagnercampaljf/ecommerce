<?php

/* @var $this yii\web\View */
/* @var $model common\models\Usuario */
/* @var $form ActiveForm */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Editar Representante: {nome}', [
    'nome' => $model->nome
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Minha Conta'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Editar Representante');
?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 usuario-update">
    <div class="portlet light grey">
        <div class="portlet-title">
            <div class="caption caption-md caption-subject">
                <?= $model->nome ?>
            </div>
        </div>
        <div class="portlet-body text-muted">
            <?php $form = ActiveForm::begin([]) ?>

            <?= $form->field($model, 'nome')->textInput() ?>
            <?= $form->field($model, 'cpf')->textInput() ?>
            <?= $form->field($model, 'cargo')->textInput() ?>
            <?= $form->field($model, 'email')->textInput() ?>

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
