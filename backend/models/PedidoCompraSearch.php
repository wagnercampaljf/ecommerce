<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PedidoCompra;

/**
 * PedidoCompraSearch represents the model behind the search form about `backend\models\PedidoCompra`.
 */
class PedidoCompraSearch extends PedidoCompra
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'filial_id'], 'integer'],
            [['valor_total_pedido'], 'number'],
            [['descricao', 'data', 'observacao', 'email', 'corpo_email', 'status'], 'safe'],
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
        $query = PedidoCompra::find()->orderBy(["data" => SORT_DESC, "id" => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'data' => $this->data,
            'filial_id' => $this->filial_id,
            'valor_total_pedido' => $this->valor_total_pedido,
        ]);

        $query->andFilterWhere(['like', 'descricao', $this->descricao])
            ->andFilterWhere(['like', 'observacao', $this->observacao])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'corpo_email', $this->corpo_email])
            ->andFilterWhere(['like', 'status', $this->status]);
            

        return $dataProvider;
    }
}
