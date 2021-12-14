<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LogConsultaExpedicaoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Historico de Consultas Expedicão Estoque';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-consulta-expedicao-index">

    <?php

    use yii\widgets\ListView;

    //print_r($dataProvider); die;

    echo  ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => 'listaProdutosPesquisadosEstoque',

    ]);

    ?>
</div>