<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\NotaFiscalPedidoProduto;

/**
 * NotaFiscalPedidoProdutoSearch represents the model behind the search form about `backend\models\NotaFiscalPedidoProduto`.
 */
class NotaFiscalPedidoProdutoSearch extends NotaFiscalPedidoProduto
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nota_fiscal_produto_id', 'pedido_mercado_livre_produto_produto_filial_id', 'pedido_produto_filial_cotacao_id', 'pedido_compras_produto_filial_id'], 'integer'],
            [['e_validado'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = NotaFiscalPedidoProduto::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'nota_fiscal_produto_id' => $this->nota_fiscal_produto_id,
            'pedido_mercado_livre_produto_produto_filial_id' => $this->pedido_mercado_livre_produto_produto_filial_id,
            'pedido_produto_filial_cotacao_id' => $this->pedido_produto_filial_cotacao_id,
            'pedido_compras_produto_filial_id' => $this->pedido_compras_produto_filial_id,
            'e_validado' => $this->e_validado,
        ]);

        return $dataProvider;
    }
}
