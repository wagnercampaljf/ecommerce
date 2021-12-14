<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Marca;

/**
 * MarcaSearch represents the model behind the search form about `common\models\Marca`.
 */
class MarcaSearch extends Marca
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nome', 'imagem'], 'safe'],
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
        $query = Marca::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'imagem', $this->imagem]);

        return $dataProvider;
    }
}
