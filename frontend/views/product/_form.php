<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Respostas */
/* @var $form ActiveForm */
?>
<div class="respostas-_form list-group text-left">

    <?php $form = ActiveForm::begin(['action' => Url::to(['/respostas/responder'])]);
    echo $form->field($model, 'opcao_id')
        ->radioList(
            $model->getOpcoes(),
            [
                'item' => function ($index, $label, $name, $checked, $value) {

                    $return = '<label class="list-group-item opcao-resposta">';
                    $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" tabindex="3">';
                    $return .= ' ';
                    $return .= $label;
                    $return .= '</label>';

                    return $return;
                }
            ]
        )
//        ->radioList($model->getOpcoes())
        ->label(false);
    echo $form->field($model, 'produto_id')->hiddenInput(['value' => $produtoId])->label(false);
    ?>
    <div class="form-group">
        <?= Html::submitButton('Responder', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div><!-- respostas-_form -->
