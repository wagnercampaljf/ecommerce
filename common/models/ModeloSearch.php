<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Modelo;

/**
 * ModeloSearch represents the model behind the search form about `common\models\Modelo`.
 */
class ModeloSearch extends Modelo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nome', 'marca_id'], 'safe'],
            [['categoria_modelo_id'], 'integer'],
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
        $query = Modelo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'categoria_modelo_id' => $this->categoria_modelo_id,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'marca_id', $this->marca_id]);

        return $dataProvider;
    }
}
