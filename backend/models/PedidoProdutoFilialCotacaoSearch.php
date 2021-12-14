<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PedidoProdutoFilialCotacao;

/**
 * PedidoProdutoFilialCotacaoSearch represents the model behind the search form about `backend\models\PedidoProdutoFilialCotacao`.
 */
class PedidoProdutoFilialCotacaoSearch extends PedidoProdutoFilialCotacao
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pedido_produto_filial_id', 'produto_filial_id', 'quantidade'], 'integer'],
            [['valor'], 'number'],
            [['observacao', 'email'], 'safe'],
            [['e_atualizar_site'], 'boolean']
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
        $query = PedidoProdutoFilialCotacao::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'pedido_produto_filial_id' => $this->pedido_produto_filial_id,
            'produto_filial_id' => $this->produto_filial_id,
            'quantidade' => $this->quantidade,
            'valor' => $this->valor,
        ]);

        $query->andFilterWhere(['ilike', 'observacao', $this->observacao])
            ->andFilterWhere(['ilike', 'email', $this->email]);

        return $dataProvider;
    }
}
