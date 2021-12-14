<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\ProdutoFilial;
use common\models\ValorProdutoMenorMaior;
use console\controllers\actions\omie\Omie;
use frontend\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use kartik\grid\GridPerfectScrollbarAsset;
use Mpdf\Tag\Dd;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProdutoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pesquisa de Produtos';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="produto-index">

    <div class="clearfix col-md-auto cab_fixo  " id="sumir" style="padding: 1px;  background: linear-gradient(#f7f7f7, #f7f7f7); height: 56px">
        <div class="container">
            <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['produto/pesquisar']) ?>">
                <div class="input-group col-md-5" style="padding-left: 0px !important;padding-right: 0px !important;">
                    <input type="text" name="termo" id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist" placeholder="Cód PA; Cód Global; Cód Fabr; Nome Prod ..." autofocus="true">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576; width: 50px; height: 45px" id="main-search-btn"><i class="fa fa-search" style="color: white" value="pesqusiar"></i></button>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <?=

    !is_null($dataProvider) ?

        GridView::widget([
            'id' => 'kv-grid-demo',
            'dataProvider' => $dataProvider,
            'bordered' => true,
            'striped' => false,
            'responsive'=>false,
            'responsiveWrap'=>false,
            'floatHeader'=>false,
            'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
            'headerRowOptions'=>['class'=>'kartik-sheet-style'],
            'filterRowOptions'=>['class'=>'kartik-sheet-style'],
            'persistResize'=>false,
            'pjax'=>true,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],


                [
                    'attribute' => 'id',
                    'header' => 'Id - PA',
                    'contentOptions' => ['style' => 'min-width: 100px;']
                ],
                [
                    'attribute' => 'nome',
                    'header' => '         Nome',
                    'contentOptions' => ['style' => 'min-width: 240px;']

                ],
                [
                    'attribute' => 'codigo_global',
                    'header' => 'Cod Glob',
                ],
                [
                    'attribute' =>   'codigo_fabricante',
                    'header' => 'Cod Fabr',
                ],
                [
                    'attribute' =>   'marca',
                    'header' => 'Marca',
                ],
                [
                    'attribute' =>   '',
                    'header' => 'Venda',
                    'content' => function ($dataProvider) {
                        //$minValue = \common\models\ValorProdutoFilial::find()->ativo()->menorValorProduto($dataProvider['id'])->one();
                        //return $minValue['valor'];
                        $valor_produto_menor_maior = ValorProdutoMenorMaior::find()->andWhere(['=', 'produto_id', $dataProvider['id']])->one();
                        return $valor_produto_menor_maior["menor_valor"];
                    }
                ],
                [
                    'attribute' =>   '',
                    'header' => 'Cotação',
                    'content' => function ($dataProvider) {
                        $minValue = \common\models\ValorProdutoFilial::find()->ativo()->menorValorProduto($dataProvider['id'])->one();
                        return $minValue['valor_compra'];
                    }
                ],
                [
                    'attribute' =>   'valor_ml',
                    'header' => 'Ult Compra',
                    'content' => function ($dataProvider) {
                        $valor_ultima_cotacao = ProdutoFilial::find()
                            //->innerJoin("pedido_mercado_livre_produto_produto_filial")
                            ->join('inner join', 'pedido_mercado_livre_produto_produto_filial', 'pedido_mercado_livre_produto_produto_filial.produto_filial_id = produto_filial.id')
                            ->join('inner join', 'valor_produto_filial', 'produto_filial.id = valor_produto_filial.produto_filial_id')
                            ->andWhere(['=', 'produto_id', $dataProvider['id']])
                            //->andWhere(['in', 'produto_filial.id', ' (select distinct) '])
                            ->orderBy(["pedido_mercado_livre_produto_produto_filial.id" => SORT_DESC])
                            ->one();

                        if ($valor_ultima_cotacao) {
                            return $valor_ultima_cotacao->valor;
                        } else {
                            return 0;
                        }
                        //return 0;
                    }
                ],

                [
                    'attribute' =>   '',
                    'header' => 'Markup',
                    'content' => function ($dataProvider) {
                        /*$minValue = \common\models\ValorProdutoFilial::find()->ativo()->menorValorProduto($dataProvider['id'])->one();
                        if ($minValue['valor_compra'] != '') {
                            $markup = Yii::$app->db->createCommand("select margem from markup_detalhe md 
                            inner join markup_mestre mm on md.markup_mestre_id = mm.id 
                            where (" . $minValue['valor_compra'] . "::float between valor_minimo and valor_maximo) and mm.e_markup_padrao = '1'
                             order by mm.id desc 
                             limit 1")->queryScalar();
                            return $markup;
                        } else {*/
                        return 0;
                        //}
                    }
                ],

                [
                    'attribute' => 'quantidade_sp',
                    'header' => 'Est SP1',
                    /*'content' => function ($dataProvider) {
                        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'produto_id', $dataProvider['id']])
                            ->andWhere(['=', 'filial_id', 96])
                            ->one();

                        if ($produto_filial) {
                            return $produto_filial->quantidade;
                        } else {
                            return 0;
                        }
                    }*/
                    // 'content' => function ($dataProvider) {

                    //     $APP_KEY_OMIE_SP                    = '468080198586';
                    //     $APP_SECRET_OMIE_SP                 = '7b3fb2b3bae35eca3b051b825b6d9f43';
                    //     $omie = new Omie(1, 1);

                    //     $body = [
                    //         "call" => "PosicaoEstoque",
                    //         "app_key" => $APP_KEY_OMIE_SP,
                    //         "app_secret" => $APP_SECRET_OMIE_SP,
                    //         "param" => [
                    //             "cod_int"       => "PA" . $dataProvider['id'],
                    //             "data"   => date('d/m/Y'),
                    //         ]
                    //     ];
                    //     $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=", $body);
                    //     if ($response_omie['httpCode'] !== 200) {
                    //         return '0';
                    //     } else {
                    //         return $response_omie['body']['saldo'];
                    //     }
                    // }
                ],

                [
                    'attribute' => 'quantidade_mg',
                    'header' => 'Est MG',
                    /*'content' => function ($dataProvider) {
                        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'produto_id', $dataProvider['id']])
                            ->andWhere(['=', 'filial_id', 94])
                            ->one();

                        if ($produto_filial) {
                            return $produto_filial->quantidade;
                        } else {
                            return 0;
                        }
                    }*/
                    // 'content' => function ($dataProvider) {

                    //     $APP_KEY_OMIE_MG                    = '469728530271';
                    //     $APP_SECRET_OMIE_MG                 = '6b63421c9bb3a124e012a6bb75ef4ace';
                    //     $omie = new Omie(1, 1);

                    //     $body = [
                    //         "call" => "PosicaoEstoque",
                    //         "app_key" => $APP_KEY_OMIE_MG,
                    //         "app_secret" => $APP_SECRET_OMIE_MG,
                    //         "param" => [
                    //             "cod_int"       => "PA" . $dataProvider['id'],
                    //             "data"   => date('d/m/Y'),
                    //         ]
                    //     ];
                    //     $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=", $body);
                    //     if ($response_omie['httpCode'] !== 200) {
                    //         return '0';
                    //     } else {
                    //         return $response_omie['body']['saldo'];
                    //     }
                    // }
                ],

                [
                    'attribute' => 'quantidade_spfilial',
                    'header' => 'Est. SP2',
                    /*'content' => function ($dataProvider) {
                        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'produto_id', $dataProvider['id']])
                            ->andWhere(['=', 'filial_id', 95])
                            ->one();

                        if ($produto_filial) {
                            return $produto_filial->quantidade;
                        } else {
                            return 0;
                        }
                    }*/
                    // 'content' => function ($dataProvider) {

                    //     $APP_KEY_OMIE_CONTA_DUPLICADA       = '1017311982687';
                    //     $APP_SECRET_OMIE_CONTA_DUPLICADA    = '78ba33370fac6178da52d42240591291';
                    //     $omie = new Omie(1, 1);

                    //     $body = [
                    //         "call" => "PosicaoEstoque",
                    //         "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                    //         "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                    //         "param" => [
                    //             "cod_int"       => "PA" . $dataProvider['id'],
                    //             "data"   => date('d/m/Y'),
                    //         ]
                    //     ];
                    //     $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=", $body);
                    //     if ($response_omie['httpCode'] !== 200) {
                    //         return '0';
                    //     } else {
                    //         return $response_omie['body']['saldo'];
                    //     }
                    // }
                ],
                [
                    'attribute' => 'quantidade_lng',
                    'header' => 'LNG',
                    /*'content' => function ($dataProvider) {
                        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'produto_id', $dataProvider['id']])
                            ->andWhere(['=', 'filial_id', 60])
                            ->one();

                        if ($produto_filial) {
                            return $produto_filial->quantidade;
                        } else {
                            return 0;
                        }
                    }*/
                ],
                [
                    'attribute' => 'quantidade_vannucci',
                    'header' => 'VNC',
                    /*'content' => function ($dataProvider) {
                        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'produto_id', $dataProvider['id']])
                            ->andWhere(['=', 'filial_id', 38])
                            ->one();

                        if ($produto_filial) {
                            return $produto_filial->quantidade;
                        } else {
                            return 0;
                        }
                    }*/
                ],
                [
                    'attribute' => 'quantidade_morelate',
                    'header' => 'MLT',
                    /*'content' => function ($dataProvider) {
                        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'produto_id', $dataProvider['id']])
                            ->andWhere(['=', 'filial_id', 43])
                            ->one();

                        if ($produto_filial) {
                            return $produto_filial->quantidade;
                        } else {
                            return 0;
                        }
                    }*/
                ],

                [
                    'attribute' => 'quantidade_br',
                    'header' => 'BR',
                    /*'content' => function ($dataProvider) {
                        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'produto_id', $dataProvider['id']])
                            ->andWhere(['=', 'filial_id', 72])
                            ->one();

                        if ($produto_filial) {
                            return $produto_filial->quantidade;
                        } else {
                            return 0;
                        }
                    }*/
                ],

            ],
            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
            'headerRowOptions' => ['class' => 'kartik-sheet-style'],
            'filterRowOptions' => ['class' => 'kartik-sheet-style'],
            'pjax' => true, // pjax is set to always true for this demo
            // set your toolbar
            'toolbar' =>  [
                [
                    'content' =>
                    Html::button('<i class="fas fa-plus"></i>', [
                        'class' => 'btn btn-success',
                        'title' => 'Add Book',
                        'onclick' => 'alert("This will launch the book creation form.\n\nDisabled for this demo!");'
                    ]) . ' ' .
                        Html::a('<i class="fas fa-redo"></i>', ['grid-demo'], [
                            'class' => 'btn btn-outline-secondary',
                            'title' => 'Reset Grid',
                        ]),
                    'options' => ['class' => 'btn-group mr-2']
                ],
                '{export}',
                '{toggleData}',
            ],
            'toggleDataContainer' => ['class' => 'btn-group mr-2'],
            // set export properties
            'export' => [
                'fontAwesome' => true
            ],
            // parameters from the demo form
            'persistResize' => false,
            'toggleDataOptions' => ['minCount' => 10],

        ]) : "Nenhum produto encontrado. ";

    ?>
</div>