<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PedidoMercadoLivreProduto;

/**
 * PedidoMercadoLivreProdutoSearch represents the model behind the search form about `common\models\PedidoMercadoLivreProduto`.
 */
class PedidoMercadoLivreProdutoSearch extends PedidoMercadoLivreProduto
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pedido_mercado_livre_id', 'produto_filial_id', 'quantity'], 'integer'],
            [['produto_meli_id', 'title', 'categoria_meli_id', 'condition', 'listing_type_id'], 'safe'],
            [['unit_price', 'full_unit_price', 'sale_fee'], 'number'],
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
        $query = PedidoMercadoLivreProduto::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'pedido_mercado_livre_id' => $this->pedido_mercado_livre_id,
            'produto_filial_id' => $this->produto_filial_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'full_unit_price' => $this->full_unit_price,
            'sale_fee' => $this->sale_fee,
        ]);

        $query->andFilterWhere(['like', 'produto_meli_id', $this->produto_meli_id])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'categoria_meli_id', $this->categoria_meli_id])
            ->andFilterWhere(['like', 'condition', $this->condition])
            ->andFilterWhere(['like', 'listing_type_id', $this->listing_type_id]);

        return $dataProvider;
    }
}
