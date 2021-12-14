<?php

use common\models\Produto;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\helpers\ArrayHelper;
use common\models\ValorProdutoMenorMaior;

$this->title = 'Meu Carrinho (' . count(Yii::$app->session['carrinho']) . ')';
$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
];

$this->registerJsFile(
    Url::to(['frontend/web/js/carrinho.js']),
    ['depends' => [frontend\assets\AppAsset::className()]]
);

?>




<div class="container hidden-xs" style="background-color: white">
    <div class="row">
        <div class="row">
            <div class="col-md-9">
                <h5>Aproveite e compre junto</h5>
            </div>
            <div class="col-md-3">
                <!-- Controls -->
                <div class="controls pull-right hidden-xs">
                    <a class="left  btn btn-success" style="background-color: #007576" href="#carousel-example"
                       data-slide="prev"><i class="fa fa-shield fa-rotate-90" style="color: white"></i>
                    </a>
                    <a class="right btn btn-success" style="background-color: #007576" href="#carousel-example"
                       data-slide="next"><i class="fa fa-shield fa-rotate-270 " style="color: white"></i>
                    </a>
                </div>
            </div>
        </div>
        <div id="carousel-example" class="carousel slide " data-ride="carousel">
            <!-- Wrapper for slides -->
            <div class="carousel-inner">
                <?php
                if (!YII_DEBUG) {
                    //$arrayid = [37768, 38614, 40156, 41523, 55917, 56129, 222241, 222292, 222293, 222494, 222496, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222528, 227171, 227171, 231348, 231514, 231596, 231618, 231646, 231647, 231648, 231649, 231651, 231664, 238131]; //producao
                    //$arrayid = [/*28943*/8673, 28942, /*229939*/251843, 56399, 38614, /*40156*/275078, 222292, 222293, 222248, 222241, 231596, 231618, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222496, 222528, 227171, 227171, 231646, 231647, /*231649*/249999, 231651, 231348, 231514, 41523, 56129, 222494, 231648, 231664, /*55917*/257602]; //Producao
                    $arrayid = [
                        230987,
                        230329,
                        230093,
                        230701,
                        230755,
                        230789,
                        230797,
                        228860,
                        230804,
                        231001,
                        230013,
                        230075,
                        230645,
                        230638,
                        230950,
                        228036,
                        228615,
                        230990,
                        231010,
                        231021,
                        231041,
                        231085,
                        245363,
                        245409,
                        245411,
                        245420,
                        245435,
                        245446,
                        245450,
                        245459,
                        245470,
                        245475,
                        245537,
                        245545,
                        245552,
                        245556,
                        245561,
                        245562,
                        245581,
                        245617,
                    ];
                } else {
                    //$arrayid = [222248, 38614, 40156, 41523, 55917, 56129, 222241, 222292, 222293, 222494, 222496, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222528, 227171, 227171, 231348, 231514, 231596, 231618, 231646, 231647, 231648, 231649, 231651, 231664, 238131]; //local
                    //$arrayid = [/*28943*/8673, 28942, 229939, 56399, 38614, /*40156*/275078, 222292, 222293, 222248, 222241, 231596, 231618, 222500, 222503, 222505, 222513, 222515, 222517, 222518, 222523, 222496, 222528, 227171, 227171, 231646, 231647, 231649, 231651, 231348, 231514, 41523, 56129, 222494, 231648, 231664, 55917]; //local
                    $arrayid = [
                        230987,
230329,
230093,
230701,
230755,
230789,
230797,
228860,
230804,
231001,
230013,
230075,
230645,
230638,
230950,
228036,
228615,
230990,
231010,
231021,
231041,
231085,
245363,
245409,
245411,
245420,
245435,
245446,
245450,
245459,
245470,
245475,
245537,
245545,
245552,
245556,
245561,
245562,
245581,
245617,

                    ];


                }

                for($x = 0 ; $x<=2 ; $x++){
                ?>
                <div class="item <?= (($x==0)? "active" : "")?>" >
                    <div class="row">
                        <?php

                        //$produtos = Produto::find()->andWhere(['id' => $arrayid])->orderBy('id')->all();
                        for ($i = ($x*4); $i <= ($x*4+3); $i++) {
                        //$produto = ArrayHelper::getValue($produtos, $i);
                        $produto = Produto::find()->andWhere(['=','id',ArrayHelper::getValue($arrayid, $i)])->one();
                        ?>
                        <div class="col-sm-3" style="background-color: white; cursor: pointer">

                            <div class="container">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="photo">
                                            <a href="<?= $produto->getUrl() ?>">
                                                <?php
                                                $alt = $produto->getLabel();
                                                echo $produto->getImage(['class' => "text-center", 'width' => '156', 'alt' => $alt, 'title' => $alt, 'itemprop' => 'image']);
                                                //$maxValue = ValorProdutoFilial::find()->ativo()->maiorValorProduto($produto->id)->one();
                                                //$minValue = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
                                                $minValue = ValorProdutoMenorMaior::findOne(['produto_id'=>$produto->id]);//->menor_valor;
                                                ?>
                                            </a>
                                        </div></div>
                                    <div class="col-sm-12">
                                        <div class="info" style=" white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                                            <a href="<?= $produto->getUrl() ?>">
                                                <span><?= $produto->getLabel() ?></span>
                                            </a>
                                        </div>
                                        <span itemprop="lowPrice"><?= $minValue->labelTituloMenor() ?></span>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-4">
                                        <a href="<?= $produto->getUrl() ?>" > <button type="button" class="btn btn-secondary btn-sm">   <!-- btn-primary -->
                                                Comprar

                                        </button> </a>
                                    </div>
                                    <div class="col-sm-4"></div>

                                </div>
                            </div>

                        </div>

                        <?php } ?>

                    </div>
                </div>

                <?php }?>
            </div>
        </div>
    </div><br>
</div><br>
<style>


    .col-item .photo img
    {
        margin: 0 auto;
        width: 100%;
    }

    .col-item .info
    {
        padding: 10px;
        border-radius: 0 0 5px 5px;
        margin-top: 1px;
    }

    .col-item:hover .info {
        background-color: #F5F5DC;
    }
    .col-item .price
    {
        /*width: 50%;*/
        float: left;
        margin-top: 5px;
    }

    .col-item .price h5
    {
        line-height: 20px;
        margin: 0;
    }


    .col-item .info .rating
    {
        color: #777;
    }

    .col-item .rating
    {
        /*width: 50%;*/
        float: left;
        font-size: 17px;
        text-align: right;
        line-height: 52px;
        margin-bottom: 10px;
        height: 52px;
    }

    .col-item .separator
    {
        border-top: 1px solid #E1E1E1;
    }



    .col-item .separator p
    {
        line-height: 20px;
        margin-bottom: 0;
        margin-top: 10px;
        text-align: center;
    }

    .col-item .separator p i
    {
        margin-right: 5px;
    }
    .col-item .btn-add
    {
        width: 50%;
        float: left;
    }

    .col-item .btn-add
    {
        border-right: 1px solid #E1E1E1;
    }

    .col-item .btn-details
    {
        width: 50%;
        float: left;
        padding-left: 10px;
    }
    .controls
    {
        margin-top: 20px;
    }
    [data-slide="prev"]
    {
        margin-right: 10px;
    }






    .col-sm-3 div{

        -webkit-transition: -webkit-transform .5s ease;
        transition: transform .5s ease;
        border-radius: 20px;
        border: #1b6d85 ;

    }

    div.col-sm-3:hover {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);

    }



</style>











</div>
<div class="carrinho-index">
    <div class="carrinho-height" >
        <?php
        if (empty(Yii::$app->session['carrinho'])) {
            Yii::$app->session->setFlash('info', 'Você ainda não tem nenhum produto no seu carrinho de compras.');
            echo Html::a('<i style="color: #fff" class="fa fa-arrow-left"></i> Continuar Comprando', ['/search'], ['class' => 'btn btn-primary']);
            return;

        }



        $carrinhoKeys = array_keys(Yii::$app->session['carrinho']);

        $dataProvider = new ActiveDataProvider(
            [
                'query' => \common\models\ProdutoFilial::find()->byIds($carrinhoKeys),
                'pagination' => [
                        'pageSize' => 20,
                ],
            ]
        );
        
        //testes carrinho

        \yii\widgets\Pjax::begin((['id' => 'idpjax']));?>


        <div class="container" style="background-color: white">
            <div class="row hidden-xs">
                <div class="col-lg-7 text-center">
                    Produto
            	</div>
            	<div class="col-lg-1 text-center">
            		Preço
            	</div>
            	<div class="col-lg-2 text-center">
					Quantidade
            	</div>
            	<div class="col-lg-1 text-center">
            		Total
            	</div>
            	<div class="col-lg-1 text-center">
            		Remover
            	</div>
            </div>
        
            <?= ListView::widget([
                    'dataProvider' => $dataProvider,
                    'itemView' => 'listaProdutosSelecionados',
            ])?>   
        
			<div class=" row separador_carrinho"></div>
        
        </div>
         
        <?php 
        //testes carrinho
        
        //\yii\widgets\Pjax::begin((['id' => 'idpjax']));
        /*echo '<div class="table-responsive">'.
            GridView::widget(
            [
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'attribute' => 'Produto',
                        'class' => 'yii\grid\DataColumn',
                        'format' => 'raw',
                        'headerOptions' => ['width' => '25%'],
                        'value' => function ($data) {
                            return Html::a($data->produto->nome, $data->produto->url);
                        },
                    ],
                    [
                        'attribute' => 'filial.nome',
                        'format' => 'text',
                        'header' => 'Vendedor',
                        'headerOptions' => ['width' => '20%'],
                    ],
                    [
                        'header' => 'Valor Unitário',
                        'class' => 'yii\grid\DataColumn',
                        'headerOptions' => ['width' => '10%'],
                        'value' => function ($data) {
                            $juridica = Yii::$app->params['isJuridica']();

                            return $data->getValorProdutoFilials()->ativo()->one()->getLabel($juridica);

                        },
                    ],
                    [
                        'header' => 'Quantidade',
                        'class' => 'yii\grid\DataColumn',
                        'headerOptions' => ['width' => '15%'],
                        'format' => 'raw',
                        'value' => function ($data) {
                            return '<form class="form-inline">
                                  <div class="form-group ">
                                    <div class="input-group">
                                      <div class="input-group-addon hidden-print"><i style="cursor:pointer;" onclick="return soma(\'#quantidade-field-' . $data->id . '\', -1, 1)" class="fa fa-minus"></i></div>
                                      <input type="text" data-id="' . $data->id . '" class="form-control quantidade-field" id="quantidade-field-' . $data->id . '" value="' . Yii::$app->session['carrinho'][$data->id] . '">
                                      <div class="input-group-addon hidden-print"><i style="cursor:pointer;" onclick="return soma(\'#quantidade-field-' . $data->id . '\', 1, 1)" class="fa fa-plus"></i></div>
                                    </div>
                                  </div>
                                </form>';
                        },
                    ],
                    [
                        'header' => 'Total',
                        'class' => 'yii\grid\DataColumn',
                        'headerOptions' => ['width' => '10%'],
                        'format' => 'raw',
                        'value' => function ($data) use (&$valorTotal) {
                            $juridica = Yii::$app->params['isJuridica']();
                            $valor = $data->getValorProdutoFilials()->ativo()->one()->getValorFinal(
                                    $juridica
                                ) * Yii::$app->session['carrinho'][$data->id];
                            $valorTotal += $valor;

                            return '<span id="total_produto_' . $data->id . '">' . Yii::$app->formatter->asCurrency($valor) . '</span>';

                        },

                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{delete}',
                        'buttons' => [
                            'delete' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash" onclick="removerProduto(' . $model->id . ')" style="padding-right: 5px; cursor:pointer"></span>',
                                    null, [
                                        'title' => Yii::t('yii', 'Delete'),
                                    ]);
                            }
                        ]
                    ],
                ],
            ]
        )."</div>";*/
        ?>
        <?php \yii\widgets\Pjax::end(); ?>

        <!--<div class="container-frete" style="float:left">
            <div class="form-inline" action="salvar-carrinho">
                <div class="form-group">
                    <label class="sr-only" for="exampleInputEmail3">Digite seu Cep:</label>
                    <?= ""/*MaskedInput::widget([
                        'name' => 'seu-cep',
                        'mask' => '99999-999',
                        'value' => Yii::$app->params['getCepComprador'](true),
                        'options' => [
                            'id' => 'seu_cep',
                            'placeholder' => 'Digite seu Cep:',
                            'class' => 'form-control seu-cep',
                        ]
                        ])*/
                    ?>
                </div>
                <button id="calcula-frete" class="btn btn-primary"><i class="fa no-color fa-truck"></i>
                    Calcular Frete
                </button>
            </div>
            <br/>

            <div id="resultado-frete" class="resultado-frete">

            </div>


        </div>
        <br>-->

        <div class="container hidden-xs">
            <div class="row">
                <div class="col-md-3"><div class="pull-left">
                        <a href="https://www.pecaagora.com/" class="btn btn-primary"><i style="color: #f2f2f2;" class="fa fa-arrow-left "></i>
                            Continuar Comprando</a>
                    </div></div>
                <div class="col-md-6"></div>
                <div class="col-md-3"><div style="float:right;">
                        <a type="submit" style="text-decoration:none;  " href="<?= Url::to("carrinho/update-address-confirmar?from=checkout") ?>" class="btn-lg btn-danger hidden-print">
                            <i class="fa no-color fa-check-circle gro"></i> Finalizar Compra
                        </a>
                    </div></div>
            </div>
        </div>
        <div class="container hidden-lg hidden-md hidden-sm">
    		<div class="row">
            	<div class=" row col-xs-12">
            		<div class="">
                        <a href="https://www.pecaagora.com/" class="btn btn-primary"><i style="color: #f2f2f2;" class="fa fa-arrow-left "></i>
                            Continuar Comprando</a>
                    </div>
            	</div>

        	</div>
        </div>
        <br><br>
    </div>
</div>


<style>

    .col-item
    {
        border: 1px solid #ffffff;
        border-radius: 5px;
        background: #FFF;
    }
    .col-item .photo img
    {
        margin: 0 auto;
        width: 100%;
    }

    .col-item .info
    {
        padding: 10px;
        border-radius: 0 0 5px 5px;
        margin-top: 1px;
    }

    .col-item:hover .info {
        background-color: #F5F5DC;
    }
    .col-item .price
    {
        /*width: 50%;*/
        float: left;
        margin-top: 5px;
    }

    .col-item .price h5
    {
        line-height: 20px;
        margin: 0;
    }

    .price-text-color
    {
        color: #219FD1;
    }

    .col-item .info
    {
        color: #777;
    }

    .col-item .rating
    {
        /*width: 50%;*/
        float: left;
        font-size: 17px;
        text-align: right;
        line-height: 52px;
        margin-bottom: 10px;
        height: 52px;
    }

    .col-item .separator
    {
        border-top: 1px solid #E1E1E1;
    }

    .clear-left
    {
        clear: left;
    }

    .col-item .separator p
    {
        line-height: 20px;
        margin-bottom: 0;
        margin-top: 10px;
        text-align: center;
    }

    .col-item .separator p i
    {
        margin-right: 5px;
    }
    .col-item .btn-add
    {
        width: 50%;
        float: left;
    }

    .col-item .btn-add
    {

    }

    .col-item .btn-details
    {
        width: 50%;
        float: left;
        padding-left: 10px;
    }
    .controls
    {
        margin-top: 20px;
    }
    [data-slide="prev"]
    {
        margin-right: 10px;
    }

</style>


<!-- 07-11-2019 BOTAO MOBILE -->



<style>
    .fab {
        background: rgba(0, 129, 130, 0.22);
        height: 60px;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;;
        padding: 10px 15px;
        z-index: 100;
    }

    button {
        display: block;
    }

    @media (max-width: 768px) {
        button {
            display: block;
        }
    }


    .fab button{
        cursor: pointer;
        width: 48px;
        height: 48px;
        border-radius: 3px;
        background-color: #cb60b3;
        border: none;
        box-shadow: 0 1px 5px rgba(0,0,0,.4);
        font-size: 24px;
        color: white;
    }

    .fab button.main{
        width: 200px;
        height: 40px;
        left: 50px;
        background-color: #ff0005;
        bottom: 0;
        z-index: 20;
    }

    .fab button.main:before{
        content: 'Finalizar compra';
        font-size: 20px;
    }


    .fab button.main:active,
    .fab button.main:focus{
        outline: none;
        background-color: #118522;
        box-shadow: 0 3px 8px rgba(0,0,0,.5);
    }


    /*  inicio botao 2  */

    .fab button.main2:active,
    .fab button.main2:focus{
        outline: none;
        background-color: #118522;
        box-shadow: 0 3px 8px rgba(0,0,0,.5);
    }

    .fab button.main2:before{
        content: 'Comprar';
        font-size: 20px;
    }

    .fab button.main2{
        width: 160px;
        height: 30px;
        background-color: #ff0005;
        right: 50px;
        bottom: 0;
        z-index: 20;
    }
    .fab {
        display: none;
    }

    @media (max-width: 768px) {
        .fab {
            display: flex;
        }
    }
    .preco{
        color: #000000;
        border-width: 2px;
        border-style: dashed;
        border-color: #000000;
        height: 30px;
        font-size: 18px;
        right: 20px;
        font-weight: bold

    }
    a:link {text-decoration: none}
    a:visited {text-decoration: none}
    a:hover {text-decoration: underline;
        color: #118522;
    }


</style>
<div class="container">
    <div class="row fab">
        <div class="col-xs-4"></div>
        <div class="col-xs-4"> <a type="submit" href="<?= Url::to("carrinho/update-address-confirmar?from=checkout") ?>">
                <button type="button" class="main"> <i class="fa no-color fa-check-circle gro"></i>
                </button>
            </a></div>
        <div class="col-xs-4"></div>
    </div>
</div>





<!-- BOTÃO MOBILE -->