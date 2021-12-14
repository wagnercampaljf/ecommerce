<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MovimentacaoEstoqueMestre;

/**
 * MovimentacaoEstoqueMestreSearch represents the model behind the search form about `common\models\MovimentacaoEstoqueMestre`.
 */
class MovimentacaoEstoqueMestreSearch extends MovimentacaoEstoqueMestre
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'autorizado_por', 'salvo_por', 'filial_origem_id', 'filial_destino_id'], 'integer'],
            [['descricao', 'salvo_em', 'codigo_remessa_omie'], 'safe'],
            [['e_autorizado'], 'boolean'],
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
        $query = MovimentacaoEstoqueMestre::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'e_autorizado' => $this->e_autorizado,
            'autorizado_por' => $this->autorizado_por,
            'salvo_em' => $this->salvo_em,
            'salvo_por' => $this->salvo_por,
            'filial_origem_id' => $this->filial_origem_id,
            'filial_destino_id' => $this->filial_destino_id,
        ]);

        $query->andFilterWhere(['ilike', 'descricao', $this->descricao])
            ->andFilterWhere(['ilike', 'codigo_remessa_omie', $this->codigo_remessa_omie]);

        return $dataProvider;
    }
}
