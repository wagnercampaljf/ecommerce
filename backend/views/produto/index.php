<?php

use yii\helpers\Html;
use yii\grid\GridView;

// echo '<pre>'; print_r($dataProvider); echo '</pre>'; die;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProdutoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Produtos';
$this->params['breadcrumbs'][] = $this->title;
// echo '<pre>'; var_dump($dataProvider); echo '</pre>'; die;
?>
<div class="produto-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Cadastrar Produto', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    if ($erro != "") {
        echo '<p><font color="red">' . $erro . '</font></p>';
    }
    ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'header' => 'ID - PA',
                'contentOptions' => ['style' => 'min-width: 100px;']
            ],
            [
                'attribute' => 'nome',
                'header' => '         NOME DO PRODUTO',
                'contentOptions' => ['style' => 'min-width: 240px;']

            ],
            [
                'attribute' => 'codigo_global',
                'header' => 'COD GLO',

            ],
            [
                'attribute' =>   'codigo_fabricante',
                'header' => 'COD FAB',

            ],
            [
                'attribute' =>   'codigo_montadora',
                'header' => 'NCM',

            ],
            [
                'attribute' =>   'pis_cofins',
                'header' => 'PIS/COFINS',
            ],
            [
                'attribute' =>  'peso',
                'header' => 'PESO',

            ],
            [
                'attribute' => 'altura',
                'header' => 'ALT.',

            ],
            [
                'attribute' => 'largura',
                'header' => 'LARG.',

            ],
            [
                'attribute' => 'profundidade',
                'header' => 'COMPR.',

            ],
            [
                'attribute' => 'marca_produto_id',
                'header' => 'MARCA',
                'content' => function ($dataProvider) {

                    if ($dataProvider->marca_produto_id != null)
                        return $dataProvider->marcaProduto->nome;
                }
            ],
            [
                'attribute' => 'e_medidas_conferidas',
                'header' => 'MED CONF',
                'content' => function ($dataProvider) {
                    if ($dataProvider->e_medidas_conferidas == true)
                        return "✔✔✔✔";
                },
            ],

            [
                'attribute' => 'e_ativo',
                'header' => 'É ativo',
                'content' => function ($dataProvider) {
                    if ($dataProvider->e_ativo == true)
                        return "Sim";
                    elseif ($dataProvider->e_ativo == false)
                        return "Não";
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'min-width: 70px;']
            ],
        ],

    ]);


    ?>

</div>

<style>
    .container-xxl {
        width: 97%;
        margin: auto;
    }
</style>
