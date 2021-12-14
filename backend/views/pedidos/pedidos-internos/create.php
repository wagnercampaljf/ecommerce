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

    <?php
        if (isset($mensagem)) {
            echo '<div class="text-success h3" style="font-size: 20px; color: #1E90FF"><b>' . $mensagem . '</b></div>';
        }
    ?>

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

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update}{delete}',
                    'buttons' => [
                        'update' => function ($url, $dataProvider) {
                            $url = "pedido-update?id=" . $dataProvider['pedido_id'] . '&id_produto=' . $dataProvider['pedido_id'];
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>  ', $url, ['title' => Yii::t('yii', 'Alterar'),]);
                        },
                        'delete' => function ($url, $dataProvider) {
                            $url = "pedido-delete?id=" . $dataProvider['pedido_id'];
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('yii', 'Excluir'), 'data-confirm' => 'Confirma a exclusão deste item?', 'data-method' => 'post']);
                        },
                    ],
                ],
            ],
        ]);
    }
    ?>

</div>