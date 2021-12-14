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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nome',
//            'descricao:ntext',
//            'peso',
//            'altura',
            // 'largura',
            // 'profundidade',
            // 'imagem',
            'codigo_global',
            // 'codigo_montadora',
            // 'codigo_fabricante',
            // 'fabricante_id',
            // 'slug',
            // 'micro_descricao',
            // 'subcategoria_id',
            // 'aplicacao:ntext',
            // 'texto_vetor',
	    'peso',
            'altura',
            'largura',
            'profundidade',

            ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'min-width: 70px;']],
        ],
    ]); ?>

</div>
