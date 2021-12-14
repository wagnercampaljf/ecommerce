<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

$form = ActiveForm::begin([
    'options' => ['class' => 'form form-search ' . $container],
    'action' => Url::to('search'),
    'method' => 'get'
]);

?>

<?= $form->field($model, 'categoria_modelo_id', ['options' => ['class' => $formGroupClass]])->dropDownList(
    ArrayHelper::map(\common\models\CategoriaModelo::find()->ordemAlfabetica()->all(), 'id', 'nome'),
    [
        'name' => 'categoria_modelo_id',
        'class' => ' form-control select2 select-dep',
        'prompt' => 'Categoria',
        'select-dep-target' => '#' . Html::getInputId($model, 'marca_id'),
        'select-dep-url' => Yii::$app->urlManager->createUrl('/site/marcas-x-categoria'),
    ]

) ?>
<?= $form->field($model, 'marca_id', ['options' => ['class' => $formGroupClass]])->dropDownList(
    [],
    [
        'name' => 'marca_id',
        'class' => ' form-control select2 select-dep',
        'prompt' => 'Marca',
        'select-dep-target' => '#' . Html::getInputId($model, 'modelo_id'),
        'select-dep-url' => Yii::$app->urlManager->createUrl('/site/modelos-x-categoria-x-marca'),
        'select-dep-send-categoria' => '#' . Html::getInputId($model, 'categoria_modelo_id'),
    ]
) ?>

<?= $form->field($model, 'modelo_id',
    [
        'options' => [
            'class' => (strpos(
                $formGroupClass,
                'col-'
            ) !== false ? 'form-group col-md-4 col-sm-4' : $formGroupClass)
        ]
    ]
)->dropDownList(
    [],
    [
        'name' => 'modelo_id',
        'class' => ' form-control select2 select-dep',
        'prompt' => 'Modelo',
        'select-dep-target' => '#' . Html::getInputId($model, 'ano_id'),
        'select-dep-url' => Yii::$app->urlManager->createUrl('/site/anos-x-modelo'),
    ]
) ?>

<?= $form->field($model, 'ano_id', ['options' => ['class' => $formGroupClass]])->dropDownList(
    [],
    [
        'name' => 'ano_id',
        'class' => ' form-control select2',
        'prompt' => 'Ano',
    ]
) ?>


    <div class="form-group ">
        <?= Html::submitButton(
            'Pesquisar',
            ['class' => 'btn btn-primary', 'name' => 'search-button']
        ) ?>
    </div>
<?php ActiveForm::end(); ?>