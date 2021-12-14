<?php
//3333
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Filial;
use common\models\PedidoMercadoLivreProduto;
use common\models\ProdutoFilial;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\models\ValorProdutoFilial;
use yii\web\View;
use backend\controllers\ProdutoController;

?>

<?php


$pedido_mercado_livre_produto = PedidoMercadoLivreProduto::find()->andWhere(['=', 'id', $model->pedido_mercado_livre_produto_id])->one();
$produto_filial_pedido = ProdutoFilial::find()->andWhere(['=', 'id', $pedido_mercado_livre_produto->produto_filial_id])->one();

$quantidade = 0;
if ($pedido_mercado_livre_produto->quantity) {
    $quantidade = $pedido_mercado_livre_produto->quantity;
}

$email_pedido = "";
$valor_compra = 0;

if ($produto_filial_pedido) {

    $valor_produto_filial  = ValorProdutoFilial::find()->andWhere(['=', 'produto_filial_id', $produto_filial_pedido->id])->orderBy(["dt_inicio" => SORT_DESC])->one();

    if ($valor_produto_filial) {


        if ($valor_produto_filial) {
            if ($valor_produto_filial->valor_compra) {

                $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id',  $produto_filial_pedido->id])->one();

                if ($produto_filial_pedido->filial_id == 43) {

                    $valor_compra = $valor_produto_filial->valor_compra * 0.97;
                } else {

                    $valor_compra = $valor_produto_filial->valor_compra;
                }
            }

	    $ipi = 0;
	    if(!is_null($produto_filial_pedido->produto->ipi)){
		$ipi = ($produto_filial_pedido->produto->ipi/100)*$valor_compra;
	    }

	    $valor_compra = number_format(($valor_compra+$ipi), 2, '.', '');

        }
    }

    $filial = Filial::find()->andWhere(['=', 'id', $produto_filial_pedido->filial_id])->one();
    if ($filial) {
        if ($filial->email_pedido) {
            $email_pedido = $filial->email_pedido;
        }
    }
}

if ($model->produto_filial_id) {
    $email_pedido = $model->email;
    $valor_compra = $model->valor;
    $quantidade =   $model->quantidade;
}


echo "<div class='container'><h3>";
if ($produto_filial_pedido) {
    echo "<b>Produto do pedido: </b>" . $produto_filial_pedido->produto->nome .
        "</br><b>Código Global: </b>" . $produto_filial_pedido->produto->codigo_global .
        "</br><b>Código Fabricante: </b>" . $produto_filial_pedido->produto->codigo_fabricante;


    if ($produto_filial_pedido->produto->marca_produto_id != null) {
?>
        </br><b>Marca: </b> <?= $produto_filial_pedido->produto->marcaProduto->nome ?>
<?php
    }
}
echo "</br><b>Quantidade: </b>" . $pedido_mercado_livre_produto->quantity . "</h3></div></br>";






  ////////////////////////////////////////////////////
    //TESTE GRADE JS
    ////////////////////////////////////////////////////

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



<div class="pedido-mercado-livre-produto-produto-filial-form">

    <?php $form = ActiveForm::begin(); ?>


    <?=

    $produto_filial = "";
    if ($model->produto_filial_id) {
        $produto_filial = ProdutoFilial::findOne($model->produto_filial_id)->filial->nome . " - " . ProdutoFilial::findOne($model->produto_filial_id)->produto->nome . "(" . ProdutoFilial::findOne($model->produto_filial_id)->produto->codigo_global . ")" . "(" . ProdutoFilial::findOne($model->produto_filial_id)->produto->codigo_fabricante . ")";
    } 
    elseif ($pedido_mercado_livre_produto->produto_filial_id) {
        //$produto_filial = ProdutoFilial::findOne($pedido_mercado_livre_produto->produto_filial_id)->filial->nome . " - " . ProdutoFilial::findOne($pedido_mercado_livre_produto->produto_filial_id)->produto->nome . "(" . ProdutoFilial::findOne($pedido_mercado_livre_produto->produto_filial_id)->produto->codigo_global . ")" . "(" . ProdutoFilial::findOne($pedido_mercado_livre_produto->produto_filial_id)->produto->codigo_fabricante . ")";
        $produto_filial_tabela  = ProdutoFilial::findOne($pedido_mercado_livre_produto->produto_filial_id);
        if($produto_filial_tabela->filial_id == 98){
            $produto_filial_origem = ProdutoFilial::findOne($produto_filial_tabela->produto_filial_origem_id);
            if($produto_filial_origem){
                $produto_filial_tabela = $produto_filial_origem;
            }
        }
        
        $produto_filial_fisica  = ProdutoFilial::find() ->andWhere(['=', 'produto_id', $produto_filial_tabela->produto_id])
                                                        ->andWhere(['=', 'filial_id', 96])
                                                        ->andWhere(['>', 'quantidade', 0])
                                                        ->one();
        if($produto_filial_fisica){
            $produto_filial         = $produto_filial_fisica->filial->nome . " - " . $produto_filial_fisica->produto->nome . "(" . $produto_filial_fisica->produto->codigo_global . ")" . "(" . $produto_filial_fisica->produto->codigo_fabricante . ")";        

            $produto_filial_tabela = $produto_filial_fisica;
            
            $valor_produto_filial_fisica  = ValorProdutoFilial::find()->andWhere(['=', 'produto_filial_id', $produto_filial_fisica->id])->orderBy(["dt_inicio" => SORT_DESC])->one();
            if ($valor_produto_filial_fisica) {
                $valor_compra = number_format($valor_produto_filial_fisica->valor_compra, 2, '.', '');
            }
        }
        else{
            $produto_filial         = $produto_filial_tabela->filial->nome . " - " . $produto_filial_tabela->produto->nome . "(" . $produto_filial_tabela->produto->codigo_global . ")" . "(" . $produto_filial_tabela->produto->codigo_fabricante . ")";
    	    //echo "<pre>";var_dump($produto_filial);echo "</pre>"; die;
    	    //if($produto_filial_tabela->filial_id == 43){
            //    $produto_filial = null;
    	    //}
        }

        //$model->produto_filial_id = $pedido_mercado_livre_produto->produto_filial_id;
        $model->produto_filial_id = $produto_filial_tabela->id;
        
        $filial = Filial::find()->andWhere(['=', 'id', $produto_filial_tabela->filial_id])->one();
        if ($filial) {
            if ($filial->email_pedido) {
                $email_pedido = $filial->email_pedido;
            }
        }
    } else {
        $produto_filial = null;
    }
    echo  $form->field($model, 'produto_filial_id')->widget(Select2::className(), [
        'initValueText' => $produto_filial,
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'ajax' => [
                'url' => Url::to(['valor-produto-filial/get-produto-filial']),
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
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
       $('#pedidomercadolivreprodutoprodutofilial-produto_filial_id').change(
           function(){
                
                var produto_filial_id = $(this).val();

                $.ajax(
                {
                    type:"GET",
                    url:baseUrl+"/produto-filial/get-valor-compra",
                    data:{id:produto_filial_id},
                    success:function(retorno)
                    {
                               var retorno = $.parseJSON(retorno);
                               document.getElementById('pedidomercadolivreprodutoprodutofilial-valor').value = retorno.valor_compra;
                    },
                });

                $.ajax(
                {
                    type:"GET",
                    url:baseUrl+"/produto-filial/get-email-filial",
                    data:{id:produto_filial_id},
                    success:function(retorno)
                    {
                                var retorno = $.parseJSON(retorno);
                                document.getElementById('pedidomercadolivreprodutoprodutofilial-email').value = retorno.email;
                    },
                })
                

           }
       );

JS;



    $this->registerJs($js);



     ////////////////////////////////////////////////////
    //TESTE GRADE JS
    ////////////////////////////////////////////////////

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

    <?= $form->field($model, 'quantidade')->textInput(['value' => $quantidade]) ?>

    <?= $form->field($model, 'valor')->textInput(['value' => $valor_compra]) ?>

    <?= $form->field($model, 'observacao')->textarea()->label("Adicionar observação"); ?>

    <?= $form->field($model, 'email')->textInput(['value' => $email_pedido])->label("E-mail"); ?>

    <?= $form->field($model, 'e_atualizar_site')->checkbox() ?>

    <div class="form-group">
        <a class="btn btn-primary" href="<?= Url::to(['/pedidos-mercado-livre/mercado-livre-produto', "id" => $pedido_mercado_livre_produto_id]) ?>" role="button">Voltar</a>
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Alterar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>





    <?php ActiveForm::end(); ?>



</div>
