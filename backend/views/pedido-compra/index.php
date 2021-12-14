<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Filial;
use common\models\Fornecedor;
use yii\helpers\Url;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PedidoCompraSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if (isset($mensagem)) {
    Alert::begin([
        'options' => [
            'class' => 'alert alert-warning d-flex align-items-center',
        ],
    ]);

    echo $mensagem;

    Alert::end();
}

$this->title = 'Pedidos de Compras';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-compra-index">

    <div class='row'>
        <div class="col-md-5 ">
            <p>
                <?= Html::a('Novo Pedido de Compra', ['create'], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
        <div class="col-md-7 ">
            <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedido-compra/criar-pedido-nota']) ?>">
                <div class="input-group col-sm-6 ">
                    <input type="text" name="cChaveNFe" id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist" placeholder="Chave de Acesso Nota ..." value="">
                </div>
                <div class="input-group col-sm-4 ">
                    <input type="text" name="perc_nota" id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist" placeholder="Porcentagem Nota" value="">
                </div>
                <div class="input-group col-sm-1 ">
                    <span class="input-group-btn">
                        <button type="submit" name="enviar-formulario" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><span class="glyphicon glyphicon-download-alt"></span></button>
                    </span>
                </div>
            </form>
        </div>

    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'descricao',
            [
                'attribute' =>   'data',
                'header' => 'Data',
                'content' => function ($dataProvider) {
                    substr('abcdef', 0, 4);
                    $ano = substr($dataProvider->data, 0, 4);
                    $mes = substr($dataProvider->data, 5, 2);
                    $dia = substr($dataProvider->data, 8, 2);
                    return $dia . '/' . $mes . '/' . $ano;
                },
            ],

            'observacao',

            [
                'attribute' =>   '',
                'header' => 'Fornecedor',
                'content' => function ($dataProvider) {
                    $fornecedor = Fornecedor::find()->andWhere(['=', 'id', $dataProvider->fornecedor_id])->one();
                    return $fornecedor['nome'];
                }
            ],
            [
                'attribute' =>   '',
                'header' => 'Filial',
                'content' => function ($dataProvider) {
                    $filial = Filial::find()->andWhere(['=', 'id', $dataProvider->filial_id])->one();
                    return $filial['nome'];
                }
            ],
            [
                'attribute' =>   'status',
                'header' => 'Status',
                'content' => function ($dataProvider) {
                    $status = '';
                    switch ($dataProvider->status) {
                        case 1:
                            $status = 'Aberto';
                            break;
                        case 2:
                            $status = 'Enviado';
                            break;
                        case 3:
                            $status = 'Recebido/Conferido';
                            break;
                    }

                    return $status;
                },
            ],
            'valor_total_pedido',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}{delete}',
                'buttons' => [
                    'update' => function ($url, $dataProvider) {
                        $url = "../pedido-compra-produto-filial/update?id=" . $dataProvider->id;
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>  ', $url, ['title' => Yii::t('yii', 'Alterar'),]);
                    },
                    'delete' => function ($url, $dataProvider) {
                        $url = "delete?id=" . $dataProvider->id;
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('yii', 'Excluir'), 'data-confirm' => 'Confirma a exclusão deste item?', 'data-method' => 'post']);
                    },
                ],
            ],
        ],
    ]); ?>

</div>