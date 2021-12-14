<?php
/* @var $this yii\web\View */
use vendor\iomageste\Moip\Moip;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Banner;
use yii\widgets\ActiveForm;
use common\models\ProdutoFilial;
use yii\widgets\ListView;

$comprador = Yii::$app->user->getIdentity();
$this->params['active'] = 'pedidos';
$this->registerJsFile(
    Url::base() . '/js/pedidos.js'
);
$this->title = 'Pedido #' . $model->id;
?>




<div class="container">

    <div class="row">
        <div class="col-sm-7">


            <p>Venda: <?= $model['id'] ?> | <?= Yii::$app->formatter->asDate($model->dt_referencia) ?></p><br>
            <div class="cards">
                <article class="card card_main" style="background-color: #f5f5f5">

                    <div class="card__body">
                        <div class="card__content">
                            <div class="card__description">
                                <p><span style="font-weight: bold; color: rgba(41,41,41,0.95)"><?= $model->comprador->empresa->nome; ?></span> |
                                    <?= $model->comprador->empresa->nome; ?> | <?= $model->comprador->empresa->getDocumentoLabel(); ?> | <?= $model->comprador->empresa->telefone; ?></p>
                            </div>
                        </div>

                    </div>
                </article>


                <article class="card card_main">
                    <div class="card__body">
                        <div class="card__content">
                            <div class="card__description">
                                <?= ListView::widget([
                                    'dataProvider' => $dataProvider,
                                    'itemView' => 'listapedidosprodutos',
                                    'summary'=>'',
                                ]); ?>
                                <br><br><br>

                                <p style="font-weight: bold; color: rgba(41,41,41,0.95)"> Dados do envio</p>
                                <p style="font-size: 14px">Envio: <?= isset($model['transportadora']['nome']) ? $model['transportadora']['nome'] : 'Não selecionado' ?></>
                                <p style="font-size: 14px"><?= $model['comprador']['empresa']['enderecosEmpresa'][0] ?></p>
                                <p style="font-size: 14px">Quem recebe: <?= $model->comprador->empresa->nome; ?>  </p>
                                <p style="font-size: 14px">email:  <?= $model['comprador']['email'] ?> </p>
                                <p style="font-size: 14px">Telefone:  <?= Yii::$app->formatter->asTelefone($model['comprador']['empresa']['telefone']) ?> </p>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="card card_main">

                    <div class="card__body">
                        <div class="card__content">
                            <div class="card__description">
                                <p style="font-weight: bold; color: rgba(41,41,41,0.95)"> Dados para sua fatura</p>
                                <p style="font-size: 14px">Nome: <?= $model->comprador->empresa->nome; ?> - CPF/CNJP: <?= $model->comprador->empresa->getDocumentoLabel(); ?> </p>
                                <p style="font-size: 14px"><?= $model['comprador']['empresa']['enderecosEmpresa'][0] ?> </p>


                            </div>
                        </div>
                    </div>

                </article>

                <article class="card card_main">

                    <div class="card__body">
                        <div class="card__content">
                            <div class="card__description">
                                <p style="font-weight: bold; color: rgba(41,41,41,0.95)"> Dados de Pagamento</p>
                                <p style="font-size: 14px">ID Moip: <?= isset($model['transportadora']['nome']) ? $model['token_moip'] : '---' ?> </p>
                                <p style="font-size: 14px">Forma de Pagamento: <?= $model['formaPagamento']['nome'] ?> </p>


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
                                <?php
                                if ($model['statusAtual']['tipoStatus']['nome'] == 'Cancelado'){
                                    echo ' <p style="font-weight: bold; color: rgba(217,0,16,0.95)">CANCELADO</p>';
                                }elseif ($model['statusAtual']['tipoStatus']['nome'] == 'Concluído'){
                                    echo ' <p style="font-weight: bold; color: rgba(217,0,16,0.95)">CONCLUIDO</p>';
                                }
                                elseif ($model['statusAtual']['tipoStatus']['nome'] == 'Enviado'){
                                    echo ' <p style="font-weight: bold; color: rgba(27,109,133,0.95)">ENVIADO</p>';
                                }
                                elseif ($model['statusAtual']['tipoStatus']['nome'] == 'Pagamento Confirmado'){
                                    echo ' <p style="font-weight: bold; color: rgba(217,0,16,0.95)">PAGAMENTO CONFIRMADO</p>';
                                }

                                elseif ($model['statusAtual']['tipoStatus']['nome'] == 'Em Aberto'){
                                    echo ' <p style="font-weight: bold; color: rgba(217,0,16,0.95)">EM ABERTO</p>';
                                }


                                ?>


                                <p style="font-size: 14px">Nº <?= $model['id'] ?> | <?= Yii::$app->formatter->asDate($model->dt_referencia) ?></p><br>
                                <table class="table">

                                    <?= ListView::widget([
                                        'dataProvider' => $dataProvider,
                                        'itemView' => 'lista_pedidos_produtos_valor',
                                        'summary'=>'',
                                    ]); ?>



                                    <tr>
                                            <th scope="row" style="font-size: 14px; border:none !important;">Taxa Fixa</p></th>
                                            <td style="border:none !important;"></td>
                                            <td style="border:none !important;"></td>
                                            <td style="font-size: 14px; border:none !important;"><?= Yii::$app->formatter->asCurrency(Moip::TAXMOIP) ?></td>
                                        </tr>



                                    <tr>
                                        <th scope="row" style="font-size: 14px; border:none !important;">Juros</p></th>
                                        <td style="border:none !important;"></td>
                                        <td style="border:none !important;"></td>
                                        <td style="font-size: 14px; border:none !important;"><?= Yii::$app->formatter->asCurrency($model['valor_total'] - $model->valorTotalSemJuros - Moip::TAXMOIP); ?></td>
                                    </tr>


                                    <tr>
                                        <th scope="row" style="font-size: 14px; border:none !important;">Frete</p></th>
                                        <td style="border:none !important;"></td>
                                        <td style="border:none !important;"></td>
                                        <td style="font-size: 14px; border:none !important;"><?= Yii::$app->formatter->asCurrency($model['valor_frete']) ?></td>
                                    </tr>

                                    <tr>
                                        <th scope="row" >Total</p></th>
                                        <td ></td>
                                        <td></td>
                                        <td style="font-size: 14px;" ><?= Yii::$app->formatter->asCurrency($model['valor_total'] + $model['valor_frete']) ?></td>
                                    </tr>
                                </table>
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
                        textarea.form-control{
                            height: 250px;
                        }

                    </style>
                    EMAIL
                </div>
            </div>
        </article>
    </div>
</div>

<a class="btn btn-primary" href="<?= Url::to(['/pedidos']) ?>" role="button">Voltar</a>





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

    @media screen and (min-width: 801px){

        .card_size-2xl{

        .card__title{
            font-size: 170%;
        }
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

    @media screen and (min-width: 801px){

        .card_main, .card_size-2xl, .card_size-xl{
            font-size: 1.6rem;
        }
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




