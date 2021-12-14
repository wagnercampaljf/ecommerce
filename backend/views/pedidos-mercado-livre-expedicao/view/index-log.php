<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\PedidoMercadoLivreSearch;
use backend\models\Administrador;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $skyhubDataProvider yii\data\ActiveDataProvider */
/* @var $filterModel \common\models\PedidoSearch */
/* @var $skyhubFilterModel \common\models\PedidoSkyhubSearch */

$this->title = 'Pedidos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-index">
	<h1><?= Html::encode($this->title) ?></h1>

    <?php 
    
    \yii\widgets\Pjax::begin(['timeout' => 5000]);
    
    echo GridView::widget([
        'dataProvider' => $dataProviderLog,
        'filterModel' => $searchModelLog,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'descricao',
            'salvo_em',
            //"salvo_por",
            [
                    'label'     => 'Usuário',
                    //'attribute' => 'salvo_por',
                    'value'     => function($model) {
                        $administrador = Administrador::find()->andwhere(["=", "id", $model->salvo_por])->one();
                        if($administrador){
                            return $administrador->nome;
                        }
                        else{
                            return "Sem usuário";
                        }
                    },
            ],
        ]
    ]); 
    
    \yii\widgets\Pjax::end();
    
    ?>

</div>













