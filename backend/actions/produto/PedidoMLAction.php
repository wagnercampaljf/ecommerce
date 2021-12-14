<?php

namespace backend\actions\produto;

use Yii;
use yii\base\Action;
use common\models\MercadoLivreOrder;

class PedidoMLAction extends Action
{

    public function run()
    {
       
        $arrayOrder =   //Yii::$app->request->post();
                        [   "user_id"           => 1111,
                            "resource"          => "/orders/731867397",
                            "topic"             => "orders",
                            "received"          => "2018-09-17",
                            "application_id"    => 14529,
                            "sent"              => "2018-09-18",
                            "attempts"          => 23
                        ];
        //var_dump($arrayOrder); echo "<br><br><br>"; die;
        
        $orderML = new MercadoLivreOrder();
        $orderML->user_id           = $arrayOrder["user_id"];
        $orderML->resource          = $arrayOrder["resource"];
        $orderML->topic	            = $arrayOrder["topic"];
        $orderML->received	        = $arrayOrder["received"];
        $orderML->application_id    = $arrayOrder["application_id"];
        $orderML->sent 		        = $arrayOrder["sent"];
        $orderML->attempts          = $arrayOrder["attempts"];
        $orderML->save();

    }

}
