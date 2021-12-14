<?php

namespace common\models;

use Yii;

interface PedidoStatus
{
    /**
     * Retorna status
     *
     * @return mixed
     * @author Vitor Horta 26/03/2015
     * @since 0.1
     */
    public function getStatus();

    /**
     * Retorna label do status
     * @return mixed
     * @author Vitor Horta 26/03/2015
     * @since 0.1
     */
    public static function getLabel();

    /**
     * Retorna novo status do pedido
     *
     * @param $status
     * @return mixed
     * @author Vitor Horta 26/03/2015
     * @since 0.1
     */
    public function mudarStatus($status);

    /**
     * Retorna true se o status chamado é o próximo a ser ativado
     *
     * @param $idStatusAtual
     * @return mixed
     * @author Vitor Horta 26/03/2015
     * @since 0.1
     */
    public static function isNext($idStatusAtual);

    /**
     * Retorna true se o status já foi completado
     *
     * @param $idStatusAtual
     * @return mixed
     * @author Vitor Horta 26/03/2015
     * @since 0.1
     */
    public static function isCompleted($idStatusAtual);
}