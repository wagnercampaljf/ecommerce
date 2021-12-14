<?php

namespace lojista\models;


/**
 * Class PedidoProdutoFilial
 * @package lojista\models
 */
class PedidoProdutoFilial extends \common\models\PedidoProdutoFilial
{
    public function getPedido()
    {
        return $this->hasOne(Pedido::className(), ['id' => 'pedido_id']);
    }
}