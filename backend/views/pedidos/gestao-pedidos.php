<?php

use backend\controllers\PedidosController;
use backend\models\Administrador;
use backend\models\NotaFiscalProduto;
use backend\models\PedidoProdutoFilialCotacao;
use common\models\PedidoMercadoLivre;
use common\models\PedidoProdutoFilial;
use common\models\Transportadora;
use kartik\grid\GridView;
use common\models\PedidoMercadoLivreProduto;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;
use yii\helpers\Html;

$this->title = 'Pedidos Backend';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="pedidos">

    <?php

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'gridpedidos',
        'rowOptions' => function ($dataProvider) {
            $color = '';
            if ($dataProvider['e_verificado']) {
                $color = 'success';
            } else {
                $color = 'danger';
            }
            return [
                'data' => ['key' => $dataProvider['num_pedido']],
                'class' => [$color]
            ];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'width' => '50px',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($dataProvider, $key, $index, $column) {
                    $dataProviderItens = PedidosController::actionGestaoItensPedidos($dataProvider['num_pedido']);
                    return GridView::widget([
                        'dataProvider' => $dataProviderItens,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' =>   'pa',
                                'header' => 'PA',
                            ],
                            [
                                'attribute' =>   'nome_produto',
                                'header' => 'Produto',
                            ],
                            [
                                'attribute' =>   'cod_global',
                                'header' => 'Cód. Global',
                            ],
                            [
                                'attribute' =>   'quantidade',
                                'header' => 'Qtd.',
                            ],
                            [
                                'attribute' =>   'valor_cotacao',
                                'header' => 'Val.Cotação',
                                'content' => function ($dataProvider) {
                                    return number_format($dataProvider['valor_cotacao'], 2, ',', '');
                                }
                            ],
                            [
                                'attribute' =>   'valor_venda',
                                'header' => 'Venda',
                                'content' => function ($dataProvider) {
                                    return number_format($dataProvider['valor_venda'], 2, ',', '');
                                }
                            ],
                            [
                                'attribute' =>   'filial_nome',
                                'header' => 'Filial',
                            ],
                            [
                                'attribute' =>   'estoque_minimo',
                                'header' => 'Estoque Min.',
                            ],
                            [
                                'attribute' =>   'estoque_sp',
                                'header' => 'Est. SP',
                            ],
                            [
                                'attribute' =>   'estoque_mg',
                                'header' => 'Est. MG',
                            ],
                            [
                                'attribute' =>   '',
                                'header' => 'CFOP',
                                'content' => function ($dataProvider) {
                                    if (isset($dataProvider['id_nf'])) {
                                        $cfop = NotaFiscalProduto::findOne(['nota_fiscal_id' => $dataProvider['id_nf'], 'codigo_produto' => $dataProvider['pa']]);
                                        return $cfop['cod_fiscal_operacao_servico'];
                                    } else {
                                        return '';
                                    }
                                }
                            ],
                            [
                                'attribute' =>   '',
                                'header' => 'ICMS ST',
                                'content' => function ($dataProvider) {
                                    if (isset($dataProvider['id_nf'])) {
                                        $icms = NotaFiscalProduto::findOne(['nota_fiscal_id' => $dataProvider['id_nf'], 'codigo_produto' => $dataProvider['pa']]);
                                        return $icms['valor_icms'];
                                    } else {
                                        return '';
                                    }
                                }
                            ],

                        ],
                        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
                        'pjax' => true,
                        'responsive' => true,
                        'hover' => true,
                    ]);
                },
                'expandOneOnly' => true
            ],
            [
                'attribute' =>   'tipo',
                'header' => 'Natureza',
            ],
            [
                'attribute' =>   'data',
                'header' => 'Data',
                'content' => function ($dataProvider) {
                    return substr($dataProvider['data'], 8, 2) . '/' . substr($dataProvider['data'], 5, 2) . '/' . substr($dataProvider['data'], 0, 4);
                }
            ],
            [
                'attribute' =>   'vendedor',
                'header' => 'Vendedor',
            ],
            [
                'attribute' =>   'num_pedido',
                'header' => 'Num. Pedido',
            ],
            [
                'attribute' =>   'filial',
                'header' => 'Omie',
                'content' => function ($dataProvider) {
                    switch ($dataProvider['filial']) {
                        case 96:
                            return 'SP1';
                            break;
                        case 95:
                            return 'SP2';
                            break;
                        case 94:
                            return 'MG';
                            break;
                        default:
                            return 'SP1';
                    }
                }
            ],
            [
                'attribute' =>   'estado',
                'header' => 'Estado VENDA',
            ],
            'nome',
            [
                'attribute' =>   'documento',
                'header' => 'PF/PJ',
                'content' => function ($dataProvider) {
                    switch (strlen($dataProvider['documento']) > 11) {
                        case true:
                            return 'PJ';
                            break;
                        default:
                            return 'PF';
                    }
                }
            ],
            [
                'attribute' =>   'valor_total',
                'header' => 'Valor',
                'content' => function ($dataProvider) {
                    return number_format($dataProvider['valor_total'], 2, ',', '');
                }
            ],
            [
                'attribute' =>   '',
                'header' => 'Markup',
                'content' => function ($dataProvider) {
                    $valor = 0;
                    if ($dataProvider['tipo'] !== 'Pedido ML') {
                        $produtoFilials = PedidoProdutoFilial::findAll(['pedido_id' => $dataProvider['num_pedido']]);
                        foreach ($produtoFilials as $produtoFilial) {
                            $cotacao = PedidoProdutoFilialCotacao::findAll(['pedido_produto_filial_id' => $produtoFilial->id]);
                            foreach ($cotacao as $valorCotacao) {
                                $valor += ($valorCotacao->valor * $valorCotacao->quantidade);
                            }
                        }
                        if ($valor > 0) {
                            return number_format($dataProvider['valor_total'] / $valor, 2);
                        }
                    } else {
                        $pedido = PedidoMercadoLivre::findOne(['pedido_meli_id' => $dataProvider['num_pedido']]);
                        $produtoFilials = PedidoMercadoLivreProduto::findAll(['pedido_mercado_livre_id' => $pedido->id]);
                        foreach ($produtoFilials as $produtoFilial) {
                            $cotacao = PedidoMercadoLivreProdutoProdutoFilial::findAll(['pedido_mercado_livre_produto_id' => $produtoFilial->id]);
                            foreach ($cotacao as $valorCotacao) {
                                $valor += ($valorCotacao->valor * $valorCotacao->quantidade);
                            }
                        }
                        if ($valor > 0) {
                            return number_format($dataProvider['valor_total'] / $valor, 2);
                        }
                    }
                }
            ],
            [
                'attribute' =>   'transportadora',
                'header' => 'Trasportadora',
            ],
            [
                'attribute' =>   'tipo_frete',
                'header' => 'Frete',
                'content' => function ($dataProvider) {
                    switch ($dataProvider['tipo_frete']) {
                        case 9:
                            return 'Sem Ocorência';
                            break;
                        case 0:
                            return 'Remetente';
                            break;
                        default:
                            return 'Destinatário';
                    }
                }
            ],
            [
                'attribute' =>   'numero_nf',
                'header' => 'NF Venda',
            ],
            [
                'class' => 'yii\grid\CheckboxColumn',
            ],
        ],
        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
        'pjax' => true,
        'responsive' => true,
        'hover' => true,
    ]); ?>

    <?= Html::button('Verificar', [
        'class' => 'btn btn-success',
        'id' => 'btn_validar',
    ]) ?>

    <?php

    $js = <<< JS
    $('#btn_validar').click(function(){
        var keys = $('#gridpedidos').yiiGridView('getSelectedRows');
        $.post({
            url: baseUrl+"/pedidos/verificar-pedido", // your controller action
            data: {keylist:keys},
            success: function(data) {
            if (data.status === 'success') {
                alert('I did it! Processed checked rows.');
            }
        },
    });
});

JS;

    $this->registerJs($js);
    ?>

</div>

<style>
    .container-xxl {
        width: 90%;
        margin: auto;
    }
</style>