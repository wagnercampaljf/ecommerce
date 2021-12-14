<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Transportadora;

/**
 * TransportadoraSearch represents the model behind the search form about `common\models\Transportadora`.
 */
class TransportadoraSearch extends Transportadora
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'filial_id'], 'integer'],
            [['nome', 'codigo', 'codigo_omie', 'razao_social', 'email', 'cnpj'], 'safe'],
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
        $query = Transportadora::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'filial_id' => $this->filial_id,
        ]);

        $query->andFilterWhere(['ilike', 'nome', $this->nome])
            ->andFilterWhere(['ilike', 'codigo', $this->codigo])
            ->andFilterWhere(['ilike', 'codigo_omie', $this->codigo_omie])
            ->andFilterWhere(['ilike', 'razao_social', $this->razao_social])
            ->andFilterWhere(['ilike', 'email', $this->email])
            ->andFilterWhere(['ilike', 'cnpj', $this->cnpj]);

        return $dataProvider;
    }
}
