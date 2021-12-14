<?php

namespace common\models;

use Yii;

class PedidoStatusConfirmado implements PedidoStatus
{
    public $id = 2;

    public function getStatus()
    {
        return "Pagamento Confirmado";
    }

    public static function getLabel()
    {
        return "Pagamento Confirmado";
    }


    public function mudarStatus($status)
    {
        $class = Pedido::$statusClasses[$status];
        return new $class();
    }

    public static function isNext($idStatusAtual)
    {
        if ($idStatusAtual == 1)
            return true;

        return false;
    }

    public static function isCompleted($idStatusAtual)
    {
        if ($idStatusAtual >= 2)
            return true;
    }
}