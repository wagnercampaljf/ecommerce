<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogConsultaExpedicao;

/**
 * LogConsultaExpedicaoSearch represents the model behind the search form about `app\common\models\LogConsultaExpedicao`.
 */
class LogConsultaExpedicaoSearch extends LogConsultaExpedicao
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'salvo_por'], 'integer'],
            [['descricao', 'salvo_em'], 'safe'],
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
        $query = LogConsultaExpedicao::find()->orderBy(["id" => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'salvo_em' => $this->salvo_em,
            'salvo_por' => $this->salvo_por,
        ]);

        $query->andFilterWhere(['like', 'descricao', $this->descricao]);

        return $dataProvider;
    }
}
