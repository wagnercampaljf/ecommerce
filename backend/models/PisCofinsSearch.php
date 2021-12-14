<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PisCofins;

/**
 * PisCofinsSearch represents the model behind the search form about `backend\models\PisCofins`.
 */
class PisCofinsSearch extends PisCofins
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['pis_cofins', 'ncm', 'data_registro'], 'safe'],
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
        $query = PisCofins::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'data_registro' => $this->data_registro,
        ]);

        $query->andFilterWhere(['ilike', 'pis_cofins', $this->pis_cofins])
            ->andFilterWhere(['ilike', 'ncm', $this->ncm]);

        return $dataProvider;
    }
}
