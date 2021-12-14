<?php


/* @var $this yii\web\View */
use common\models\FilialTransportadora;
use common\models\Transportadora;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model common\models\FilialTransportadora */

$this->title = Yii::t('app', 'Atualizar {modelClass}', [
    'modelClass' => 'Transportadoras',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Filial Transportadoras'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('
    $(".habilitado").change(function(){
        var k = $(this).data("line");
        $(".transportadora_"+k).attr("disabled",!$(this).is(":checked"));
    });
');

$filial_id = Yii::$app->user->identity->filial_id;
?>
<div class="filial-transportadora-create">
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <span class="caption-subject bold uppercase h2">
                    <?= $this->title ?>
                </span>
            </div>
        </div>
        <div class="portlet-body">
            <?php $form = ActiveForm::begin(); ?>
            <table class="table table-bordered flip-content">
                <thead>
                <tr>
                    <th>Habilitado?</th>
                    <th>Transportadora</th>
                    <th>Dias para Postagem</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $transportadoras = Transportadora::find()->all();
                foreach ($transportadoras as $k => $transportadora):
                    if (!$model = FilialTransportadora::findOne([
                        'filial_id' => $filial_id,
                        'transportadora_id' => $transportadora->id
                    ])
                    ) {
                        $model = new FilialTransportadora();
                    }
                    ?>
                    <tr>
                        <td>
                            <?= Html::checkbox('habilitado_' . $k, !$model->isNewRecord,
                                [
                                    'class' => 'form-control habilitado',
                                    'data' => ['line' => $k, 'transportadora' => $transportadora->id]
                                ]); ?>
                        </td>
                        <td>
                            <?= Html::activeHiddenInput($model, "[$k]transportadora_id",
                                [
                                    'class' => 'form-control transportadora_' . $k,
                                    'disabled' => $model->isNewRecord,
                                    'value' => $transportadora->id
                                ]); ?>
                            <?= Html::textInput('', $transportadora->nome,
                                ['class' => 'form-control', 'disabled' => true]); ?>
                        </td>
                        <td>
                            <?= $form->field($model, "[$k]dias_postagem", ['template' => '{input}'])->textInput([
                                'class' => 'form-control transportadora_' . $k,
                                'disabled' => $model->isNewRecord,
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Atualizar Transportadoras'), ['class' => 'btn btn-success']); ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
