
<?php


    use backend\models\PedidoMercadoLivreSearch;
    use yii\widgets\ListView;

    \yii\widgets\Pjax::begin(['timeout' => 5000]);

    $searchModel = new PedidoMercadoLivreSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);



    echo  ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => 'listaPedidoMercadoLivre',
    ]);



    \yii\widgets\Pjax::end();

?>


