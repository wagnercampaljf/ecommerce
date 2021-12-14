<?php

error_reporting(E_ALL);

ini_set("display_errors", 1);

use backend\models\NotaFiscalProduto;
use backend\models\NotaFiscalProdutoSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\popover\PopoverX;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\NotaFiscalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notas Fiscais';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nota-fiscal-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/nota-fiscal/index-nota-pedido']) ?>">
        <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <input type="text" name="filtro" id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist" placeholder="Procure por dados da NF." value="">
            <span class="input-group-btn">
                <button type="submit" class="btn btn-default btn-lg control " style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
            </span>
        </div>

    </form>

    <?php

    if (isset($dataProvider)) {

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'tipo',
                [
                    'label' => 'Pedido ID',
                    'format' => 'raw',
                    'value' => function ($dataProviderProduto) {
                        if ($dataProviderProduto['tipo'] == 'Pedido ML') {
                            return Html::a(Html::encode($dataProviderProduto['pedido_id']), ['/pedidos-mercado-livre/mercado-livre-view', 'id' => $dataProviderProduto['pedido_id']]);
                        } else if ($dataProviderProduto['tipo'] == 'Pedido Estoque') {
                            return Html::a(Html::encode($dataProviderProduto['pedido_id']), ['/pedido-compra-produto-filial/update', 'id' => $dataProviderProduto['pedido_id']]);
                        } else {
                            return Html::a(Html::encode($dataProviderProduto['pedido_id']), ['/pedidos/view', 'id' => $dataProviderProduto['pedido_id']]);
                        }
                    },
                ],
                [
                    'label' => 'Data pedido',
                    'attribute' => 'data_pedido',
                    'value' => function ($dataProviderProduto) {
                        $data = substr($dataProviderProduto['data_pedido'], 8, 2) . '/' . substr($dataProviderProduto['data_pedido'], 5, 2) . '/' . substr($dataProviderProduto['data_pedido'], 0, 4);
                        return $data;
                    },
                ],

                'nome',
                'pa',
                'nome_produto',
                'codigo_global',
                'nome_filial',
                'valor',
                'quantidade',
            ],
            'containerOptions' => ['style' => 'overflow: auto'],
            'headerRowOptions' => ['class' => 'kartik-sheet-style'],
            'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        ]);
    }
    ?>

</div>

<style>
    .container-xxl {
        width: 93%;
        margin: auto;
    }
</style>