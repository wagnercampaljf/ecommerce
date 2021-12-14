<?php

use backend\models\PedidoProdutoFilialCotacao;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Filial;
use common\models\PedidoProdutoFilial;
use common\models\ProdutoFilial;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\models\ValorProdutoFilial;
use yii\web\View;

?>

<?php

$formatJs = <<< 'JS'
var formatRepo = function (repo) {
    if (repo.loading) {
        return repo.test;
    }
    var markup =
'<div class="row">' + 
    '<div class="col-sm-4">' +
        '<b style="margin-left:5px">' + repo.nome + '</b>' + 

    '</div>' +
    '<div class="col-sm-2"> ' + repo.codigo_global + '</div>' +
    '<div class="col-sm-2">' + repo.filial + '</div>' +
    '<div class="col-sm-1"> ' + repo.quantidade + '</div>' +
    '<div class="col-sm-1">' + repo.codigo_fabricante + '</div>' +
    '<div class="col-sm-1">' + repo.valor + '</div>' +
    '<div class="col-sm-1">' + repo.compra + '</div>' +
'</div>';
    
    return '<div>' + markup + '</div>';
};
JS;

// Register the formatting script
$this->registerJs($formatJs, View::POS_HEAD);

// script to parse the results into the format expected by Select2
$resultsJs = <<< JS

JS;


?>

<div class="pedido-produto-interno-form">

    <?php
    $url = '';
    $id = '';

    if ($model->id) {
        $url = "update-cotacao";
        $id = $model->id;
    } else {
        $url = "pedido-produto-filial-cotacao-create";
        $id = $model->pedido_produto_filial_id;
    }
    $form = ActiveForm::begin(['action' => Url::to(["/pedidos/$url", 'id' => $id])]); ?>
    <?=

    $produto_filial = $model->produto_filial_id ? ProdutoFilial::findOne($model->produto_filial_id)->filial->nome . " - " . ProdutoFilial::findOne($model->produto_filial_id)->produto->nome . "(" . ProdutoFilial::findOne($model->produto_filial_id)->produto->codigo_global . ")" . "(" . ProdutoFilial::findOne($model->produto_filial_id)->produto->codigo_fabricante . ")" : null;
    echo  $form->field($model, 'produto_filial_id')->widget(Select2::className(), [
        'initValueText' => $produto_filial,
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'ajax' => [
                'url' => Url::to(['valor-produto-filial/get-produto-filial']),
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('formatRepo'),
            'templateSelection' => new JsExpression('formatRepoSelection'),
        ],
        'options' => ['placeholder' => 'Selecione um Produto']
    ])->label("Produto:");
    ?>

    <?php
    $js = <<< JS
       $('#pedidoprodutofilialcotacao-produto_filial_id').change(
           function(){
                
                var produto_filial_id = $(this).val();

                $.ajax(
                {
                    type:"GET",url:baseUrl+"/produto-filial/get-valor-compra",
                    data:{id:produto_filial_id},
                    success:function(retorno)
                    {
                               var retorno = $.parseJSON(retorno);
                               document.getElementById('pedidoprodutofilialcotacao-valor').value = retorno.valor_compra;
                    },
                });

                $.ajax(
                {
                    type:"GET",url:baseUrl+"/produto-filial/get-email-filial",
                    data:{id:produto_filial_id},
                    success:function(retorno)
                    {
                        var retorno = $.parseJSON(retorno);
                        document.getElementById('pedidoprodutofilialcotacao-email').value = retorno.email;
                    },
                })
           }
       );
JS;

    $this->registerJs($js);
    ?>
    <?php

    $formatJs = <<< 'JS'
var formatRepoSelection = function (repo) {
    return repo.text ;
}
JS;

    // Register the formatting script
    $this->registerJs($formatJs, View::POS_HEAD);

    // script to parse the results into the format expected by Select2
    $resultsJs = <<< JS

JS;

    ?>

    <?= $form->field($model, 'quantidade')->textInput() ?>

    <?= $form->field($model, 'valor')->textInput() ?>

    <?= $form->field($model, 'observacao')->textarea()->label("Adicionar observação"); ?>

    <?= $form->field($model, 'email')->textInput()->label("E-mail"); ?>

    <?= $form->field($model, 'e_atualizar_site')->checkbox() ?>

    <div class="form-group">
        <a class="btn btn-primary" href="<?= Url::to(['/pedidos/pedido-interno-produto', "pedido_produto_filial_id" => $model->pedido_produto_filial_id]) ?>" role="button">Voltar</a>
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Alterar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>