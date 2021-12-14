<?php
/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 13/09/17
 * Time: 18:32
 */

use yii\helpers\Html;
use backend\models\PedidoMercadoLivreProdutoSearch;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\PedidoMercadoLivreProduto;
use common\models\ProdutoFilial;
use common\models\Imagens;

//echo "<pre>"; print_r($model); echo "</pre>"; die;

$this->params['active'] = 'pedidos';

$this->title = 'Pedido #' . $model['pedido_meli_id'];
?>
<div class="container">
    <article class="card card_main">

        <div class="card__body">
            <div class="card__content">
                <div class="card__description">

                    <p>Número do Pedido <?= '#' . $model['id'] ?></p>


                </div>
            </div>

        </div>
    </article>
    <div class="cards">
        <article class="card card_main">

            <div class="card__body">
                <div class="card__content">
                    <div class="card__description">
                        <p><?= $model['buyer_first_name'] ?> <?= $model['buyer_last_name'] ?> |
                            <?= $model['buyer_nickname'] ?> | <?= $model['buyer_doc_type'] ?> <?= $model['buyer_doc_number'] ?> | Tel <?= $model['receiver_phone'] ?> | Email <?= $model['buyer_email'] ?></p>
                    </div>
                </div>

            </div>
        </article>

        <article class="card card_main">

            <div class="card__body">
                <div class="card__content">
                    <div class="card__description">
                        <p> Dados do envio</p><hr>
                        <p>Envio: <?= $model['shipping_option_name'] ?> </p>
                        <p><?= $model['receiver_street_name'] ?>, <?=  $model['receiver_street_number'] ?> </p>
                        <p>CEP <?=  $model['receiver_zip_code'] ?> - <?=  $model['receiver_city_name'] ?> - <?=  $model['receiver_state_name'] ?>  </p>
                        <p>Quem recebe: <?=  $model['receiver_name'] ?> - Tel <?= $model['receiver_phone'] ?> </p>

                    </div>
                </div>

            </div>
        </article>

        <article class="card card_main">

            <div class="card__body">
                <div class="card__content">
                    <div class="card__description">
                        <p> Dados para sua fatura</p><hr>
                        <p><?= $model['buyer_first_name'] ?> <?= $model['buyer_last_name'] ?> - <?= $model['buyer_doc_type'] ?> <?= $model['buyer_doc_number'] ?></p>
                        <p><?= $model['receiver_street_name'] ?>, <?=  $model['receiver_street_number'] ?> - <?=  $model['receiver_city_name'] ?> - CEP <?=  $model['receiver_zip_code'] ?> - <?=  $model['receiver_state_name'] ?> </p>


                    </div>
                </div>

            </div>
        </article>



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
    }</style>



<div class="portlet light" id="pedido">
    <div class="portlet-body">


        <?php
        
        //$pedido_mercado_livre_produtos = PedidoMercadoLivreProduto::find()->andWhere(['=','produto_mercado_livre_id',$model->id])->all();
        
        //$dataProvider = new \yii\data\ArrayDataProvider(['models' => $model['items'], 'pagination' => false]);
        //$dataProvider = new \yii\data\ArrayDataProvider(['models' => $pedido_mercado_livre_produtos, 'pagination' => false]);
        
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                
                'id',
                'produto_meli_id',
                [
                    'attribute' => 'title',
                    'label' => 'Produto',
                    'value' => 'title',
                ],


                [
                    'attribute' => 'quantity',
                    'label' => 'Quantidade',
                    'value' => 'quantity',
                ],

                [
                    'attribute' => 'unit_price',
                    'label' => 'Preço unitário',
                    'value' => 'unit_price',
                ],

                [
                    'attribute' => 'sale_fee',
                    'label' => 'Taxa de venda',
                    'value' => 'sale_fee',
                ],
                [
                    'attribute' => 'img',
                    'format'    =>'html',
                    'label'     => 'Imagem',
                    'value'     => function($data){
                        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $data['id']])->one();
                        if($produto_filial){
                            //return Html::a($data['name'], 'https://www.pecaagora.com/p/'. $produto_filial->produto_id);
                            //$imagens = Imagens::find()->andWhere(['=','produto_id', $produto_filial->produto->id])->orderBy('ordem')->one();
                            //if($imagens){
                            //    return $imagens->imagem_sem_logo;
                            //}
                            echo $produto_filial->produto->getUrlImage(); //die;
                            return Html::img(
                                $produto_filial->produto->getUrlImage(),
                                ['width' => '60px']
                            );
                        }
                        else{
                            return "";//$data['name'];
                        }
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                        return Html::a(' <span class="glyphicon glyphicon-search" ></span > ',
                            Url::to(['/pedidos/mercado-livre-produto', 'id' => $model['id']]), [
                                'title' => Yii::t('yii', 'View'),
                            ]);
                        
                        }
                    ]
                ],
                // 'condition',
                // 'quantity',
                // 'unit_price',
                // 'full_unit_price',
                // 'sale_fee',
                // 'listing_type_id',
                
                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]);?>

        <span class="price">
            <p align="right" class="bg-success">
                Frete: <?= Yii::$app->formatter->asCurrency($model['shipping_option_list_cost']) ?>
            </p>
        </span>
        <span class="price lead">
            <p align="right" class="bg-success">
                Total: <?= Yii::$app->formatter->asCurrency($model['total_amount']) ?>
            </p>
        </span>
        <a class="btn btn-primary" href="<?= Url::to(['/pedidos']) ?>" role="button">Voltar</a>
        
        <a class="btn btn-primary" href="<?= Url::to(['/pedidos/gerar-x-m-l-omie', 'id'=>$model['id']]) ?>" role="button">Gerar XML (Omie)</a>
    </div>
</div>

