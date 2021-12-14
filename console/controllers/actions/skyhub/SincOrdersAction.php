<?php
/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 21/09/17
 * Time: 17:30
 */

namespace console\controllers\actions\skyhub;


use common\models\PedidoSkyhub;
use console\models\SkyhubClient;
use yii\base\Action;

class SincOrdersAction extends Action
{
    public function run()
    {
        echo "Sincronizar pedidos\n";
        $skyhub = new SkyhubClient();
        $pedido = $skyhub->queues()->findAll();
        do {
            if (!empty($pedido) && PedidoSkyhub::createFromSkyhubData($pedido)) {
                $skyhub->queues()->remove($pedido['code']);
            }
            $pedido = $skyhub->queues()->findAll();
        } while (!empty($pedido));
        echo "Sincronização concluída";
    }
}