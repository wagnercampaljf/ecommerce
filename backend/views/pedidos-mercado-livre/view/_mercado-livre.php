<?php

/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 13/09/17
 * Time: 18:32
 */

use backend\models\PedidoMercadoLivreProdutoSearch;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreProduto;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;
use common\models\PedidoMercadoLivreShipments;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;

$pedido_mercado_livre_produto  = new PedidoMercadoLivreProdutoSearch();
$dataProvider = $pedido_mercado_livre_produto->search(['PedidoMercadoLivreProdutoSearch' => ['pedido_mercado_livre_id' => $model->id]]);
$pedido_shipments = PedidoMercadoLivreShipments::findOne(['order_id' => $model['pedido_meli_id']]);

$this->params['active'] = 'pedidos';

$this->title = 'Pedido #' . $model['pedido_meli_id'];

?>

<h2></h2>

<?php $form = ActiveForm::begin(['action' => Url::to(['/pedidos-mercado-livre/mercado-livre-update', 'id' => $model['id']])]); ?>

<div class="container">

    <div class="row">
        <div class="col-sm-12">
            <div class="col-sm-9">

                <?php foreach ($produtos as $produto) { ?>
                    <h2> <?= $produto['title'] ?></h2>
                <?php } ?>
                <p>Venda <?= '#' . $model['pedido_meli_id'] ?> | <?= $model['date_created'] ?></p><br>
            </div>
            <div class="col-sm-3">
                <?php
                echo Html::a('Editar Endereço', ['pedidos-mercado-livre-update', 'id' => $model['id']], ['class' => 'btn btn-primary']);
                ?>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="cards">
                <article class="card card_main" style="background-color: #f5f5f5">

                    <div class="card__body">
                        <div class="card__content">
                            <div class="card__description">
                                <?php
                                if ($model->filial_id) {
                                    switch ($model->filial_id) {
                                        case 93:
                                ?>
                                            <h3><span style="font-weight: bold; color: rgba(41,41,41,0.95)">Faturamento MG4</span></h3>
                                        <?php
                                            break;
                                        case 94:
                                        ?>
                                            <h3><span style="font-weight: bold; color: rgba(41,41,41,0.95)">Faturamento MG1</span></h3>
                                        <?php
                                            break;
                                        case 95:
                                        ?>
                                            <h3><span style="font-weight: bold; color: rgba(41,41,41,0.95)">Faturamento SP3</span></h3>
                                        <?php
                                            break;
                                        case 96:
                                        ?>
                                            <h3><span style="font-weight: bold; color: rgba(41,41,41,0.95)">Faturamento SP2</span></h3>
                                    <?php
                                            break;
                                    }
                                    ?>
                                <?php
                                } ?>

                                <p><span style="font-weight: bold; color: rgba(41,41,41,0.95)"><?= $model['buyer_first_name'] ?> <?= $model['buyer_last_name'] ?></span> |
                                    <?= $model['buyer_nickname'] ?> | <?= $model['buyer_doc_type'] ?> <?= $model['buyer_doc_number'] ?> | Tel <?= $model['receiver_phone'] ?></p>
                            </div>
                        </div>

                    </div>
                </article>


                <article class="card card_main">
                    <div class="card__body">
                        <div class="card__content">
                            <div class="card__description">

                                <?php


                                echo  ListView::widget([

                                    'dataProvider' => $dataProvider,
                                    'itemView' => '../index/_mercado-livre-principal-produto',
                                    'summary' => '',

                                ]);

                                ?>
                                <?php
                                /*foreach($produtos as $produto){ 
                                        echo Html::a('<button type="button" class="btn btn-primary">Valor Cotação</button>', Url::to(['/pedidos-mercado-livre/mercado-livre-produto', 'id' => $produto['id']]), ['title' => Yii::t('yii', 'View'),]);
                                	}*/
                                ?>
                                <br><br><br>


                                <p style="font-weight: bold; color: rgba(41,41,41,0.95)"> Dados do envio</p>
                                <p style="font-size: 14px">Envio: <?= $model['shipping_option_name'] ?> </p>
                                <p style="font-size: 14px"><?= $model['receiver_street_name'] ?>, <?= $model['receiver_street_number'] ?> </p>
                                <p style="font-size: 14px">CEP <?= $model['receiver_zip_code'] ?> - <?= $model['receiver_city_name'] ?> - <?= $model['receiver_state_name'] ?> </p>
                                <p style="font-size: 14px">Quem recebe: <?= $model['receiver_name'] ?> - Tel <?= $model['receiver_phone'] ?> </p>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="card card_main">

                    <div class="card__body">
                        <div class="card__content">
                            <div class="card__description">
                                <p style="font-weight: bold; color: rgba(41,41,41,0.95)"> Dados para sua fatura</p>
                                <p style="font-size: 14px"><?= $model['buyer_first_name'] ?> <?= $model['buyer_last_name'] ?> - <?= $model['buyer_doc_type'] ?> <?= $model['buyer_doc_number'] ?></p>
                                <p style="font-size: 14px"><?= $model['receiver_street_name'] ?>, <?= $model['receiver_street_number'] ?> - <?= $model['receiver_city_name'] ?> - CEP <?= $model['receiver_zip_code'] ?> - <?= $model['receiver_state_name'] ?> </p>


                            </div>
                        </div>

                    </div>


                </article>


            </div>
        </div>
        <div class="col-sm-5">
            <div class="cards">
                <article class="card card_main" style="background-color: #f5f5f5">
                    <div class="card__body">
                        <div class="card__content">
                            <div>
                                <p style="font-weight: bold; color: rgba(41,41,41,0.95)"> Recebimento <?php if ($pagamento->status  == 'approved')
                                                                                                            echo "Aprovado";
                                                                                                        elseif ($pagamento->status  == 'cancelled')
                                                                                                            echo "Cancelado";
                                                                                                        elseif ($pagamento->status == 'refunded')
                                                                                                            echo "Cancelado"; ?> </p>



                                <p style="font-size: 14px"><?= '#' . $pagamento['pagamento_meli_id'] ?> | <?= $pagamento['date_created'] ?></p><br>
                                <table class="table">

                                    <?php
                                    $valor_total_produtos = 0;
                                    $valor_total_cotacao = 0;

                                    $e_valor_cotacao_preenchido = true;


                                    foreach ($produtos as $produto) {
                                        $valor_total_produtos += $produto->full_unit_price * $produto->quantity;
                                        $valor_total_cotacao += $produto->valor_cotacao * $produto->quantity;

                                        if ($produto->valor_cotacao == 0 || is_null($produto->valor_cotacao)) {
                                            $e_valor_cotacao_preenchido = false;
                                        }


                                    ?>
                                        <tr>
                                            <th scope="row" style="font-size: 14px; border:none !important;"><?= $produto->title ?></p>
                                            </th>
                                            <td style="border:none !important;"></td>
                                            <td style="border:none !important;"></td>
                                            <td style="border:none !important;"><?= Yii::$app->formatter->asCurrency($produto->full_unit_price * $produto->quantity) ?></td>
                                        </tr>
                                    <?php } ?>

                                    <tr>
                                        <th scope="row" style="font-size: 14px; border:none !important;">Envio pelo Mercado Envios</p>
                                        </th>
                                        <td style="border:none !important;"></td>
                                        <td style="border:none !important;"></td>
                                        <td style="border:none !important;"><?= Yii::$app->formatter->asCurrency($model->shipping_option_cost) ?></td>
                                    </tr>


                                    <?php
                                    $valor_total_tarifas = 0;

                                    foreach ($produtos as $produto) {
                                        $valor_total_tarifas += $produto->sale_fee * $produto->quantity
                                    ?>
                                        <tr>
                                            <th scope="row" style="font-size: 14px; overflow: hidden; max-width: 200px; text-overflow: ellipsis">Tarifa de <?= $produto['title'] ?></th>
                                            <td></td>
                                            <td></td>
                                            <td><?= '-' . Yii::$app->formatter->asCurrency($produto->sale_fee * $produto->quantity) ?></td>

                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <th scope="row" style="font-size: 14px; border:none !important;">Envio pelo Mercado Envios</p>
                                        </th>
                                        <td style="border:none !important;"></td>
                                        <td style="border:none !important;"></td>
                                        <td style="border:none !important;"><?= '-' . Yii::$app->formatter->asCurrency($model->shipping_option_list_cost) ?></td>
                                    </tr>

                                    <?php

                                    $valor_venda = $valor_total_produtos + $model->shipping_option_cost - $valor_total_tarifas - $model->shipping_option_list_cost;


                                    $_SESSION['valor_venda'] =  $valor_venda;


                                    ?>

                                    <!-- valor total -->
                                    <tr>
                                        <th scope="row" style="font-weight: bold; color: rgba(41,41,41,0.95)">Valor Liquido</p>
                                        </th>
                                        <td></td>
                                        <td></td>
                                        <td style="font-weight: bold; color: rgba(41,41,41,0.95)"><?= Yii::$app->formatter->asCurrency($valor_venda) ?></td>
                                    </tr>

                                    <!-- valor venda -->

                                    <?php
                                    $valor_total_produtos = 0;
                                    $valor_total_cotacao = 0;

                                    $e_valor_cotacao_preenchido = true;


                                    foreach ($produtos as $produto) {
                                        $valor_total_produtos += $produto->full_unit_price * $produto->quantity;
                                        $valor_total_cotacao += $produto->valor_cotacao * $produto->quantity;

                                        if ($produto->valor_cotacao == 0 || is_null($produto->valor_cotacao)) {
                                            $e_valor_cotacao_preenchido = false;
                                        }


                                    ?>
                                        <tr>
                                            <th scope="row" style="font-size: 14px;">Valor venda</p>
                                            </th>
                                            <td></td>
                                            <td></td>
                                            <th scope="row" style="font-size: 14px;"><?= Yii::$app->formatter->asCurrency($produto->full_unit_price * $produto->quantity) ?></th>
                                        </tr>
                                    <?php } ?>

                                    <!-- preço compra -->
                                    <tr>

                                        <?php

                                        echo  ListView::widget([

                                            'dataProvider' => $dataProvider,
                                            'itemView' => '../index/_mercado-livre-principal-produto-valor',
                                            'summary' => '',
                                        ]);

                                        ?>

                                        <!-- CALCULO DO VALOR LIQUIDO --->

                                        <?php

                                        $valor_cotacao = $_SESSION['valor_total'];

                                        $valor_liquido = $valor_venda - $valor_cotacao



                                        ?>
                                    </tr>


                                    <!-- margem -->
                                    <tr>
                                        <th scope="row" style="font-weight: bold;  color: rgba(41,41,41,0.95)">Margem</p>
                                        </th>
                                        <td></td>
                                        <td></td>

                                        <!-- CALCULO DA MARGEM --->
                                        <?php

                                        $pedidoMercadoLivreProduto = PedidoMercadoLivreProduto::findAll(['pedido_mercado_livre_id' => $model['id']]);
                                        $totalCotacao = 0;
                                        $tarifa = 0;
                                        $frete = $model['shipping_option_list_cost'] - $model['shipping_option_cost'];
                                        $margem = 0;

                                        foreach ($pedidoMercadoLivreProduto as $produto) {
                                            $pedidoMercadoLivreProdutoProdutoFilial = PedidoMercadoLivreProdutoProdutoFilial::findAll(['pedido_mercado_livre_produto_id' => $produto->id]);
                                            $tarifa = $produto->sale_fee * $produto->quantity;
                                            if ($pedidoMercadoLivreProdutoProdutoFilial) {
                                                foreach ($pedidoMercadoLivreProdutoProdutoFilial as $cotacao) {
                                                    $totalCotacao += $cotacao->valor * $cotacao->quantidade;
                                                }
                                            }
                                        }
                                        if ($totalCotacao > 0) {
                                            $margem = round(($model['total_amount'] - $tarifa - $frete) / $totalCotacao, 2);
                                        }

                                        echo ' <td style="font-weight: bold; color: rgba(41,41,41,0.95)">' . $margem . ' </td>';

                                        ?>
                                    </tr>

                                </table>
                            </div>
                        </div>

                    </div>
                </article>

                <article class="card card_main">

                    <div class="card__body">
                        <div class="card__content">
                            <div class="card__description">
                                <p style="font-weight: bold; color: rgba(41,41,41,0.95)"> Observação</p>
                                <p style="font-size: 14px"><?= $model['observacao_envio'] ?> </p>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
        <article class="card card_main" style="background-color: #f5f5f5">
            <div class="card__body">
                <div class="card__content">
                    <style>
                        textarea.form-control {
                            height: 250px;
                        }
                    </style>

                    <?= $form->field($model, 'porcentagem_pedido')->widget(Select2::class, [
                        'data' => [100 => '100%', 40 => '40%', 20 => '20%', 10 => '10%'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'options' => ['placeholder' => 'Selecione a porcentagem']
                    ])->label("Porcentagem Pedido:"); ?>

                    <?= $form->field($model, 'email_assunto') ?>

                    <?= $form->field($model, 'email_texto')->textarea() //->widget(Redactor::className()) 
                    ?>

                    <?= $form->field($model, 'email_enderecos') ?>

                    <?= $form->field($model, 'comentario')->label("Adicionar comentários"); ?>




                </div>

            </div>
        </article>
    </div>
</div>

<a class="btn btn-primary" href="<?= Url::to(['/pedidos']) ?>" role="button">Voltar</a>

<?= Html::submitButton('Salvar', ['class' => 'btn btn-primary']) ?>

<a class="btn btn-primary" href="<?= Url::to(['/pedidos-mercado-livre/mercado-livre-autorizar', 'id' => $model['id']]) ?>" role="button">Autorizar Pedido</a>

<a class="btn btn-primary" href="<?= Url::to(['/pedidos-mercado-livre/mercado-livre-desautorizar', 'id' => $model['id']]) ?>" role="button">Desautorizar</a>

<?php ActiveForm::end(); ?>


<style>
    /*
     * Core for cards
     */

    .cards {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        font-family: "Roboto", sans-serif;
    }

    .card {
        box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2);
        margin-bottom: 2rem;

        display: flex;
        flex-direction: column;
    }

    .card_main {
        width: 100%;
    }

    @media screen and (min-width: 801px) {

        .card_main {

            .card__title {
                font-size: 180%;
            }

            .card__main-action {
                width: @card_main_action_size * 1.12;
                height: @card_main_action_size * 1.12;
            }
        }
    }

    .card_size-2xl {
        width: 66%;
    }

    @media screen and (min-width: 801px) {

        .card_size-2xl {

            .card__title {
                font-size: 170%;
            }
        }
    }

    .card_size-xl {
        width: 49%;
    }

    @media screen and (min-width: 801px) {

        .card_size-xl {

            .card__title {
                font-size: 160%;
            }
        }
    }

    .card_size-m {
        width: 32%;
    }

    @media screen and (min-width: 481px) and (max-width: 800px) {

        .card_size-m,
        .card_size-2xl {
            width: 49%;
        }
    }

    @media screen and (max-width: 480px) {

        .card_size-m,
        .card_size-xl,
        .card_size-2xl {
            width: 100%;
        }
    }

    .card__header {
        position: relative;
        line-height: 0;
    }

    *::-ms-backdrop,
    .card__header {
        display: flex;
    }

    .card__preview {
        max-width: 100%;
        height: auto;
    }

    *::-ms-backdrop,
    .card__preview {
        flex: 0 0 auto;
    }

    @card_main_action_size: 4em;

    .card__main-action {

        font-size: 100%;
        text-decoration: none;
        text-indent: -9999px;
        cursor: pointer;

        border: none;
        border-radius: 50%;
        padding: 0;
        box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2);

        position: absolute;
        right: 5%;
        bottom: 0;
        transform: translateY(50%);

        width: @card_main_action_size;
        height: @card_main_action_size;

        &:before {

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

        &:focus {
            outline: none;
        }
    }

    .card__body {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        flex-grow: 2;
    }

    .card__title {

        font-size: 140%;
        font-weight: 400;
        line-height: 1.5;

        margin-top: 0;
        margin-bottom: .8em;
    }

    .card__showmore {
        text-decoration: none;
    }

    .card__content {
        padding: 2.5em 4% 1.5em;
        flex-grow: 2;
    }

    .card__footer {

        padding: 1.5em 4%;
        border-top-width: 1px;
        border-top-style: solid;
        font-size: 90%;

        display: flex;
        justify-content: space-between;
    }

    .card__meta-item {
        display: inline-block;
        vertical-align: middle;
        margin-left: .8em;
    }

    .card__meta-icon {

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

    .card {
        background-color: #fff;
        color: @color_text;
        font-size: 1.4rem;
    }

    @media screen and (min-width: 801px) {

        .card_main,
        .card_size-2xl,
        .card_size-xl {
            font-size: 1.6rem;
        }
    }

    .card__main-action {
        background-color: @main_color;

        &:before {
            background-image: url("https://stas-melnikov.ru/cssgrid/bookmark.svg");
        }

        &:hover,
        &:focus {
            background-color: @dark_color;
        }
    }

    .card__footer {
        border-top-color: @optional_color;
    }

    .card__showmore {

        color: @dark_color;
        transition: color .3s ease-out;

        &:hover,
        &:focus {
            color: @main_color;
        }
    }

    .card__meta-comments {
        background-image: url("https://stas-melnikov.ru/cssgrid/comment.svg");
    }

    .card__meta-likes {
        background-image: url("https://stas-melnikov.ru/cssgrid/favorite.svg");
    }
</style>