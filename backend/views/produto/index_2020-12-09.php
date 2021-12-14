<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProdutoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Produtos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Cadastrar Produto', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php 
    	if($erro!=""){
    	    echo '<p><font color="red">'.$erro.'</font></p>';
    	}


    ?>

    <?=



    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],




            'id',
            ['attribute' => 'nome',
                'header' => '         NOME DO PRODUTO',

            ],



            ['attribute' => 'codigo_global',
                'header' => 'COD GLOBAL',

            ],

            ['attribute' =>   'codigo_fabricante',
                'header' => 'COD. FABRICANTE',

            ],

            ['attribute' =>   'codigo_fornecedor',
                'header' => 'COD. FORNECEDOR',

            ],



            ['attribute' =>  'peso',
                'header' => 'PESO',

            ],




            ['attribute' => 'altura',
                'header' => 'ALT.',

            ],




            ['attribute' => 'largura',
                'header' => 'LARG.',

            ],



            ['attribute' => 'profundidade',
                'header' => 'COMPR.',

            ],

            ['attribute' => 'e_medidas_conferidas',
                'header' => 'Medidas conferidas',
                'content' => function($dataProvider){
                    if ($dataProvider->e_medidas_conferidas == true)
                        return "✔" ;

                }


            ],

            ['attribute' => 'e_ativo',
                'header' => 'É ativo',
                'content' => function($dataProvider){
                    if ($dataProvider->e_ativo == true)
                        return "Sim" ;

                }



            ],




            ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'min-width: 70px;']],
        ],

    ]);


    ?>

</div>
