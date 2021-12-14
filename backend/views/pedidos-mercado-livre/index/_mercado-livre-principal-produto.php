<?php

use common\models\ProdutoFilial;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\ValorProdutoFilial;

?>


<div class="container">
    <div class="row">

        <?php //foreach($produtos as $produto){

            echo "<div class='col-sm-1'>";

            $produto_filial_imagem = ProdutoFilial::find()->andWhere(['=', 'id', $model->produto_filial_id])->one();
            if($produto_filial_imagem){
                echo "<img src='".$produto_filial_imagem->produto->getUrlImageBackend()."' height='60' width = '60'>";
            }
            
            echo "</div>";
        ?>

        <div class="col-sm-4"><?= $model->title ?></div>
        <div class="col-sm-2" style="color: rgba(145,145,145,0.56)">R$ <?= $model->full_unit_price ?>
        <?php 
            /*if($model->produto_filial_id != null){
                $produto_filial_pedido = ProdutoFilial::find()->andWhere([])->one();
                if($produto_filial_pedido){
                    $valor_produto_filial_pedido = ValorProdutoFilial::find()->andWhere(['=', 'produto_filial_id', $produto_filial_pedido->id])->orderBy(['dt_inicio' => SORT_DESC])->one();
                    if($valor_produto_filial_pedido){
                        if($valor_produto_filial_pedido->valor_compra != null){
                            echo "(".$valor_produto_filial_pedido->valor_compra.")";
                        }
                    }
                }
            }*/
        ?>
        </div>
        <div class="col-sm-2" style="color: rgba(145,145,145,0.56)"> <?= $model->quantity ?> u.</div>
        <div class="col-sm-2" style="color: rgba(145,145,145,0.56)"></div>

    </div>
    <div class="row">
    	<?php 
    
        $pedido_mercado_livre_produto_produto_filiais = PedidoMercadoLivreProdutoProdutoFilial::find()->andWhere(['=', 'pedido_mercado_livre_produto_id', $model->id])->all();
	
        if($pedido_mercado_livre_produto_produto_filiais){
            
            foreach($pedido_mercado_livre_produto_produto_filiais as $pedido_mercado_livre_produto_produto_filial){
                
                $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $pedido_mercado_livre_produto_produto_filial->produto_filial_id])->one();

                echo '<div class="row"><div class="col-sm-1" style="color: rgba(145,145,145,0.56)"></div><div class="col-sm-1">';
                
                if($produto_filial_imagem){
                    echo "<img src='".$produto_filial_imagem->produto->getUrlImageBackend()."' height='60' width = '60'>";
                }
                
                echo "</div>";
            
                echo '<div class="col-sm-3"><h6>'.(($produto_filial) ? $produto_filial->produto->nome : "").'</h6></div>';
                echo '<div class="col-sm-2" style="color: rgba(145,145,145,0.56)"><h6>R$ '.$pedido_mercado_livre_produto_produto_filial->valor .'</h6></div>';
                echo '<div class="col-sm-2" style="color: rgba(145,145,145,0.56)"><h6> '.$pedido_mercado_livre_produto_produto_filial->quantidade .'u.</h6></div>';
                echo '</div>';
                
            }
        }
    ?>
    </div>
    <div class="row">
    	<?= Html::a('<button type="button" class="btn btn-primary">Valor Cotação</button>', Url::to(['/pedidos-mercado-livre/mercado-livre-produto', 'id' => $model->id]), ['title' => Yii::t('yii', 'View'),]);?>
    </div>
</div>




