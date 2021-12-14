<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PedidoProdutoFilial;

/**
 * PedidoProdutoFilialSearch represents the model behind the search form about `common\models\PedidoProdutoFilial`.
 */
class PedidoProdutoFilialSearch extends PedidoProdutoFilial
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['produto_filial_id', 'pedido_id', 'quantidade'], 'integer'],
            [['valor', 'valor_cotacao'], 'number'],
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
        $query = PedidoProdutoFilial::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'produto_filial_id' => $this->produto_filial_id,
            'pedido_id' => $this->pedido_id,
            'valor' => $this->valor,
            'quantidade' => $this->quantidade,
            'valor_cotacao' => $this->valor_cotacao,
        ]);

        return $dataProvider;
    }
}
