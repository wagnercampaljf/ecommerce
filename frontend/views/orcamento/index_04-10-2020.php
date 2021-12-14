<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 09/12/2015
 * Time: 16:28
 */

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\Orcamento */

use common\models\Filial;
use common\models\Produto;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Solicitar Orçamento';
$this->params['breadcrumbs'][] = $this->title;
$pecaId = null;
$filialId = null;
$texto = " ";
$pecaId = Yii::$app->request->get('peca');
$filialId = Yii::$app->request->get('filial');
if (!is_null($pecaId) && !is_null($filialId)) {
    $produto = Produto::find()->readyToView()->andWhere(['produto.id' => $pecaId])->one();
    $filial = Filial::find()->andWhere(['filial.id' => $filialId])->one();
    $texto = 'Olá, 
Gostaria de solicitar um Orçameto da Peça:
' . $produto->nome . '
com código:
' . $produto->codigo . '
da Loja:
' . $filial->nome . '
Obrigado.
    ';
} else if (!is_null($pecaId) && is_null($filialId)) {
    $produto = Produto::find()->readyToView()->andWhere(['produto.id' => $pecaId])->one();
    $filial = Filial::find()->andWhere(['filial.id' => $filialId])->one();
    $texto = 'Olá, 
Gostaria de solicitar um Orçameto da Peça:
' . $produto->nome . '
com código:
' . $produto->codigo . '
Obrigado.
    ';
}

?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success">
            Obrigado por fazer a solicitação no Peça Agora. Responderemos o mais rápido possível!
        </div>

    <?php else: ?>

        <p>
            Caso não tenha encontrado a peça desejada em nosso site, faça sua solicitação de orçamento aqui.
        </p>

        <div class="row">
            <div class="col-lg-5">

                <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'subject')->hint('Ex: Punto 2009 ELX 1.4') ?>

                <?= $form->field($model, 'body')->textArea(['rows' => 12, 'value' => $texto]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Enviar Solicitação',
                        ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>

    <?php endif; ?>
</div>