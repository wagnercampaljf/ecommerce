<?php

use common\models\Caracteristica;
use common\models\CaracteristicaFilial;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\CaracteristicaFilial */

$this->title = Yii::t('app', '{modelClass}', [
    'modelClass' => 'Características',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Características'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('
    $(".habilitado").change(function(){
        var k = $(this).data("line");
        $(".caract_"+k).attr("disabled",!$(this).is(":checked"));
    });
');

$filial_id = Yii::$app->user->identity->filial_id;
?>
<div class="caracteristica-filial-create flip-scroll">
    <?php $form = ActiveForm::begin() ?>
    <table class="table table-bordered flip-content">
        <thead>
        <tr>
            <th>Habilitado?</th>
            <th>Característica</th>
            <th>Observação</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $caracteristicas = Caracteristica::find()->all();
        foreach ($caracteristicas as $k => $caracteristica):
            if (!$model = CaracteristicaFilial::findOne([
                'filial_id' => $filial_id,
                'caracteristica_id' => $caracteristica->id
            ])
            ) {
                $model = new CaracteristicaFilial();
            }
            ?>
            <tr>
                <td class="text-center">
                    <?= Html::checkbox('habilitado_' . $k, !$model->isNewRecord,
                        [
                            'class' => 'form-control habilitado',
                            'data' => ['line' => $k]
                        ]); ?>
                </td>
                <td>
                    <?= Html::activeHiddenInput($model, "[$k]caracteristica_id",
                        [
                            'class' => 'form-control caract_' . $k,
                            'disabled' => $model->isNewRecord,
                            'value' => $caracteristica->id
                        ]); ?>
                    <?= Html::textInput('', $caracteristica->nome, ['class' => 'form-control', 'disabled' => true]); ?>
                </td>
                <td>
                    <?= $form->field($model, "[$k]observacao",
                        ['template' => '{input}'])->textarea([
                        'class' => 'form-control caract_' . $k,
                        'disabled' => $model->isNewRecord
                    ]); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Atualizar Características'), ['class' => 'btn btn-success']); ?>
    </div>
    <?php ActiveForm::end() ?>
</div>
