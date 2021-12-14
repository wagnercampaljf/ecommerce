<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ValorProdutoFilialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Valor Produto Filials';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="valor-produto-filial-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Criar Valor Produto Filial', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
	<?php //print_r($dataProvider);die;?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
	    [
                'label'     => 'Código Global',
                'attribute' => 'codigo_global',
                'value'     => 'produtoFilial.produto.codigo_global',
            ],
	    [
                'label'     => 'Código Fabricante',
                'attribute' => 'codigo_fabricante',
                'value'     => 'produtoFilial.produto.codigo_fabricante',
            ],
            [
                'label'     => 'Filial',
                'attribute' => 'filial_nome',
                'value'     => 'produtoFilial.filial.nome' ,
            ],
            [
                'label'     => 'Produto',
                'attribute' => 'produto_nome',
                'value'     => 'produtoFilial.produto.nome'
            ],
	    'valor:currency',
[
                'label'     => 'Valor ML',
                'attribute' => 'valor_mercado_livre',
                'value'     => function($model){
                                                    $valorml = $model->valor * 1.06;

                                                    if($valorml>=500){
                                                        return $valorml + 10;
                                                    } elseif($valorml <= 109){
                                                        return $valorml + 5;
                                                    } elseif($valorml > 109 and $valorml < 500){
                                                        return $valorml + 16;
                                                    }
                                               },
		'format'    => 'currency',
            ],
            [
                'label'     => 'Valor B2W',
                'attribute' => 'valor_b2W',
                'value'     => function($model){ return $model->valor * 1.2;},
		'format'    => 'currency',
            ],
            //'valor_compra:currency',
	    [
                'label'     => 'Valor Compra',
                'attribute' => 'valor_compra',
                'value'     => function($model){
                                    
                                    $valor_compra   = $model->valor_compra;
                                    $ipi            = 0;
                                    $cest           = $valor_compra * 0.144;                       
                                    if($model->produtoFilial->produto->ipi > 0){
                                        $ipi = $model->produtoFilial->produto->ipi/100 * $valor_compra;
                                    }
                                    $valor_compra   += ($ipi + $cest); 
                                    
                                    return $valor_compra;
                               },
                'format'    => 'currency',
            ],
	    [
                'label'     => 'Quantidade',
                'attribute' => 'quantidade',
                'value'     => 'produtoFilial.quantidade',
            ],
	    'dt_inicio',
            /*[
                'attribute' => 'valorProdutoFilial.produtoFilial.produto.nome',
                'label'     => 'Produto/Filial',
                'value'     =>  function ($data) {
                return $data->produtoFilial->filial->nome . " => " . $data->produtoFilial->produto->nome;//'produtoFilial.produto.nome' ,
                },
            ],*/

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
