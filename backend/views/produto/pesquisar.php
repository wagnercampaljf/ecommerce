<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\ValorProdutoMenorMaior;
use backend\models\NotaFiscalProduto;
use yii\data\ActiveDataProvider;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

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
            'responsive' => false,
            'responsiveWrap' => false,
            'floatHeader' => false,
            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
            'headerRowOptions' => ['class' => 'kartik-sheet-style'],
            'filterRowOptions' => ['class' => 'kartik-sheet-style'],
            'persistResize' => false,
            'pjax' => true,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'width' => '50px',
                    'value' => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail' => function ($dataProvider, $key, $index, $column) {
                        $model = (new \yii\db\Query())
                            ->Select(['*'])
                            ->from("nota_fiscal_produto")
                            ->innerJoin("nota_fiscal", "nota_fiscal.id = nota_fiscal_produto.nota_fiscal_id")
                            ->where("(pa_produto = 'PA" . $dataProvider['id'] . "') and finalidade_emissao <> 4 and (cod_cliente <> 2641483458 and cod_cliente <> 1018587858 or cod_cliente is null) and tipo_nf = 0")
                            ->orderBy(["nota_fiscal.data_emissao" => SORT_DESC])->limit(10);

                        $provider = new ActiveDataProvider([
                            'query' => $model,
                            'pagination' => [
                                'pageSize' => 10,
                                'totalCount' => 100,
                            ],
                        ]);

                        return GridView::widget([
                            'dataProvider' => $provider,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                [
                                    'attribute' =>   'numero_nf',
                                    'header' => 'NF',
                                ],
                                [
                                    'attribute' =>   '',
                                    'header' => 'Data Compra',
                                    'content' => function ($provider) {
                                        return substr($provider['data_nf'], 8, 2) . '/' . substr($provider['data_nf'], 5, 2) . '/' . substr($provider['data_nf'], 0, 4);
                                    }
                                ],
                                [
                                    'attribute' =>   'fornecedor',
                                    'header' => 'Fornecedor',
                                ],
                                [
                                    'attribute' =>   '',
                                    'header' => 'Valor NF',
                                    'content' => function ($provider) {
                                        return number_format($provider['valor_produto'] / $provider['qtd_comercial'], 2, ",", "");
                                    }
                                ],

                                [
                                    'attribute' =>   '',
                                    'header' => 'Valor Real',
                                    'content' => function ($provider) {
                                        if ($provider['valor_real_produto'] !== null) {
                                            return number_format($provider['valor_real_produto'], 2, ",", "");
                                        } else {
                                            return '0,00';
                                        }
                                    }
                                ],
                                [
                                    'attribute' =>   'qtd_comercial',
                                    'header' => 'Qtd.',
                                ],
                            ],
                            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
                            'pjax' => true,
                            'responsive' => true,
                            'hover' => true,
                            'responsiveWrap' => false,
                        ]);
                    },
                    'expandOneOnly' => true
                ],


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
                    'attribute' => 'quantidade_fisico',
                    'header' => 'Est Físico SP',
                ],
                [
                    'attribute' => 'quantidade_sp2',
                    'header' => 'Est SP2',
                ],

                [
                    'attribute' => 'quantidade_mg1',
                    'header' => 'Est MG1',
                ],

                [
                    'attribute' => 'quantidade_spfilial3',
                    'header' => 'Est. SP3',
                ],
                [
                    'attribute' => 'quantidade_mg4',
                    'header' => 'Est. MG4',
                ],
                [
                    'attribute' =>   'marca',
                    'header' => 'Marca',
                ],
                [
                    'attribute' =>   '',
                    'header' => 'Venda',
                    'content' => function ($dataProvider) {
                        $valor_produto_menor_maior = ValorProdutoMenorMaior::find()->andWhere(['=', 'produto_id', $dataProvider['id']])->one();
                        return $valor_produto_menor_maior["menor_valor"];
                    }
                ],
                [
                    'attribute' =>   '',
                    'header' => 'Cotação',
                    'content' => function ($dataProvider) {

                        $produto_filial_lng = ProdutoFilial::find()->andWhere(["=", "produto_id", $dataProvider['id']])
                            ->andWhere(["=", "filial_id", 60])
                            ->one();
                        if ($produto_filial_lng) {
                            $valor_produto_filial_lng = ValorProdutoFilial::find()->andWhere(["=", "produto_filial_id", $produto_filial_lng->id])
                                ->orderBy(["dt_inicio" => SORT_DESC])
                                ->one();
                            if ($valor_produto_filial_lng) {
                                $ipi = 0;
                                if (!is_null($produto_filial_lng->produto->ipi)) {
                                    $ipi = $valor_produto_filial_lng->valor_compra * ($produto_filial_lng->produto->ipi / 100);
                                }
                                return $valor_produto_filial_lng->valor_compra + $ipi;
                            }
                        }

                        $minValue = \common\models\ValorProdutoFilial::find()->ativo()->menorValorProduto($dataProvider['id'])->one();
                        //echo "<pre>"; var_dump($minValue); echo "</pre>"; die;
                        $ipi = 0;
                        if ($minValue) {
                            $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $minValue->produto_filial_id])->one();

                            if (!is_null($produto_filial->produto->ipi)) {
                                $ipi = $minValue['valor_compra'] * ($produto_filial->produto->ipi / 100);
                            }
                        }

                        //echo "<pre>"; print_r($produto_filial->produto); echo "</pre>"; die;
                        return $minValue['valor_compra'] + $ipi;
                    }
                ],
                [
                    'attribute' =>   '',
                    'header' => 'Ult Compra',
                    'content' => function ($dataProvider) {
                        $model = NotaFiscalProduto::find()
                            ->join('inner join', 'nota_fiscal', 'nota_fiscal.id = nota_fiscal_produto.nota_fiscal_id')
                            ->where("(pa_produto = 'PA" . $dataProvider['id'] . "') and finalidade_emissao <> 4 and (cod_cliente <> 2641483458 or cod_cliente is null) and tipo_nf = 0")
                            ->orderBy(["nota_fiscal.data_emissao" => SORT_DESC])->limit(1)
                            ->one();

                        if ($model['valor_real_produto'] !== null) {
                            return $model['valor_real_produto'];
                        } else {
                            if (isset($model['valor_produto'])) {
                                return number_format($model['valor_produto'] / $model['qtd_comercial'], 2, ",", "");
                            } else {
                                return 0;
                            }
                        }
                    }
                ],

                [
                    'attribute' => 'quantidade_lng',
                    'header' => 'LNG',
                ],
                [
                    'attribute' => 'quantidade_vannucci',
                    'header' => 'VNC',
                ],
                [
                    'attribute' => 'quantidade_morelate',
                    'header' => 'MLT',
                ],

                [
                    'attribute' => 'quantidade_br',
                    'header' => 'BR',
                ],

            ],
            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
            'headerRowOptions' => ['class' => 'kartik-sheet-style'],
            'filterRowOptions' => ['class' => 'kartik-sheet-style'],
            'pjax' => true, // pjax is set to always true for this demo
        ]) : "Nenhum produto encontrado. ";

    ?>
</div>
