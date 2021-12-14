<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

use common\models\ProdutoFilial;
use yii\widgets\ListView;

?>


<div class="col-sm-12">


    <div class="row">
        <div class="row">

            <div class="col-sm-2">
                <?php //foreach($produtos as $produto){

                echo "<div class='col-sm-2'>";

                $produto_filial_imagem = ProdutoFilial::find()->andWhere(['=', 'id', $model->produto_filial_id])->one();
                if($produto_filial_imagem){
                    echo "<img src='".$produto_filial_imagem->produto->getUrlImageBackend()."' height='60' width = '60'>";
                }

                echo "</div>";
                ?>


            </div>
            <div class="col-sm-5"><h6> <?=


                    Html::a($model->produtoFilial->produto->nome, $model->produtoFilial->produto->getUrl())

                    ?> </h6></div>
            <div class="col-sm-2"><h6><?= Yii::$app->formatter->asCurrency($model['valor'])?> </h6></div>
            <div class="col-sm-2"><h6><?=  $model->quantidade ?>u. </h6></div>
            <div class="col-sm-2"><h6>Total: <?= Yii::$app->formatter->asCurrency($model['valor'] * $model['quantidade']) ?> </h6></div>

        </div>




    </div>
    <div class="row">

    </div>
</div>
