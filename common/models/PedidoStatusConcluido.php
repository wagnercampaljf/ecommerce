<?php

namespace common\models;

use Yii;

class PedidoStatusConcluido implements PedidoStatus
{
    public $id = 4;

    public function getStatus()
    {
        return "Concluído";
    }

    public static function getLabel()
    {
        return "Concluído";
    }


    public function mudarStatus($status)
    {
        $class = Pedido::$statusClasses[$status];
        return new $class();
    }

    public static function isNext($idStatusAtual)
    {
        if ($idStatusAtual == 3)
            return true;

        return false;
    }

    public static function isCompleted($idStatusAtual)
    {
        if ($idStatusAtual >= 4)
            return true;
    }
}