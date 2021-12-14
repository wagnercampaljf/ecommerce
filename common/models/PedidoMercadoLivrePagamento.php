<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "pedido_mercado_livre_pagamento".
 *
 * @property integer $id
 * @property integer $pedido_mercado_livre_id
 * @property string $pagamento_meli_id
 * @property string $payer_id
 * @property string $card_id
 * @property string $payment_method_id
 * @property string $operation_type
 * @property string $payment_type
 * @property string $status
 * @property string $status_detail
 * @property string $transaction_amount
 * @property string $taxes_amount
 * @property string $shipping_cost
 * @property string $coupon_amount
 * @property string $overpaid_amount
 * @property string $total_paid_amount
 * @property string $installment_amount
 * @property string $date_approved
 * @property string $authorization_code
 * @property string $date_created
 * @property string $date_last_modified
 *
 * @property PedidoMercadoLivre $pedidoMercadoLivre
 *
 * @author Unknown 08/06/2020
 */
class PedidoMercadoLivrePagamento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 08/06/2020
     */
    public static function tableName()
    {
        return 'pedido_mercado_livre_pagamento';
    }

    /**
     * @inheritdoc
     * @author Unknown 08/06/2020
     */
    public function rules()
    {
        return [
            [['pedido_mercado_livre_id'], 'integer'],
            [['transaction_amount', 'taxes_amount', 'shipping_cost', 'coupon_amount', 'overpaid_amount', 'total_paid_amount', 'installment_amount'], 'number'],
            [['date_approved', 'date_created', 'date_last_modified'], 'safe'],
            [['pagamento_meli_id', 'payer_id', 'card_id', 'payment_method_id', 'authorization_code'], 'string', 'max' => 20],
            [['operation_type', 'payment_type', 'status', 'status_detail'], 'string', 'max' => 30],
            [['pedido_mercado_livre_id'], 'exist', 'skipOnError' => true, 'targetClass' => PedidoMercadoLivre::className(), 'targetAttribute' => ['pedido_mercado_livre_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 08/06/2020
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pedido_mercado_livre_id' => 'Pedido Mercado Livre ID',
            'pagamento_meli_id' => 'Pagamento Meli ID',
            'payer_id' => 'Payer ID',
            'card_id' => 'Card ID',
            'payment_method_id' => 'Payment Method ID',
            'operation_type' => 'Operation Type',
            'payment_type' => 'Payment Type',
            'status' => 'Status',
            'status_detail' => 'Status Detail',
            'transaction_amount' => 'Transaction Amount',
            'taxes_amount' => 'Taxes Amount',
            'shipping_cost' => 'Shipping Cost',
            'coupon_amount' => 'Coupon Amount',
            'overpaid_amount' => 'Overpaid Amount',
            'total_paid_amount' => 'Total Paid Amount',
            'installment_amount' => 'Installment Amount',
            'date_approved' => 'Date Approved',
            'authorization_code' => 'Authorization Code',
            'date_created' => 'Date Created',
            'date_last_modified' => 'Date Last Modified',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/06/2020
    */
    public function getPedidoMercadoLivre()
    {
        return $this->hasOne(PedidoMercadoLivre::className(), ['id' => 'pedido_mercado_livre_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/06/2020
    */
    public static function find()
    {
        return new PedidoMercadoLivrePagamentoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da PedidoMercadoLivrePagamento, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 08/06/2020
*/
class PedidoMercadoLivrePagamentoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/06/2020
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido_mercado_livre_pagamento.nome' => $sort_type]);
    }
}
