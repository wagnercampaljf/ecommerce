<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PedidoMercadoLivreProdutoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pedido Mercado Livre Produtos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-mercado-livre-produto-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Pedido Mercado Livre Produto', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'pedido_mercado_livre_id',
            'produto_filial_id',
            'produto_meli_id',
            'title',
            // 'categoria_meli_id',
            // 'condition',
            // 'quantity',
            // 'unit_price',
            // 'full_unit_price',
            // 'sale_fee',
            // 'listing_type_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
