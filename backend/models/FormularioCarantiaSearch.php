<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\FormularioGarantia;

/**
 * FormularioCarantiaSearch represents the model behind the search form about `common\models\FormularioGarantia`.
 */
class FormularioCarantiaSearch extends FormularioGarantia
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nome', 'email', 'data_compra', 'razao_social', 'nr_nf_compra', 'codigo_peca_seis_digitos', 'modelo_do_veiculo', 'ano', 'chassi', 'numero_de_serie_do_motor', 'data_aplicacao', 'km_montagem', 'km_defeito', 'contato', 'telefone', 'descricao_do_defeito_apresentado'], 'safe'],
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
        $query = FormularioGarantia::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'data_compra' => $this->data_compra,
            'data_aplicacao' => $this->data_aplicacao,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'razao_social', $this->razao_social])
            ->andFilterWhere(['like', 'nr_nf_compra', $this->nr_nf_compra])
            ->andFilterWhere(['like', 'codigo_peca_seis_digitos', $this->codigo_peca_seis_digitos])
            ->andFilterWhere(['like', 'modelo_do_veiculo', $this->modelo_do_veiculo])
            ->andFilterWhere(['like', 'ano', $this->ano])
            ->andFilterWhere(['like', 'chassi', $this->chassi])
            ->andFilterWhere(['like', 'numero_de_serie_do_motor', $this->numero_de_serie_do_motor])
            ->andFilterWhere(['like', 'km_montagem', $this->km_montagem])
            ->andFilterWhere(['like', 'km_defeito', $this->km_defeito])
            ->andFilterWhere(['like', 'contato', $this->contato])
            ->andFilterWhere(['like', 'telefone', $this->telefone])
            ->andFilterWhere(['like', 'descricao_do_defeito_apresentado', $this->descricao_do_defeito_apresentado]);

        return $dataProvider;
    }
}
