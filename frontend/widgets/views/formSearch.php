<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

$href = [''] + Yii::$app->request->get();
$model->setAttributes(Yii::$app->request->get());

$form = ActiveForm::begin(['options' => ['class' => 'form form-search ' . $container]]);

$marca = [];
if (!is_null($model->categoria_modelo_id) && !empty($model->categoria_modelo_id)) {
    $marca = ArrayHelper::map(\common\models\Marca::find()->byCategoria($model->categoria_modelo_id)->ordemAlfabetica()->all(),
        'id', 'nome');
}

$modelo = [];
if (!is_null($model->marca_id) && !empty($model->marca_id)) {
    $modelo = ArrayHelper::map(\common\models\Modelo::find()->byMarca($model->marca_id)->ordemAlfabetica()->all(),
        'id', 'nome');
}

$ano = [];
if (!is_null($model->modelo_id) && !empty($model->modelo_id)) {
    $ano = ArrayHelper::map(\common\models\AnoModelo::find()->byModelo($model->modelo_id)->ordemAlfabetica()->all(),
        'id', 'nome');
}

?>

<?= $form->field($model, 'categoria_modelo_id', ['options' => ['class' => $formGroupClass]])->dropDownList(
    ArrayHelper::map(\common\models\CategoriaModelo::find()->ordemAlfabetica()->all(), 'id', 'nome'),
    [
        'class' => ' form-control select2 select-dep',
        'prompt' => 'Categoria',
        'select-dep-target' => '#' . Html::getInputId($model, 'marca_id'),
        'select-dep-url' => Yii::$app->urlManager->createUrl('/site/marcas-x-categoria'),
    ]

) ?>

<?php
echo Html::a('', $href, ['id' => 'categoria']);
$this->registerJs('
    $(\'#' . Html::getInputId($model, 'categoria_modelo_id') . '\').change(function(){
        $(\'#categoria\').attr(\'href\', $.urlParamChange(\'categoria_modelo_id\', $(this).val()));
        $(\'#categoria\').trigger(\'click\');
    });
');
?>

<?= $form->field($model, 'marca_id', ['options' => ['class' => $formGroupClass]])->dropDownList(
    $marca,
    [
        'class' => ' form-control select2 select-dep',
        'prompt' => 'Marca',
        'select-dep-target' => '#' . Html::getInputId($model, 'modelo_id'),
        'select-dep-url' => Yii::$app->urlManager->createUrl('/site/modelos-x-categoria-x-marca'),
        'select-dep-send-categoria' => '#' . Html::getInputId($model, 'categoria_modelo_id'),
    ]
) ?>

<?php
echo Html::a('', $href, ['id' => 'marca']);
$this->registerJs('
    $(\'#' . Html::getInputId($model, 'marca_id') . '\').change(function(){
        $(\'#marca\').attr(\'href\', $.urlParamChange(\'marca_id\', $(this).val()));
        $(\'#marca\').trigger(\'click\');
    });
');
?>

<?= $form->field(
    $model,
    'modelo_id',
    [
        'options' => [
            'class' => (strpos(
                $formGroupClass,
                'col-'
            ) !== false ? 'form-group col-md-4 col-sm-4' : $formGroupClass)
        ]
    ]
)->dropDownList(
    $modelo,
    [
        'class' => ' form-control select2 select-dep',
        'prompt' => 'Modelo',
        'select-dep-target' => '#' . Html::getInputId($model, 'ano_id'),
        'select-dep-url' => Yii::$app->urlManager->createUrl('/site/anos-x-modelo'),
    ]
) ?>

<?php
echo Html::a('', $href, ['id' => 'modelo']);
$this->registerJs('
    $(\'#' . Html::getInputId($model, 'modelo_id') . '\').change(function(){
        $(\'#modelo\').attr(\'href\', $.urlParamChange(\'modelo_id\', $(this).val()));
        $(\'#modelo\').trigger(\'click\');
    });
');
?>

<?= $form->field($model, 'ano_id', ['options' => ['class' => $formGroupClass]])->dropDownList(
    $ano,
    [
        'class' => ' form-control select2',
        'prompt' => 'Ano',
    ]
) ?>

<?php
echo Html::a('', $href, ['id' => 'ano']);
$this->registerJs('
    $(\'#' . Html::getInputId($model, 'ano_id') . '\').change(function(){
        $(\'#ano\').attr(\'href\', $.urlParamChange(\'ano_id\', $(this).val()));
        $(\'#ano\').trigger(\'click\');
    });
');
?>

<?php ActiveForm::end(); ?>