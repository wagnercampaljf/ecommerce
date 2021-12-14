<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\PedidoMercadoLivreSearch;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $skyhubDataProvider yii\data\ActiveDataProvider */
/* @var $filterModel \common\models\PedidoSearch */
/* @var $skyhubFilterModel \common\models\PedidoSkyhubSearch */

$this->title = 'Pedidos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-index">

    <div class="container">
      
		<?php  echo $this->render('index/_mercado-livre-principal', [
		        "filtro" => $filtro,
            "filtro_status_cancelado" => $filtro_status_cancelado,
            "filtro_status_pedido_faturado" => $filtro_status_pedido_faturado,
            "filtro_status_pedido_envido" => $filtro_status_pedido_envido,

        ]) ?>


    </div>
      
    </div>




</div>
