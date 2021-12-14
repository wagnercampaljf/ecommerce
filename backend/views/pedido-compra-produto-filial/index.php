<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PedidoCompraProdutoFilialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pedido Compra Produto Filials';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-compra-produto-filial-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <p>
        <?= Html::a('Create Pedido Compra Produto Filial', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'quantidade',
            'valor_compra',
            'valor_venda',
            'pedido_compra_id',
            'observacao',
            // 'produto_filial_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>