<?php

namespace common\models;

use Yii;

class PedidoStatusAberto implements PedidoStatus
{
    public $id = 1;

    public function getStatus()
    {
        return "Em Aberto";
    }

    public static function getLabel()
    {
        return "Em Aberto";
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
        return true;
    }

}