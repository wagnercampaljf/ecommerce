<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProdutoFilialVariacao;

/**
 * ProdutoFilialVariacaoSearch represents the model behind the search form about `common\models\ProdutoFilialVariacao`.
 */
class ProdutoFilialVariacaoSearch extends ProdutoFilialVariacao
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'produto_filial_id', 'variacao_id'], 'integer'],
            [['meli_id'], 'safe'],
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
        $query = ProdutoFilialVariacao::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'produto_filial_id' => $this->produto_filial_id,
            'variacao_id' => $this->variacao_id,
        ]);

        $query->andFilterWhere(['ilike', 'meli_id', $this->meli_id]);

        return $dataProvider;
    }
}
