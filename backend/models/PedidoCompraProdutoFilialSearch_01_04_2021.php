<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PedidoCompraProdutoFilial;

/**
 * PedidoCompraProdutoFilialSearch represents the model behind the search form about `backend\models\PedidoCompraProdutoFilial`.
 */
class PedidoCompraProdutoFilialSearch extends PedidoCompraProdutoFilial
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'quantidade', 'pedido_compra_id', 'produto_filial_id'], 'integer'],
            [['valor_compra', 'valor_venda'], 'number'],
            [['observacao'], 'safe'],
            [['e_verificado', 'e_atualizar_site'], 'boolean'],
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
    public function search($params, $id = null)
    {
        $query = PedidoCompraProdutoFilial::find()->andFilterWhere([
            'pedido_compra_id' => $id
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'quantidade' => $this->quantidade,
            'valor_compra' => $this->valor_compra,
            'valor_venda' => $this->valor_venda,
            'pedido_compra_id' => $this->pedido_compra_id,
            'produto_filial_id' => $this->produto_filial_id,
            'e_verificado' => $this->e_verificado,
            'e_atualizar_site' => $this->e_atualizar_site,
        ]);

        $query->andFilterWhere(['like', 'observacao', $this->observacao]);

        return $dataProvider;
    }
}

