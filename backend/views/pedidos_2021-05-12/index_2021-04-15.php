<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\PedidoMercadoLivreSearch;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $skyhubDataProvider yii\data\ActiveDataProvider */
/* @var $filterModel \common\models\PedidoSearch */
/* @var $skyhubFilterModel \common\models\PedidoSkyhubSearch */

$this->title = 'Pedidos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-index">



</div>

<div class="pedido-index">

    <div class="container">
        <h2>Dynamic Tabs</h2>
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home">Peça Agora</a></li>
        </ul>

        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <div class="container">

                    <?php  echo $this->render('index/_pedidos-site', [ "filtro" => $filtro ]) ?>
                </div>
            </div>


        </div>
    </div>




</div>

