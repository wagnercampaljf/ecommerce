<?php

use common\models\ProdutoFilial;


?>


<div class="container">
    <div class="row">

        <?php //foreach($produtos as $produto){

            $produto_filial_imagem = ProdutoFilial::find()->andWhere(['=', 'id', $model->produto_filial_id])->one();
            if($produto_filial_imagem){
                echo "<div class='col-sm-1'><img src='".$produto_filial_imagem->produto->getUrlImageBackend()."' height='60' width = '60'></div>";
            }
        ?>

        <div class="col-sm-4"><?= $model->title ?></div>
        <div class="col-sm-2" style="color: rgba(145,145,145,0.56)">R$ <?= $model->full_unit_price ?></div>
        <div class="col-sm-2" style="color: rgba(145,145,145,0.56)"> <?= $model->quantity ?> u.</div>
        <div class="col-sm-2" style="color: rgba(145,145,145,0.56)"></div>

        <?php //} ?>
    </div>
</div>

