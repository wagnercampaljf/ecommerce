<?php

    use yii\widgets\ListView;

    //print_r($dataProvider); die;
    
    echo  ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => 'listaProdutos',
    ]);

?>