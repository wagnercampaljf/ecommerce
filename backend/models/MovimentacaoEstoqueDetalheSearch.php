<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MovimentacaoEstoqueDetalhe;

/**
 * MovimentacaoEstoqueDetalheSearch represents the model behind the search form about `common\models\MovimentacaoEstoqueDetalhe`.
 */
class MovimentacaoEstoqueDetalheSearch extends MovimentacaoEstoqueDetalhe
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'produto_id', 'salvo_por', 'quantidade', 'movimentacao_estoque_mestre_id'], 'integer'],
            [['descricao', 'salvo_em', 'id_ajuste_omie_entrada', 'id_ajuste_omie_saida'], 'safe'],
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
    public function search($params, $id = null)
    {
        $query = MovimentacaoEstoqueDetalhe::find()->andFilterWhere([
            'movimentacao_estoque_mestre_id' => $id
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'produto_id' => $this->produto_id,
            'salvo_em' => $this->salvo_em,
            'salvo_por' => $this->salvo_por,
            'quantidade' => $this->quantidade,
            'movimentacao_estoque_mestre_id' => $this->movimentacao_estoque_mestre_id,
        ]);

        $query->andFilterWhere(['ilike', 'descricao', $this->descricao])
            ->andFilterWhere(['ilike', 'id_ajuste_omie_entrada', $this->id_ajuste_omie_entrada])
            ->andFilterWhere(['ilike', 'id_ajuste_omie_saida', $this->id_ajuste_omie_saida]);

        return $dataProvider;
    }
}
