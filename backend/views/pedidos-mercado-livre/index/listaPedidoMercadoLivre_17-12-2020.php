<?php

use common\models\PedidoMercadoLivreProduto;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;
use yii\helpers\Url;
use backend\models\PedidoMercadoLivreProdutoSearch;
use common\models\PedidoMercadoLivre;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\helpers\Html;


//echo "<pre>"; print_r(Yii::$app->request->queryParams); echo "</pre>"; die;
$pedido_mercado_livre_produto  = new PedidoMercadoLivreProdutoSearch();
$dataProvider = $pedido_mercado_livre_produto ->search(['PedidoMercadoLivreProdutoSearch'=> ['pedido_mercado_livre_id' => $model->id]]);


?>


<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <article class="card card_main" style="background-color: #ffffff; border-radius: 10px">
                <div class="card__body">
                    <div class="card__content">
                        <p style="color: #b10c10; font-weight: bold;"></p>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <h2>Nº  <?= $model->pedido_meli_id?> <b><span style="color:#008000;"><?= ($model->e_pedido_autorizado) ? "(Autorizado)" : "" ?></span></b></h2>
                                </div>
                                <div class="row">
                                    <?php

                                    if ($model->shipping_status == 'delivered')
                                        echo '<p style="color: #000000; font-weight: bold;">' ."Entregue".'</p>' ;
                                    elseif ($model->shipping_status == 'shipped')
                                        echo '<p style="color: #000000; font-weight: bold;">' ."A caminho".'</p>' ;
                                    elseif ($model->shipping_status == 'cancelled')
                                        echo '<p style="color: #ff0000; font-weight: bold;">' ."Cancelado".'</p>'
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-2">

                                <button onClick="window.open('<?= Url::to(['/pedidos-mercado-livre/gerar-etiqueta', 'id' => $model['id']])?>')" type="button" class="btn btn-primary">Imprimir etiqueta</button>

                            </div>
                            <div class="col-sm-1">
                                <div><img src="https://img.icons8.com/color/64/000000/name.png"/></div>
                            </div>
                            <div class="col-sm-2">
                                <di>
                                    <p style="font-weight: bold; color: rgba(41,41,41,0.95); white-space: nowrap;overflow: hidden; text-overflow: ellipsis"><span><?= $model['buyer_first_name'] ?> <?= $model['buyer_last_name'] ?></span> </p><p style=" white-space: nowrap;overflow: hidden; text-overflow: ellipsis"><?= $model['buyer_nickname'] ?></p>

                                </di>
                            </div>

                            <div class="col-sm-1">
                                <a target="_blank" href=<?=Url::to(['/pedidos-mercado-livre/mercado-livre-view', 'id' => $model['id']])?>><span class="glyphicon glyphicon-search" ></span> </a>
                            </div>
                            <hr>
                        </div><hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <?php
                                //\yii\widgets\Pjax::begin();


                                echo  ListView::widget([

                                    'dataProvider' => $dataProvider,
                                    'itemView' => '_mercado-livre-principal-produto',
                                    'summary'=>'',

                                ]);

                                //\yii\widgets\Pjax::end();
                                ?>

                            </div>
                            <div class="col-sm-6">

                                <div class="container">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-3" style="background-color: rgba(74,78,90,0.2); border-radius: 10px">
                                        <table class="table">

                                            <!-- Valor liquido  -->
                                            <?php

                                            $pedido_mercado_livre_produtos = PedidoMercadoLivreProduto::find()->andWhere(['=', 'pedido_mercado_livre_id', $model->id])->all();


                                            $valor_total_tarifas = 0;
                                            $valor_total_produtos = 0;
                                            if($pedido_mercado_livre_produtos){
                                                foreach($pedido_mercado_livre_produtos as $pedidos_mercado_livre_produto){

                                                    $valor_total_tarifas += $pedidos_mercado_livre_produto->sale_fee * $pedidos_mercado_livre_produto->quantity;
                                                    $valor_total_produtos += $pedidos_mercado_livre_produto->full_unit_price * $pedidos_mercado_livre_produto->quantity;


                                                }
                                                $valor_total_tarifas_total = $pedidos_mercado_livre_produto->sale_fee * $pedidos_mercado_livre_produto->quantity;
                                                $valor_total_produtos_total = $pedidos_mercado_livre_produto->full_unit_price * $pedidos_mercado_livre_produto->quantity;

                                            }

                                            $pedidos_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $model->id])->all();
                                            $valor_venda = 0;

                                            foreach($pedidos_mercado_livre as $pedido_mercado_livre) {

                                                $valor_venda += $valor_total_produtos_total + $pedido_mercado_livre->shipping_option_cost - $valor_total_tarifas_total - $pedido_mercado_livre->shipping_option_list_cost;

                                                /*                                                    echo 'Valor total: '. $valor_total_produtos_total;
                                                echo '<br>';
                                                echo 'shipping_option_cost: '.$pedido_mercado_livre->shipping_option_cost;
                                                echo '<br>';
                                                echo 'sale_fee: '. $pedidos_mercado_livre_produto->sale_fee;
                                                echo '<br>';
                                                echo 'Valor total das tarifcas: '.$valor_total_tarifas_total;
                                                echo '<br>';
                                                echo 'shipping_option_list_cost: '. $pedido_mercado_livre->shipping_option_list_cost;
                                                echo '<br>';
                                                echo '<br>';

                                                echo 'full_unit_price: '. $pedidos_mercado_livre_produto->full_unit_price;
                                                echo '<br>';
                                                echo 'quantity: '.$pedidos_mercado_livre_produto->quantity;
                                                echo '<br>';*/


                                            }
                                              echo '<tr>';
                                              echo '<th scope="row" style="font-size: 12px; border:none !important;">'. "Valor venda".'</p></th>';
                                               echo'<td style="border:none !important;"></td>';
                                                echo'<td style="border:none !important;"></td>';
                                               echo'<th scope="row" style="font-size: 12px; border:none !important;">'. Yii::$app->formatter->asCurrency($pedidos_mercado_livre_produto->full_unit_price * $pedidos_mercado_livre_produto->quantity).'</th>';
                                            echo'</tr>';




                                            echo '<tr>';
                                            echo '<th scope="row" style="font-size: 12px; border:none !important;">' ."Valor liquido".'</p></th>';
                                            echo '<td style="border:none !important;"></td>';
                                            echo'<td style="border:none !important;"></td>';
                                            echo '<th scope="row" style="font-size: 12px; border:none !important;">'.  Yii::$app->formatter->asCurrency($valor_venda).'</td>';
                                            echo' </tr>';


                                            ?>




                                            <!-- Valor Compra -->
                                            <?php

                                            echo  ListView::widget([

                                                'dataProvider' => $dataProvider,
                                                'itemView' => '../index/_mercado-livre-principal-produto-valor',
                                                'summary'=>'',

                                            ]);

                                            $valor_cotacao=$_SESSION['valor_total'];

                                            $valor_liquido= $valor_venda - $valor_cotacao

                                            ?>


                                            <!-- margem -->
                                            <tr>

                                                <!-- CALCULO DA MARGEM --->
                                                <?php


                                                $valor_cotacao=$_SESSION['valor_total'];

                                                if ($valor_cotacao == 0){
                                                    $margem = "Sem valor de cotação";

                                                    echo '<th scope="row" style="color: #1b6d85; font-size: 12px;">' .$margem.' </td>';

                                                } else{
                                                    $margem = round((($valor_venda/$valor_cotacao)-1)*100,2)."%";

                                                    if ($margem < 0){
                                                        echo '<th scope="row" style="color: red; font-size: 12px;">'."Margem". '</p></th>';
                                                        echo '<td ></td>';
                                                        echo '<td ></td>';

                                                        echo ' <th scope="row" style="color: red;  font-size: 12px;">'.$margem.' </td>';

                                                    }else{

                                                        echo '<th scope="row" style="font-size: 12px; ">'."Margem". '</p></th>';
                                                        echo '<td ></td>';
                                                        echo '<td ></td>';

                                                        echo ' <th scope="row" style=" font-size: 12px; ">'.$margem.' </td>';

                                                    }

                                                }

                                                ?>

                                            </tr>


                                        </table>

                                    </div>

                                </div>

                            </div>
                        </div><br>
                    </div>

                </div>
                <div class="card__content" style="background-color:rgba(0,0,0,.03); border-radius: 0px 0px 10px 10px; padding: 10px 55px">
                    <div class="col-sm-12">
                        <?= $model['comentario'] ?>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>






<style>
    /*
     * Core for cards
     */

    .cards{
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        font-family: "Roboto", sans-serif;
    }

    .card{
        box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14),0 1px 5px 0 rgba(0,0,0,0.12),0 3px 1px -2px rgba(0,0,0,0.2);
        margin-bottom: 2rem;

        display: flex;
        flex-direction: column;
    }

    .card_main{
        width: 100%;
    }

    @media screen and (min-width: 801px){

        .card_main{

        .card__title{
            font-size: 180%;
        }

        .card__main-action{
            width: @card_main_action_size * 1.12;
            height: @card_main_action_size * 1.12;
        }
    }
    }

    .card_size-2xl{
        width: 66%;
    }


    }

    .card_size-xl{
        width: 49%;
    }

    @media screen and (min-width: 801px){

        .card_size-xl{

        .card__title{
            font-size: 160%;
        }
    }
    }

    .card_size-m{
        width: 32%;
    }

    @media screen and (min-width: 481px) and (max-width: 800px){

        .card_size-m, .card_size-2xl{
            width: 49%;
        }
    }

    @media screen and (max-width: 480px){

        .card_size-m, .card_size-xl, .card_size-2xl{
            width: 100%;
        }
    }

    .card__header{
        position: relative;
        line-height: 0;
    }

    *::-ms-backdrop,.card__header{
        display: flex;
    }

    .card__preview{
        max-width: 100%;
        height: auto;
    }

    *::-ms-backdrop,.card__preview{
        flex: 0 0 auto;
    }

    @card_main_action_size: 4em;

    .card__main-action{

        font-size: 100%;
        text-decoration: none;
        text-indent: -9999px;
        cursor: pointer;

        border: none;
        border-radius: 50%;
        padding: 0;
        box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14),0 1px 5px 0 rgba(0,0,0,0.12),0 3px 1px -2px rgba(0,0,0,0.2);

        position: absolute;
        right: 5%;
        bottom: 0;
        transform: translateY(50%);

        width: @card_main_action_size;
        height: @card_main_action_size;

    &:before{

         content: "";
         display: block;

         width: 60%;
         height: 60%;

         box-sizing: border-box;

         background-position: 50% 50%;
         background-repeat: no-repeat;
         background-size: contain;

         position: absolute;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%)
     }

    &:focus{
         outline: none;
     }
    }

    .card__body {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        flex-grow: 2;
    }

    .card__title{

        font-size: 140%;
        font-weight: 400;
        line-height: 1.5;

        margin-top: 0;
        margin-bottom: .8em;
    }

    .card__showmore{
        text-decoration: none;
    }

    .card__content{
        padding: 2.5em 4% 1.5em;
        flex-grow: 2;
    }

    .card__footer{

        padding: 1.5em 4%;
        border-top-width: 1px;
        border-top-style: solid;
        font-size: 90%;

        display: flex;
        justify-content: space-between;
    }

    .card__meta-item{
        display: inline-block;
        vertical-align: middle;
        margin-left: .8em;
    }

    .card__meta-icon{

        display: inline-block;
        vertical-align: middle;
        text-align: right;

        width: 1.5em;
        height: 1.5em;
        margin-right: .2em;

        background-position: 50% 50%;
        background-repeat: no-repeat;
        background-size: contain;
    }

    /*
     * Skin for cards
     */

    @main_color: #3F51B5;
    @light_color: #C5CAE9;
    @dark_color: #303F9F;
    @optional_color: #BDBDBD;
    @optional_color2: #448AFF;
    @color_text: #212121;

    .card{
        background-color: #fff;
        color: @color_text;
        font-size: 1.4rem;
    }



    .card__main-action{
        background-color: @main_color;

    &:before{
         background-image: url("https://stas-melnikov.ru/cssgrid/bookmark.svg");
     }

    &:hover, &:focus{
                  background-color: @dark_color;
              }
    }

    .card__footer{
        border-top-color: @optional_color;
    }

    .card__showmore{

        color: @dark_color;
        transition: color .3s ease-out;

    &:hover, &:focus{
                  color: @main_color;
              }
    }

    .card__meta-comments{
        background-image: url("https://stas-melnikov.ru/cssgrid/comment.svg");
    }

    .card__meta-likes{
        background-image: url("https://stas-melnikov.ru/cssgrid/favorite.svg");
    }
</style>