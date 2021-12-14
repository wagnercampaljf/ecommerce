<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Produto;

/**
 * ProdutoSearch represents the model behind the search form about `common\models\Produto`.
 */
class ProdutoSearch extends Produto
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'fabricante_id', 'subcategoria_id'], 'integer'],
            [['nome', 'descricao', 'imagem', 'codigo_global', 'codigo_montadora', 'codigo_fabricante', 'slug', 'micro_descricao', 'aplicacao','pis_cofins','texto_vetor'], 'safe'],
            [['peso', 'altura', 'largura', 'profundidade'], 'number'],
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
        $query = Produto::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'peso' => $this->peso,
            'altura' => $this->altura,
            'largura' => $this->largura,
            'profundidade' => $this->profundidade,
            'fabricante_id' => $this->fabricante_id,
            'subcategoria_id' => $this->subcategoria_id,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'descricao', $this->descricao])
            ->andFilterWhere(['like', 'imagem', $this->imagem])
            ->andFilterWhere(['like', 'codigo_global', $this->codigo_global])
            ->andFilterWhere(['like', 'codigo_montadora', $this->codigo_montadora])
            ->andFilterWhere(['like', 'codigo_fabricante', $this->codigo_fabricante])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'micro_descricao', $this->micro_descricao])
            ->andFilterWhere(['like', 'aplicacao', $this->aplicacao])
            ->andFilterWhere(['like', 'texto_vetor', $this->texto_vetor]);

        return $dataProvider;
    }
}
