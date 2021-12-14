<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Newsletter */

?>
<div class="clearfix newsletter-create col-xs-12 col-sm-8 col-md-12 col-lg-10 col-lg-offset-1">
    <div class="newsletter-form">

        <div class='h4' style="padding: 10px;">Receba nossas Novidades e Promoções por E-mail!</div>

        <?php $form = ActiveForm::begin(['action' => \yii\helpers\Url::to(['/site/newsletter-create'])]); ?>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <?= $form->field($model, 'nome')->textInput(['maxlength' => 150]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <?= $form->field($model, 'email')->textInput(['maxlength' => 150]) ?>
        </div>
        <div class="col-xs-12 col-sm-9 col-md-4 col-lg-4">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Assunto, veículos:</div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <?= $form->field($model, 'Leves')->checkbox(['value' => true]) ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <?= $form->field($model, 'Pesados')->checkbox(['value' => true]) ?>
            </div>
        </div>

        <div class="form-group" style="margin-left: 15px;">
            <?= Html::submitButton('Inscrever-se',
                ['class' => 'btn btn-primary']) ?>
        </div>


        <?php ActiveForm::end(); ?>

    </div>

</div>
