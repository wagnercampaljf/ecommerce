<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MovimentacaoFinanceira;

/**
 * MovimentacaoFinanceiraSearch represents the model behind the search form about `common\models\MovimentacaoFinanceira`.
 */
class MovimentacaoFinanceiraSearch extends MovimentacaoFinanceira
{
    
    public $operacao_financeira_numero;
    public $movimentacao_fincaneira_tipo_descricao;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'operacao_financeira_id', 'movimentacao_financeira_tipo_id'], 'integer'],
            [['numero', 'cliente_cpf_cnpj', 'cliente_nome', 'data_hora'], 'safe'],
            [['valor', 'valor_total'], 'number'],
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
        $query = MovimentacaoFinanceira::find()->orderBy(["data_hora" => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'data_hora' => $this->data_hora,
            'valor' => $this->valor,
            'valor_total' => $this->valor_total,
            'operacao_financeira_id' => $this->operacao_financeira_id,
            'movimentacao_financeira_tipo_id' => $this->movimentacao_financeira_tipo_id,
        ]);

        $query->andFilterWhere(['ilike', 'numero', $this->numero]);

        return $dataProvider;
    }
}
