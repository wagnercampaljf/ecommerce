<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "pedido_produto_filial".
 *
 * @property integer $produto_filial_id
 * @property integer $pedido_id
 * @property double $valor
 * @property double $valor_cotacao
 * @property integer $quantidade
 * @property bool $e_email_enviado
 * @property bool $e_revisao
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
            [['produto_filial_id', 'pedido_id', 'quantidade'], 'required'],
            [['produto_filial_id', 'pedido_id', 'quantidade'], 'integer'],
            [['valor', 'valor_cotacao'], 'number'],
            [['e_email_enviado', 'e_revisao'], 'boolean'],
            [['observacao'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'produto_filial_id' => 'Produto Filial ID',
            'pedido_id' => 'Pedido ID',
            'valor' => 'Valor',
            'valor_cotacao' => 'Valor Cotação',
            'quantidade' => 'Quantidade',
            'e_email_enviado' => 'Email Enviado',
            'observacao' => 'Observação',
            'e_revisao' => 'Enviar Revisão',
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
