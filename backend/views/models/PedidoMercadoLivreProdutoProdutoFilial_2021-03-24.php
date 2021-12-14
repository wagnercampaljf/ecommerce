<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "pedido_mercado_livre_produto_produto_filial".
 *
 * @property integer $id
 * @property integer $pedido_mercado_livre_produto_id
 * @property integer $produto_filial_id
 * @property integer $quantidade
 * @property double $valor
 * @property string $observacao
 * @property string $email
 *
 * @property PedidoMercadoLivreProduto $pedidoMercadoLivreProduto
 * @property ProdutoFilial $produtoFilial
 *
 * @author Unknown 25/09/2020
 */
class PedidoMercadoLivreProdutoProdutoFilial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 25/09/2020
     */
    public static function tableName()
    {
        return 'pedido_mercado_livre_produto_produto_filial';
    }

    /**
     * @inheritdoc
     * @author Unknown 25/09/2020
     */
    public function rules()
    {
        return [
            [['pedido_mercado_livre_produto_id', 'produto_filial_id', 'quantidade'], 'integer'],
            [['observacao', 'email'], 'string'],
            [['valor'], 'required'],
            [['valor'], 'number'],
            [['pedido_mercado_livre_produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => PedidoMercadoLivreProduto::className(), 'targetAttribute' => ['pedido_mercado_livre_produto_id' => 'id']],
            [['produto_filial_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProdutoFilial::className(), 'targetAttribute' => ['produto_filial_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 25/09/2020
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pedido_mercado_livre_produto_id' => 'Pedido Mercado Livre Produto ID',
            'produto_filial_id' => 'Produto Filial ID',
            'quantidade' => 'Quantidade',
            'valor' => 'Valor',
            'observacao' => 'Observação',
            'email' => 'E-mail',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 25/09/2020
    */
    public function getPedidoMercadoLivreProduto()
    {
        return $this->hasOne(PedidoMercadoLivreProduto::className(), ['id' => 'pedido_mercado_livre_produto_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 25/09/2020
    */
    public function getProdutoFilial()
    {
        return $this->hasOne(ProdutoFilial::className(), ['id' => 'produto_filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 25/09/2020
    */
    public static function find()
    {
        return new PedidoMercadoLivreProdutoProdutoFilialQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da PedidoMercadoLivreProdutoProdutoFilial, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 25/09/2020
*/
class PedidoMercadoLivreProdutoProdutoFilialQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 25/09/2020
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido_mercado_livre_produto_produto_filial.nome' => $sort_type]);
    }
}
