<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OperacaoFinanceira;

/**
 * OperacaoFinanceiraSearch represents the model behind the search form about `common\models\OperacaoFinanceira`.
 */
class OperacaoFinanceiraSearch extends OperacaoFinanceira
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'filial_id'], 'integer'],
            [['numero'], 'safe'],
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
        $query = OperacaoFinanceira::find();

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

        $query->andFilterWhere(['ilike', 'numero', $this->numero]);

        return $dataProvider;
    }
}
