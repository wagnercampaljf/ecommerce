
<?php

use common\models\PedidoSearch;
use yii\widgets\ListView;
use yii\helpers\Url;

\yii\widgets\Pjax::begin(['timeout' => 5000]);


?>

<div class="container">
    <div class="row">
        <div class="col-sm-13">
            <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos']) ?>">
                <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <input type="text"
                           name="filtro"
                           id="main-search-product"
                           class="form-control form-control-search input-lg data-hj-whitelist"
                           placeholder="Procure por dados do pedido ..."
                           value="<?= $filtro?>">
                    <span class="input-group-btn">
                    	<button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
                    </span>
                </div>
            </form>
        </div>
    </div><br>
</div>


<?php

$searchModel = new PedidoSearch();

$dataProvider = $searchModel->search(['PedidoSearch'=> ['id' => $filtro]]);
$dataProvider->pagination = ['pageSize' => 6,];

echo  ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => 'listapedidos',
]);

\yii\widgets\Pjax::end();

?>





