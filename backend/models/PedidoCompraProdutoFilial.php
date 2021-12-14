<?php

namespace backend\models;

use common\models\ProdutoFilial;

use Yii;

/**
 * Este é o model para a tabela "pedido_compra_produto_filial".
 *
 * @property integer $id
 * @property integer $quantidade
 * @property string $valor_compra
 * @property string $valor_venda
 * @property integer $pedido_compra_id
 * @property integer $produto_filial_id
 * @property string $observacao
 * @property string $valor_markup
 * @property MarkupMestre[] $markupMestre
 *
 * @property PedidoCompra $pedidoCompra
 * @property ProdutoFilial $produtoFilial
 *
 * @author Unknown 05/02/2021
 */
class PedidoCompraProdutoFilial extends \yii\db\ActiveRecord
{
    public $markup_id;
    /**
     * @inheritdoc
     * @author Unknown 05/02/2021
     */
    public static function tableName()
    {
        return 'pedido_compra_produto_filial';
    }

    /**
     * @inheritdoc
     * @author Unknown 05/02/2021
     */
    public function rules()
    {
        return [
            [['quantidade', 'valor_compra', 'valor_venda'], 'required'],
            [['quantidade'], 'integer'],
            [['valor_compra', 'valor_venda', 'valor_markup'], 'number'],
            [['e_verificado', 'e_atualizar_site'], 'boolean'],
            [['observacao'], 'string', 'max' => 200],
            [['markup_id'], 'required', 'on' => ['create']],
            [['pedido_compra_id'], 'exist', 'skipOnError' => true, 'targetClass' => PedidoCompra::className(), 'targetAttribute' => ['pedido_compra_id' => 'id']],
            [['produto_filial_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProdutoFilial::className(), 'targetAttribute' => ['produto_filial_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 05/02/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quantidade' => 'Quantidade',
            'valor_compra' => 'Valor Compra',
            'valor_venda' => 'Valor Venda',
            'pedido_compra_id' => 'Pedido Compra ID',
            'produto_filial_id' => 'Produto Filial ID',
            'observacao' => 'Observacao',
            'e_verificado' => 'Verificado',
            'e_atualizar_site' => 'Atualizar Preço Site',
            'valor_markup' => 'Valor Markup',
            'markup_id' => 'Markup',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 05/02/2021
     */
    public function getPedidoCompra()
    {
        return $this->hasOne(PedidoCompra::className(), ['id' => 'pedido_compra_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 05/02/2021
     */
    public function getProdutoFilial()
    {
        return $this->hasOne(ProdutoFilial::className(), ['id' => 'produto_filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 05/02/2021
     */
    public static function find()
    {
        return new PedidoCompraProdutoFilialQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da PedidoCompraProdutoFilial, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 05/02/2021
 */
class PedidoCompraProdutoFilialQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 05/02/2021
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido_compra_produto_filial.nome' => $sort_type]);
    }
}
