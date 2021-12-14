<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "pedido_mercado_livre_shipments_item".
 *
 * @property integer $id
 * @property integer $pedido_mercado_livre_shipments_id
 * @property string $meli_id
 * @property string $description
 * @property integer $quantity
 * @property string $dimensions
 * @property string $dimensions_source_id
 * @property string $dimensions_source_origin
 *
 * @property PedidoMercadoLivreShipments $pedidoMercadoLivreShipments
 *
 * @author Unknown 13/10/2020
 */
class PedidoMercadoLivreShipmentsItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 13/10/2020
     */
    public static function tableName()
    {
        return 'pedido_mercado_livre_shipments_item';
    }

    /**
     * @inheritdoc
     * @author Unknown 13/10/2020
     */
    public function rules()
    {
        return [
            [['pedido_mercado_livre_shipments_id', 'quantity'], 'integer'],
            [['meli_id', 'dimensions', 'dimensions_source_id'], 'string', 'max' => 30],
            [['description'], 'string', 'max' => 100],
            [['dimensions_source_origin'], 'string', 'max' => 50],
            [['pedido_mercado_livre_shipments_id'], 'exist', 'skipOnError' => true, 'targetClass' => PedidoMercadoLivreShipments::className(), 'targetAttribute' => ['pedido_mercado_livre_shipments_id' => 'id']]
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
            'pedido_mercado_livre_shipments_id' => 'Pedido Mercado Livre Shipments ID',
            'meli_id' => 'Meli ID',
            'description' => 'Description',
            'quantity' => 'Quantity',
            'dimensions' => 'Dimensions',
            'dimensions_source_id' => 'Dimensions Source ID',
            'dimensions_source_origin' => 'Dimensions Source Origin',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 13/10/2020
    */
    public function getPedidoMercadoLivreShipments()
    {
        return $this->hasOne(PedidoMercadoLivreShipments::className(), ['id' => 'pedido_mercado_livre_shipments_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 13/10/2020
    */
    public static function find()
    {
        return new PedidoMercadoLivreShipmentsItemQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da PedidoMercadoLivreShipmentsItem, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 13/10/2020
*/
class PedidoMercadoLivreShipmentsItemQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 13/10/2020
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido_mercado_livre_shipments_item.nome' => $sort_type]);
    }
}
