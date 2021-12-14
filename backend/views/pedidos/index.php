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
        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <div class="container">

                    <?php echo $this->render('index/_pedidos-site', ["filtro" => $filtro, "filtro_status" => $filtro_status]) ?>
                </div>
            </div>
        </div>
    </div>
</div>