<?php

use backend\models\PedidoProdutoFilialCotacao;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

use common\models\ProdutoFilial;
use yii\widgets\ListView;

?>
<div class="col-sm-12">
    <div class="row">

        <div class="col-sm-2">
            <?php //foreach($produtos as $produto){

            echo "<div class='col-sm-2'>";

            $produto_filial_imagem = ProdutoFilial::find()->andWhere(['=', 'id', $model->produto_filial_id])->one();
            if ($produto_filial_imagem) {
                echo "<img src='" . $produto_filial_imagem->produto->getUrlImageBackend() . "' height='60' width = '60'>";
            }

            echo "</div>";
            ?>


        </div>
        <div class="col-sm-5">
            <h6> <?=
                    Html::a('PA'.$model->produtoFilial->produto->id.' - '.$model->produtoFilial->filial->nome . ' - ' . $model->produtoFilial->produto->nome, $model->produtoFilial->produto->getUrl())
                    ?> </h6>
        </div>
        <div class="col-sm-2">
            <h6><?= Yii::$app->formatter->asCurrency($model['valor_cotacao']? $model['valor_cotacao'] : $model['valor']) ?> </h6>
        </div>
        <div class="col-sm-2">
            <h6><?= $model->quantidade ?>u. </h6>
        </div>
        <div class="col-sm-2" style="color: rgba(145,145,145,0.56)"></div>
        <?= Html::a('<button type="button" class="btn btn-primary">Valor Cotação</button>', Url::to(['/pedidos/pedido-interno-produto', 'pedido_produto_filial_id' => $model->id]), ['title' => Yii::t('yii', 'View'),]); ?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php

            $pedido_produto_filial_contacao = PedidoProdutoFilialCotacao::find()->Where(['pedido_produto_filial_id' => $model->id])->all();

            if ($pedido_produto_filial_contacao) {

                foreach ($pedido_produto_filial_contacao as $produto_filial_contacao) {

                    $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $produto_filial_contacao->produto_filial_id])->one();

                    if ($produto_filial_imagem) {
                        echo "<div class='col-md-2'><img src='" . $produto_filial_imagem->produto->getUrlImageBackend() . "' height='60' width = '60' align='center'></div>";
                    }

                    echo '<div class="col-sm-6"><h6>' . (($produto_filial) ? 'PA'.$produto_filial->produto->id.' - '.$produto_filial->filial->nome . ' - ' . $produto_filial->produto->nome : "") . '</h6></div>';
                    echo '<div class="col-sm-2" style="color: rgba(145,145,145,0.56)"><h6>R$ ' . $produto_filial_contacao->valor . '</h6></div>';
                    echo '<div class="col-sm-3" style="color: rgba(145,145,145,0.56)"><h6> ' . $produto_filial_contacao->quantidade . 'u.</h6></div>';
                }
            }
            ?>
            <br><br>
        </div>
    </div>
</div>