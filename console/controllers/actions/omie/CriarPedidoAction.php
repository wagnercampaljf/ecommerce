<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use common\models\PedidoMercadoLivre;

class CriarPedidoAction extends Action
{
    public function run($pedido_meli_id)
    {
        
        echo "Criar Pedido - ".$pedido_meli_id."\n\n";

	echo PedidoMercadoLivre::baixarPedidoML($pedido_meli_id);

    }
}



