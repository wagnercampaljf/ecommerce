<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Filial;
use common\models\PedidoMercadoLivreProduto;
use common\models\ProdutoFilial;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\models\ValorProdutoFilial;

/* @var $this yii\web\View */
/* @var $model common\models\PedidoMercadoLivreProdutoProdutoFilial */
/* @var $form yii\widgets\ActiveForm */
?>
    
<?php 

    //echo "<pre>"; print_r($model); echo "</pre>";

    $pedido_mercado_livre_produto = PedidoMercadoLivreProduto::find()->andWhere(['=','id',$model->pedido_mercado_livre_produto_id])->one();
    $produto_filial_pedido = ProdutoFilial::find()->andWhere(['=','id',$pedido_mercado_livre_produto->produto_filial_id])->one();
    /*$valor_produto_filial  = ValorProdutoFilial::find()->andWhere(['=', 'produto_filial_id', $produto_filial_pedido->id])->orderBy(["dt_inicio" => SORT_DESC])->one();
    $filial = Filial::find()->andWhere(['=', 'id', $produto_filial_pedido->filial_id])->one();

    $email_pedido = "";
    if($filial){
        if($filial->email_pedido){
            $email_pedido = $filial->email_pedido;
        }
    }

    $valor_compra = 0;
    if($valor_produto_filial){
        if($valor_produto_filial->valor_compra){
            $valor_compra = $valor_produto_filial->valor_compra;
        }
    }*/

    $quantidade = 0;
    if($pedido_mercado_livre_produto->quantity){
        $quantidade = $pedido_mercado_livre_produto->quantity;
    }

    $email_pedido = "";
    $valor_compra = 0;
    //$quantidade = 0;


    /*if($produto_filial_pedido){


        $valor_produto_filial  = ValorProdutoFilial::find()->andWhere(['=', 'produto_filial_id', $produto_filial_pedido->id])->orderBy(["dt_inicio" => SORT_DESC])->one();

        if($valor_produto_filial){


            if($valor_produto_filial->valor_compra){

	            $valor_compra = $valor_produto_filial->valor_compra;

            }

        }


        $filial = Filial::find()->andWhere(['=', 'id', $produto_filial_pedido->filial_id])->one();

        if($filial){

            if($filial->email_pedido){

	            $email_pedido = $filial->email_pedido;

            }

        }
    }*/

if($produto_filial_pedido){

    $valor_produto_filial  = ValorProdutoFilial::find()->andWhere(['=', 'produto_filial_id', $produto_filial_pedido->id])->orderBy(["dt_inicio" => SORT_DESC])->one();

    if($valor_produto_filial){


        if($valor_produto_filial) {
            if($valor_produto_filial->valor_compra) {

                $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id',  $produto_filial_pedido->id])->one();

                if ($produto_filial->filial_id == 43) {

                    $valor_compra = $valor_produto_filial->valor_compra * 0.97;
                    

                } else {

                    $valor_compra = $valor_produto_filial->valor_compra;

                }
            }

            $valor_compra = number_format($valor_compra, 2, '.', '');

        }


    }

    $filial = Filial::find()->andWhere(['=', 'id', $produto_filial_pedido->filial_id])->one();
    if($filial){
        if($filial->email_pedido){
            $email_pedido = $filial->email_pedido;
        }
    }
}

    if($model->produto_filial_id){
	$email_pedido = $model->email;
	$valor_compra = $model->valor;
	$quantidade =   $model->quantidade;
    }

    //var_dump($model->produto_filial_id); die;

    echo "<div class='container'><h3>";
    if($produto_filial_pedido){
                echo "<b>Produto do pedido: </b>".$produto_filial_pedido->produto->nome.
        		"</br><b>Código Global: </b>".$produto_filial_pedido->produto->codigo_global.
        		"</br><b>Código Fabricante: </b>".$produto_filial_pedido->produto->codigo_fabricante;


        if($produto_filial_pedido->produto->marca_produto_id != null){
            ?>
            </br><b>Marca: </b> <?= $produto_filial_pedido->produto->marcaProduto->nome ?>
            <?php
        }

       
    }
    echo "</br><b>Quantidade: </b>".$pedido_mercado_livre_produto->quantity."</h3></div></br>";

?>



<div class="pedido-mercado-livre-produto-produto-filial-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= ""//$form->field($model, 'pedido_mercado_livre_produto_id')->textInput() ?>

    <?= 
        
        $produto_filial = "";
        if($model->produto_filial_id){
            $produto_filial = ProdutoFilial::findOne($model->produto_filial_id)->filial->nome ." - ".ProdutoFilial::findOne($model->produto_filial_id)->produto->nome . "(".ProdutoFilial::findOne($model->produto_filial_id)->produto->codigo_global.")"."(".ProdutoFilial::findOne($model->produto_filial_id)->produto->codigo_fabricante . ")";
        }
        elseif($pedido_mercado_livre_produto->produto_filial_id){
            $produto_filial = ProdutoFilial::findOne($pedido_mercado_livre_produto->produto_filial_id)->filial->nome ." - ".ProdutoFilial::findOne($pedido_mercado_livre_produto->produto_filial_id)->produto->nome . "(".ProdutoFilial::findOne($pedido_mercado_livre_produto->produto_filial_id)->produto->codigo_global.")"."(".ProdutoFilial::findOne($pedido_mercado_livre_produto->produto_filial_id)->produto->codigo_fabricante . ")";
            $model->produto_filial_id = $pedido_mercado_livre_produto->produto_filial_id;
        }
        else{
            $produto_filial = null;
        }
        /*$produto_filial = $model->produto_filial_id ?   ProdutoFilial::findOne($model->produto_filial_id)->filial->nome ." - ".
                                                        ProdutoFilial::findOne($model->produto_filial_id)->produto->nome . "(".
                                                        ProdutoFilial::findOne($model->produto_filial_id)->produto->codigo_global.")".
                                                        "(".ProdutoFilial::findOne($model->produto_filial_id)->produto->codigo_fabricante . ")": null;*/
        //echo "<pre>"; var_dump($produto_filial); echo "</pre>"; die;
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
            ],
            'options' => ['placeholder' => 'Selecione um Produto']
            ])->label("Produto:");
        //$form->field($model, 'produto_filial_id')->textInput() 
    ?>
    
    <?php
    $js = <<< JS
       $('#pedidomercadolivreprodutoprodutofilial-produto_filial_id').change(
           function(){
                
                var produto_filial_id = $(this).val();

                $.ajax(
                {
                    type:"GET",url:baseUrl+"/produto-filial/get-valor-compra",
                    data:{id:produto_filial_id},
                    success:function(retorno)
                    {
                               var retorno = $.parseJSON(retorno);
                               document.getElementById('pedidomercadolivreprodutoprodutofilial-valor').value = retorno.valor_compra;
                    },
                });

                $.ajax(
                {
                    type:"GET",url:baseUrl+"/produto-filial/get-email-filial",
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
    ?>

    <?= $form->field($model, 'quantidade')->textInput(['value' => $quantidade]) ?>

    <?= $form->field($model, 'valor')->textInput(['value' => $valor_compra]) ?>
    
    <?= $form->field($model, 'observacao')->textarea()->label("Adicionar observação"); ?>
    
    <?= $form->field($model, 'email')->textInput(['value' => $email_pedido])->label("E-mail"); ?>

    <div class="form-group">
        <?= ""//Html::submitButton($model->isNewRecord ? 'mercado-livre-produto-produto-filial-create' : 'Alterar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <a class="btn btn-primary" href="<?= Url::to(['/pedidos-mercado-livre/mercado-livre-produto',"id"=>$pedido_mercado_livre_produto_id]) ?>" role="button">Voltar</a>
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Alterar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
