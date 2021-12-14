<?php

namespace common\models;

use Yii;

class PedidoStatusEnviado implements PedidoStatus
{
    public $id = 3;

    public function getStatus()
    {
        return "Enviado";
    }

    public static function getLabel()
    {
        return "Enviado";
    }

    public function mudarStatus($status)
    {
        $class = Pedido::$statusClasses[$status];
        return new $class();
    }

    public static function isNext($idStatusAtual)
    {
        if ($idStatusAtual == 2)
            return true;

        return false;
    }

    public static function isCompleted($idStatusAtual)
    {
        if ($idStatusAtual >= 3)
            return true;
    }
}