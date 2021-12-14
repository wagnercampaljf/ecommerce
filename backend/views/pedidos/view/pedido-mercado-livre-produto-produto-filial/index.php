<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PedidoMercadoLivreProdutoProdutoFilialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pedido Mercado Livre Produto Produto Filials';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-mercado-livre-produto-produto-filial-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Pedido Mercado Livre Produto Produto Filial', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'pedido_mercado_livre_produto_id',
            'produto_filial_id',
            'quantidade',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
