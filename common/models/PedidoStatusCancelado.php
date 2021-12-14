<?php

namespace common\models;

use Yii;

class PedidoStatusCancelado implements PedidoStatus
{
    public $id = 5;

    public function getStatus()
    {
        return "Cancelado";
    }

    public static function getLabel()
    {
        return "Cancelado";
    }


    public function mudarStatus($status)
    {
        $class = Pedido::$statusClasses[$status];
        return new $class();
    }

    public static function isNext($idStatusAtual)
    {
        return false;
    }

    public static function isCompleted($idStatusAtual)
    {
        return false;
    }
}