<?php

use yii\helpers\Html;
use kartik\grid\GridView;


/* @var $this yii\web\View */
/* @var $model common\models\Modelo */

$this->title = 'Pedido Interno';
$this->params['breadcrumbs'][] = ['label' => 'Pedido', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-create">

    <h1><?= Html::encode('Cadastro Pedido Interno') ?></h1>

    <?= $this->render('_form', [
        'modelPedido' => $modelPedido,
        'modelComprador' => $modelComprador,
        'modelEmpresa' => $modelEmpresa,
        'modelEndEmpresa' => $modelEndEmpresa,
        'modelPedProdFilial' => $modelPedProdFilial,
        'modelProdFilial' => $modelProdFilial,
    ]) ?>

    <?php
    if (isset($dataProvider)) {
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'nome',
                'valor',
                'quantidade',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
    }
    ?>

</div>