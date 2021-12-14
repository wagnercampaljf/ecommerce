<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "pedido_mercado_livre".
 *
 * @property integer $id
 * @property string $pedido_meli_id
 * @property string $date_created
 * @property string $date_closed
 * @property string $last_updated
 * @property string $total_amount
 * @property string $paid_amount
 * @property string $shipping_id
 * @property string $status
 * @property string $buyer_id
 * @property string $buyer_nickname
 * @property string $buyer_email
 * @property string $buyer_first_name
 * @property string $buyer_last_name
 * @property string $buyer_doc_type
 * @property string $buyer_doc_number
 * @property string $shipping_base_cost
 * @property string $shipping_status
 * @property string $shipping_substatus
 * @property string $shipping_date_created
 * @property string $shipping_last_updated
 * @property string $shipping_tracking_number
 * @property string $shipping_tracking_method
 * @property string $shipping_service_id
 * @property string $receiver_id
 * @property string $receiver_address_id
 * @property string $receiver_address_line
 * @property string $receiver_street_name
 * @property string $receiver_street_number
 * @property string $receiver_comment
 * @property string $receiver_zip_code
 * @property string $receiver_city_id
 * @property string $receiver_city_name
 * @property string $receiver_state_id
 * @property string $receiver_state_name
 * @property string $receiver_country_id
 * @property string $receiver_country_name
 * @property string $receiver_neighborhood_id
 * @property string $receiver_neighborhood_name
 * @property string $receiver_municipality_id
 * @property string $receiver_municipality_name
 * @property string $receiver_delivery_preference
 * @property string $receiver_name
 * @property string $receiver_phone
 * @property string $shipping_option_id
 * @property string $shipping_option_shipping_method_id
 * @property string $shipping_option_name
 * @property string $shipping_option_list_cost
 * @property string $shipping_option_cost
 * @property string $shipping_option_delivery_type
 * @property string $user_id
 * @property boolean $e_pedido_autorizado
 * @property boolean $e_nota_fiscal_anexada
 * @property boolean $e_pedido_faturado
 * @property string $email_texto
 * @property string $email_enderecos
 * @property string $comentario
 * @property string $email_assunto
 *
 * @property PedidoMercadoLivrePagamento[] $pedidoMercadoLivrePagamentos
 * @property PedidoMercadoLivreProduto[] $pedidoMercadoLivreProdutos
 *
 * @author Unknown 15/06/2020
 */
class PedidoMercadoLivre extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 15/06/2020
     */
    public static function tableName()
    {
        return 'pedido_mercado_livre';
    }

    /**
     * @inheritdoc
     * @author Unknown 15/06/2020
     */
    public function rules()
    {
        return [
            [['pedido_meli_id', 'total_amount'], 'required'],
            [['date_created', 'date_closed', 'last_updated', 'shipping_date_created', 'shipping_last_updated'], 'safe'],
            [['total_amount', 'paid_amount', 'shipping_base_cost', 'shipping_option_list_cost', 'shipping_option_cost'], 'number'],
            [['e_pedido_autorizado', 'e_nota_fiscal_anexada', 'e_pedido_faturado'], 'boolean'],
            [['email_texto', 'email_assunto'], 'string'],
            [['pedido_meli_id', 'shipping_id', 'status', 'buyer_id', 'buyer_doc_type', 'shipping_service_id', 'receiver_id', 'receiver_address_id', 'receiver_street_number', 'receiver_zip_code', 'receiver_country_id', 'receiver_neighborhood_id', 'receiver_municipality_id', 'shipping_option_id', 'shipping_option_name', 'shipping_option_delivery_type', 'user_id'], 'string', 'max' => 20],
            [['buyer_nickname', 'buyer_email', 'buyer_first_name', 'buyer_last_name', 'receiver_address_line', 'receiver_street_name'], 'string', 'max' => 200],
            [['buyer_doc_number', 'shipping_tracking_number', 'receiver_state_name', 'receiver_country_name'], 'string', 'max' => 30],
            [['shipping_status'], 'string', 'max' => 40],
            [['shipping_substatus', 'receiver_comment', 'receiver_neighborhood_name'], 'string', 'max' => 100],
            [['shipping_tracking_method', 'receiver_city_id', 'receiver_state_id', 'receiver_municipality_name', 'receiver_delivery_preference'], 'string', 'max' => 50],
            [['receiver_city_name', 'receiver_name'], 'string', 'max' => 60],
            [['receiver_phone'], 'string', 'max' => 15],
            [['shipping_option_shipping_method_id'], 'string', 'max' => 10],
            [['email_enderecos'], 'string', 'max' => 400]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 15/06/2020
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pedido_meli_id' => 'Pedido Meli ID',
            'date_created' => 'Date Created',
            'date_closed' => 'Date Closed',
            'last_updated' => 'Last Updated',
            'total_amount' => 'Total Amount',
            'paid_amount' => 'Paid Amount',
            'shipping_id' => 'Shipping ID',
            'status' => 'Status',
            'buyer_id' => 'Buyer ID',
            'buyer_nickname' => 'Buyer Nickname',
            'buyer_email' => 'Buyer Email',
            'buyer_first_name' => 'Buyer First Name',
            'buyer_last_name' => 'Buyer Last Name',
            'buyer_doc_type' => 'Buyer Doc Type',
            'buyer_doc_number' => 'Buyer Doc Number',
            'shipping_base_cost' => 'Shipping Base Cost',
            'shipping_status' => 'Shipping Status',
            'shipping_substatus' => 'Shipping Substatus',
            'shipping_date_created' => 'Shipping Date Created',
            'shipping_last_updated' => 'Shipping Last Updated',
            'shipping_tracking_number' => 'Shipping Tracking Number',
            'shipping_tracking_method' => 'Shipping Tracking Method',
            'shipping_service_id' => 'Shipping Service ID',
            'receiver_id' => 'Receiver ID',
            'receiver_address_id' => 'Receiver Address ID',
            'receiver_address_line' => 'Receiver Address Line',
            'receiver_street_name' => 'Receiver Street Name',
            'receiver_street_number' => 'Receiver Street Number',
            'receiver_comment' => 'Receiver Comment',
            'receiver_zip_code' => 'Receiver Zip Code',
            'receiver_city_id' => 'Receiver City ID',
            'receiver_city_name' => 'Receiver City Name',
            'receiver_state_id' => 'Receiver State ID',
            'receiver_state_name' => 'Receiver State Name',
            'receiver_country_id' => 'Receiver Country ID',
            'receiver_country_name' => 'Receiver Country Name',
            'receiver_neighborhood_id' => 'Receiver Neighborhood ID',
            'receiver_neighborhood_name' => 'Receiver Neighborhood Name',
            'receiver_municipality_id' => 'Receiver Municipality ID',
            'receiver_municipality_name' => 'Receiver Municipality Name',
            'receiver_delivery_preference' => 'Receiver Delivery Preference',
            'receiver_name' => 'Receiver Name',
            'receiver_phone' => 'Receiver Phone',
            'shipping_option_id' => 'Shipping Option ID',
            'shipping_option_shipping_method_id' => 'Shipping Option Shipping Method ID',
            'shipping_option_name' => 'Shipping Option Name',
            'shipping_option_list_cost' => 'Shipping Option List Cost',
            'shipping_option_cost' => 'Shipping Option Cost',
            'shipping_option_delivery_type' => 'Shipping Option Delivery Type',
            'user_id' => 'User ID',
            'e_pedido_autorizado' => 'E Pedido Autorizado',
            'e_nota_fiscal_anexada' => 'E Nota Fiscal Anexada',
            'e_pedido_faturado' => 'E Pedido Faturado',
            'email_texto' => 'Email Texto',
            'email_enderecos' => 'Email Enderecos',
            'email_assunto' => 'Email Assunto'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/06/2020
    */
    public function getPedidoMercadoLivrePagamentos()
    {
        return $this->hasMany(PedidoMercadoLivrePagamento::className(), ['pedido_mercado_livre_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/06/2020
    */
    public function getPedidoMercadoLivreProdutos()
    {
        return $this->hasMany(PedidoMercadoLivreProduto::className(), ['pedido_mercado_livre_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/06/2020
    */
    public static function find()
    {
        return new PedidoMercadoLivreQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da PedidoMercadoLivre, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 15/06/2020
*/
class PedidoMercadoLivreQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/06/2020
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido_mercado_livre.nome' => $sort_type]);
    }
}
