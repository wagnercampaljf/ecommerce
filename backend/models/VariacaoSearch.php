<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Variacao;

/**
 * VariacaoSearch represents the model behind the search form about `common\models\Variacao`.
 */
class VariacaoSearch extends Variacao
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'variacao_tipo_id'], 'integer'],
            [['descricao'], 'safe'],
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
        $query = Variacao::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'variacao_tipo_id' => $this->variacao_tipo_id,
        ]);

        $query->andFilterWhere(['ilike', 'descricao', $this->descricao]);

        return $dataProvider;
    }
}
