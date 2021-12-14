<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "pedido_mercado_livre_shipments".
 *
 * @property integer $id
 * @property string $meli_id
 * @property string $mode
 * @property string $created_by
 * @property string $order_id
 * @property string $order_cost
 * @property string $base_cost
 * @property string $site_id
 * @property string $status
 * @property string $substatus
 * @property string $history_date_cancelled
 * @property string $history_date_delivered
 * @property string $history_date_first_visit
 * @property string $history_date_handling
 * @property string $history_date_not_delivered
 * @property string $history_date_ready_to_ship
 * @property string $history_date_shipped
 * @property string $history_date_returned
 * @property string $date_created
 * @property string $last_updated
 * @property string $tracking_number
 * @property string $tracking_method
 * @property string $service_id
 * @property string $sender_id
 * @property string $receiver_id
 * @property string $receiver_address_id
 * @property string $receiver_address_address_line
 * @property string $receiver_address_street_name
 * @property string $receiver_address_street_number
 * @property string $receiver_address_comment
 * @property string $receiver_address_zip_code
 * @property string $receiver_address_city_id
 * @property string $receiver_address_city_name
 * @property string $receiver_address_state_id
 * @property string $receiver_address_state_name
 * @property string $receiver_address_country_id
 * @property string $receiver_address_country_name
 * @property string $receiver_address_neighborhood_id
 * @property string $receiver_address_neighborhood_name
 * @property string $receiver_address_municipality_id
 * @property string $receiver_address_municipality_name
 * @property string $receiver_address_delivery_preference
 * @property string $receiver_address_receiver_name
 * @property string $receiver_address_receiver_phone
 * @property string $shipping_option_id
 * @property string $shipping_option_shipping_method_id
 * @property string $shipping_option_name
 * @property string $shipping_option_currency_id
 * @property string $shipping_option_list_cost
 * @property string $shipping_option_cost
 * @property string $delivery_type
 * @property string $comments
 * @property string $date_first_printed
 * @property string $market_place
 * @property string $type
 * @property string $logistic_type
 * @property string $application_id
 * @property integer $pedido_mercado_livre_id
 *
 * @property PedidoMercadoLivre $pedidoMercadoLivre
 * @property PedidoMercadoLivreShipmentsItem[] $pedidoMercadoLivreShipmentsItems
 *
 * @author Unknown 13/10/2020
 */
class PedidoMercadoLivreShipments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 13/10/2020
     */
    public static function tableName()
    {
        return 'pedido_mercado_livre_shipments';
    }

    /**
     * @inheritdoc
     * @author Unknown 13/10/2020
     */
    public function rules()
    {
        return [
            [['order_cost', 'base_cost', 'shipping_option_list_cost', 'shipping_option_cost'], 'number'],
            [['history_date_cancelled', 'history_date_delivered', 'history_date_first_visit', 'history_date_handling', 'history_date_not_delivered', 'history_date_ready_to_ship', 'history_date_shipped', 'history_date_returned', 'date_created', 'last_updated', 'date_first_printed'], 'safe'],
            [['pedido_mercado_livre_id'], 'integer'],
            [['meli_id', 'mode', 'receiver_address_city_id', 'receiver_address_state_name', 'receiver_address_country_id', 'receiver_address_neighborhood_id', 'receiver_address_municipality_id', 'receiver_address_delivery_preference', 'receiver_address_receiver_phone', 'delivery_type', 'application_id'], 'string', 'max' => 30],
            [['created_by', 'tracking_number', 'receiver_address_country_name'], 'string', 'max' => 40],
            [['order_id', 'tracking_method', 'sender_id', 'receiver_id', 'receiver_address_zip_code', 'shipping_option_id', 'shipping_option_shipping_method_id', 'market_place', 'type', 'logistic_type'], 'string', 'max' => 20],
            [['site_id', 'service_id', 'receiver_address_id', 'receiver_address_state_id', 'shipping_option_currency_id'], 'string', 'max' => 10],
            [['status', 'substatus', 'receiver_address_street_number', 'receiver_address_city_name', 'receiver_address_municipality_name', 'shipping_option_name'], 'string', 'max' => 50],
            [['receiver_address_address_line', 'receiver_address_comment'], 'string', 'max' => 200],
            [['receiver_address_street_name'], 'string', 'max' => 180],
            [['receiver_address_neighborhood_name', 'receiver_address_receiver_name', 'comments'], 'string', 'max' => 100],
            [['pedido_mercado_livre_id'], 'exist', 'skipOnError' => true, 'targetClass' => PedidoMercadoLivre::className(), 'targetAttribute' => ['pedido_mercado_livre_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 13/10/2020
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'meli_id' => 'Meli ID',
            'mode' => 'Mode',
            'created_by' => 'Created By',
            'order_id' => 'Order ID',
            'order_cost' => 'Order Cost',
            'base_cost' => 'Base Cost',
            'site_id' => 'Site ID',
            'status' => 'Status',
            'substatus' => 'Substatus',
            'history_date_cancelled' => 'History Date Cancelled',
            'history_date_delivered' => 'History Date Delivered',
            'history_date_first_visit' => 'History Date First Visit',
            'history_date_handling' => 'History Date Handling',
            'history_date_not_delivered' => 'History Date Not Delivered',
            'history_date_ready_to_ship' => 'History Date Ready To Ship',
            'history_date_shipped' => 'History Date Shipped',
            'history_date_returned' => 'History Date Returned',
            'date_created' => 'Date Created',
            'last_updated' => 'Last Updated',
            'tracking_number' => 'Tracking Number',
            'tracking_method' => 'Tracking Method',
            'service_id' => 'Service ID',
            'sender_id' => 'Sender ID',
            'receiver_id' => 'Receiver ID',
            'receiver_address_id' => 'Receiver Address ID',
            'receiver_address_address_line' => 'Receiver Address Address Line',
            'receiver_address_street_name' => 'Receiver Address Street Name',
            'receiver_address_street_number' => 'Receiver Address Street Number',
            'receiver_address_comment' => 'Receiver Address Comment',
            'receiver_address_zip_code' => 'Receiver Address Zip Code',
            'receiver_address_city_id' => 'Receiver Address City ID',
            'receiver_address_city_name' => 'Receiver Address City Name',
            'receiver_address_state_id' => 'Receiver Address State ID',
            'receiver_address_state_name' => 'Receiver Address State Name',
            'receiver_address_country_id' => 'Receiver Address Country ID',
            'receiver_address_country_name' => 'Receiver Address Country Name',
            'receiver_address_neighborhood_id' => 'Receiver Address Neighborhood ID',
            'receiver_address_neighborhood_name' => 'Receiver Address Neighborhood Name',
            'receiver_address_municipality_id' => 'Receiver Address Municipality ID',
            'receiver_address_municipality_name' => 'Receiver Address Municipality Name',
            'receiver_address_delivery_preference' => 'Receiver Address Delivery Preference',
            'receiver_address_receiver_name' => 'Receiver Address Receiver Name',
            'receiver_address_receiver_phone' => 'Receiver Address Receiver Phone',
            'shipping_option_id' => 'Shipping Option ID',
            'shipping_option_shipping_method_id' => 'Shipping Option Shipping Method ID',
            'shipping_option_name' => 'Shipping Option Name',
            'shipping_option_currency_id' => 'Shipping Option Currency ID',
            'shipping_option_list_cost' => 'Shipping Option List Cost',
            'shipping_option_cost' => 'Shipping Option Cost',
            'delivery_type' => 'Delivery Type',
            'comments' => 'Comments',
            'date_first_printed' => 'Date First Printed',
            'market_place' => 'Market Place',
            'type' => 'Type',
            'logistic_type' => 'Logistic Type',
            'application_id' => 'Application ID',
            'pedido_mercado_livre_id' => 'Pedido Mercado Livre ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 13/10/2020
    */
    public function getPedidoMercadoLivre()
    {
        return $this->hasOne(PedidoMercadoLivre::className(), ['id' => 'pedido_mercado_livre_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 13/10/2020
    */
    public function getPedidoMercadoLivreShipmentsItems()
    {
        return $this->hasMany(PedidoMercadoLivreShipmentsItem::className(), ['pedido_mercado_livre_shipments_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 13/10/2020
    */
    public static function find()
    {
        return new PedidoMercadoLivreShipmentsQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da PedidoMercadoLivreShipments, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 13/10/2020
*/
class PedidoMercadoLivreShipmentsQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 13/10/2020
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido_mercado_livre_shipments.nome' => $sort_type]);
    }
}
