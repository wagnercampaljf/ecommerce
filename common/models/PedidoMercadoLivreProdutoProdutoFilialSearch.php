<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;

/**
 * PedidoMercadoLivreProdutoProdutoFilialSearch represents the model behind the search form about `common\models\PedidoMercadoLivreProdutoProdutoFilial`.
 */
class PedidoMercadoLivreProdutoProdutoFilialSearch extends PedidoMercadoLivreProdutoProdutoFilial
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pedido_mercado_livre_produto_id', 'produto_filial_id', 'quantidade'], 'integer'],
            [['valor'], 'number'],
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
        $query = PedidoMercadoLivreProdutoProdutoFilial::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'pedido_mercado_livre_produto_id' => $this->pedido_mercado_livre_produto_id,
            'produto_filial_id' => $this->produto_filial_id,
            'quantidade' => $this->quantidade,
            'valor' => $this->valor,
        ]);

        return $dataProvider;
    }
}
