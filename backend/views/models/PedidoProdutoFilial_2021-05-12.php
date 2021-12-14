<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "pedido_produto_filial".
 *
 * @property integer $produto_filial_id
 * @property integer $pedido_id
 * @property double $valor
 * @property integer $quantidade
 *
 * @property Pedido $pedido
 * @property ProdutoFilial $produtoFilial
 *
 * @author Vinicius Schettino 02/12/2014
 */
class PedidoProdutoFilial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'pedido_produto_filial';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['produto_filial_id', 'pedido_id', 'quantidade', 'valor'], 'required'],
            [['produto_filial_id', 'pedido_id', 'quantidade'], 'integer'],
            [['valor'], 'number']
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'produto_filial_id' => 'Produto Filial ID',
            'pedido_id' => 'Pedido ID',
            'valor' => 'Valor',
            'quantidade' => 'Quantidade'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getPedido()
    {
        return $this->hasOne(Pedido::className(), ['id' => 'pedido_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getProdutoFilial()
    {
        return $this->hasOne(ProdutoFilial::className(), ['id' => 'produto_filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public static function find()
    {
        return new PedidoProdutoFilialQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da PedidoProdutoFilial, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class PedidoProdutoFilialQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido_produto_filial.nome' => $sort_type]);
    }
}
