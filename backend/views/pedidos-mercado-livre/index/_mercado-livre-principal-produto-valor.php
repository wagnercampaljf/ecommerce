<?php

use common\models\ProdutoFilial;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\ValorProdutoFilial;

?>

<div class="container">

    <div class="row">
    	<?php


        $pedido_mercado_livre_produto_produto_filiais = PedidoMercadoLivreProdutoProdutoFilial::find()->andWhere(['=', 'pedido_mercado_livre_produto_id', $model->id])->all();

        if($pedido_mercado_livre_produto_produto_filiais){
            $valor_total = 0;
            foreach($pedido_mercado_livre_produto_produto_filiais as $pedido_mercado_livre_produto_produto_filial){

                $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $pedido_mercado_livre_produto_produto_filial->produto_filial_id])->one();

                $valor_total+= $pedido_mercado_livre_produto_produto_filial->valor * $pedido_mercado_livre_produto_produto_filial->quantidade;

            }
            echo '<tr>';
            echo '<th scope="row" style="font-size: 12px; border:none !important;">'."Valor compra". ' </p></th>';
            echo '<td style="border:none !important;"></td>';
            echo '<td style="border:none !important;"></td>';
            echo '<th scope="row" style="font-size: 12px; border:none !important;">'.Yii::$app->formatter->asCurrency($valor_total).'</td>';

            echo '</tr>';
        }else{
            $valor_total= 0;
        }

        $_SESSION['valor_total'] =  $valor_total;




        ?>

    </div>
</div>






