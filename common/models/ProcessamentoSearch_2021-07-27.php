<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Processamento;

/**
 * ProcessamentoSearch represents the model behind the search form about `common\models\Processamento`.
 */
class ProcessamentoSearch extends Processamento
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'funcao_id'], 'integer'],
            [['data_hora_inicial', 'data_hora_final', 'observacao', 'status'], 'safe'],
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
        $query = Processamento::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'funcao_id' => $this->funcao_id,
            'data_hora_inicial' => $this->data_hora_inicial,
            'data_hora_final' => $this->data_hora_final,
        ]);

        $query->andFilterWhere(['ilike', 'observacao', $this->observacao])
            ->andFilterWhere(['ilike', 'status', $this->status]);

        return $dataProvider;
    }
}
