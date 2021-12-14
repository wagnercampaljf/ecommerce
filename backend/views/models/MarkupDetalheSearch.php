<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MarkupDetalhe;

/**
 * MarkupDetalheSearch represents the model behind the search form about `app\common\models\MarkupDetalhe`.
 */
class MarkupDetalheSearch extends MarkupDetalhe
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'markup_mestre_id'], 'integer'],
            [['e_margem_absoluta'], 'boolean'],
            [['valor_minimo', 'valor_maximo', 'margem'], 'number'],
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
    public function search($params, $markup_mestre_id)
    {
        $query = MarkupDetalhe::find()->andWhere(['=', 'markup_mestre_id', $markup_mestre_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'markup_mestre_id' => $this->markup_mestre_id,
            'e_margem_absoluta' => $this->e_margem_absoluta,
            'valor_minimo' => $this->valor_minimo,
            'valor_maximo' => $this->valor_maximo,
            'margem' => $this->margem,
        ]);

        return $dataProvider;
    }
}
