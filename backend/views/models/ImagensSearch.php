<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Imagens;

/**
 * ImagensSearch represents the model behind the search form about `common\models\Imagens`.
 */
class ImagensSearch extends Imagens
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'produto_id', 'ordem'], 'integer'],
            [['imagem'], 'safe'],
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
        $query = Imagens::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'produto_id' => $this->produto_id,
            'ordem' => $this->ordem,
        ]);

        $query->andFilterWhere(['like', 'imagem', $this->imagem]);

        return $dataProvider;
    }
}
