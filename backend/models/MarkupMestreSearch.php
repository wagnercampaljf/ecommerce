<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\MarkupMestre;
/**
 * MarkupMestreSearch represents the model behind the search form about `app\models\MarkupMestre`.
 */
class MarkupMestreSearch extends MarkupMestre
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['descricao', 'data_inicio', 'observacao'], 'safe'],
            [['e_margem_absoluta_padrao', 'e_markup_padrao'], 'boolean'],
            [['margem_padrao'], 'number'],
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
        $query = MarkupMestre::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'data_inicio' => $this->data_inicio,
            'e_margem_absoluta_padrao' => $this->e_margem_absoluta_padrao,
            'e_markup_padrao' => $this->e_markup_padrao,
            'margem_padrao' => $this->margem_padrao,
        ]);

        $query->andFilterWhere(['like', 'descricao', $this->descricao])
            ->andFilterWhere(['like', 'observacao', $this->observacao]);

        return $dataProvider;
    }
}
