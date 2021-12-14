<?php

namespace backend\models;

use Yii;

/**
 * Este é o model para a tabela "nota_fiscal_pedido_produto".
 *
 * @property integer $id
 * @property integer $nota_fiscal_produto_id
 * @property integer $pedido_mercado_livre_produto_produto_filial_id
 * @property integer $pedido_produto_filial_cotacao_id
 * @property integer $pedido_compras_produto_filial_id
 * @property boolean $e_validado
 *
 * @author Unknown 01/06/2021
 */
class NotaFiscalPedidoProduto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 01/06/2021
     */
    public static function tableName()
    {
        return 'nota_fiscal_pedido_produto';
    }

    /**
     * @inheritdoc
     * @author Unknown 01/06/2021
     */
    public function rules()
    {
        return [
            [['nota_fiscal_produto_id', 'pedido_mercado_livre_produto_produto_filial_id', 'pedido_produto_filial_cotacao_id', 'pedido_compras_produto_filial_id'], 'default', 'value' => null],
            [['nota_fiscal_produto_id', 'pedido_mercado_livre_produto_produto_filial_id', 'pedido_produto_filial_cotacao_id', 'pedido_compras_produto_filial_id'], 'integer'],
            [['e_validado'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 01/06/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nota_fiscal_produto_id' => 'Nota Fiscal Produto ID',
            'pedido_mercado_livre_produto_produto_filial_id' => 'Pedido Mercado Livre Produto Produto Filial ID',
            'pedido_produto_filial_cotacao_id' => 'Pedido Produto Filial Cotacao ID',
            'pedido_compras_produto_filial_id' => 'Pedido Compras Produto Filial ID',
            'e_validado' => 'E Validado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 01/06/2021
    */
    public static function find()
    {
        return new NotaFiscalPedidoProdutoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da NotaFiscalPedidoProduto, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 01/06/2021
*/
class NotaFiscalPedidoProdutoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 01/06/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['nota_fiscal_pedido_produto.nome' => $sort_type]);
    }
}
