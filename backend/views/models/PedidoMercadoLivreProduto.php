<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "pedido_mercado_livre_produto".
 *
 * @property integer $id
 * @property integer $pedido_mercado_livre_id
 * @property integer $produto_filial_id
 * @property string $produto_meli_id
 * @property string $title
 * @property string $categoria_meli_id
 * @property string $condition
 * @property integer $quantity
 * @property string $unit_price
 * @property string $full_unit_price
 * @property string $sale_fee
 * @property string $listing_type_id
 * @property integer $produto_filial_selecionado_id
 * @property string $valor_cotacao
 *
 * @property PedidoMercadoLivre $pedidoMercadoLivre
 * @property ProdutoFilial $produtoFilial
 * @property ProdutoFilial $produtoFilialSelecionado
 *
 * @author Unknown 12/06/2020
 */
class PedidoMercadoLivreProduto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 12/06/2020
     */
    public static function tableName()
    {
        return 'pedido_mercado_livre_produto';
    }

    /**
     * @inheritdoc
     * @author Unknown 12/06/2020
     */
    public function rules()
    {
        return [
            [['pedido_mercado_livre_id'], 'required'],
            [['pedido_mercado_livre_id', 'produto_filial_id', 'quantity', 'produto_filial_selecionado_id'], 'integer'],
            [['unit_price', 'full_unit_price', 'sale_fee', 'valor_cotacao'], 'number'],
            [['produto_meli_id'], 'string', 'max' => 24],
            [['title'], 'string', 'max' => 200],
            [['categoria_meli_id'], 'string', 'max' => 20],
            [['condition'], 'string', 'max' => 30],
            [['listing_type_id'], 'string', 'max' => 50],
            [['pedido_mercado_livre_id'], 'exist', 'skipOnError' => true, 'targetClass' => PedidoMercadoLivre::className(), 'targetAttribute' => ['pedido_mercado_livre_id' => 'id']],
            [['produto_filial_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProdutoFilial::className(), 'targetAttribute' => ['produto_filial_id' => 'id']],
            [['produto_filial_selecionado_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProdutoFilial::className(), 'targetAttribute' => ['produto_filial_selecionado_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 12/06/2020
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pedido_mercado_livre_id' => 'Pedido Mercado Livre ID',
            'produto_filial_id' => 'Produto Filial ID',
            'produto_meli_id' => 'Produto Meli ID',
            'title' => 'Title',
            'categoria_meli_id' => 'Categoria Meli ID',
            'condition' => 'Condition',
            'quantity' => 'Quantity',
            'unit_price' => 'Unit Price',
            'full_unit_price' => 'Full Unit Price',
            'sale_fee' => 'Sale Fee',
            'listing_type_id' => 'Listing Type ID',
            'produto_filial_selecionado_id' => 'Produto Selecionado',
            'valor_cotacao' => 'Valor Cotação',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 12/06/2020
    */
    public function getPedidoMercadoLivre()
    {
        return $this->hasOne(PedidoMercadoLivre::className(), ['id' => 'pedido_mercado_livre_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 12/06/2020
    */
    public function getProdutoFilial()
    {
        return $this->hasOne(ProdutoFilial::className(), ['id' => 'produto_filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 12/06/2020
    */
    public function getProdutoFilialSelecionado()
    {
        return $this->hasOne(ProdutoFilial::className(), ['id' => 'produto_filial_selecionado_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 12/06/2020
    */
    public static function find()
    {
        return new PedidoMercadoLivreProdutoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da PedidoMercadoLivreProduto, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 12/06/2020
*/
class PedidoMercadoLivreProdutoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 12/06/2020
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido_mercado_livre_produto.nome' => $sort_type]);
    }
}
