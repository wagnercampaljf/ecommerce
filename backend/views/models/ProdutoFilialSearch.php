<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProdutoFilial;

/**
 * ProdutoFilialSearch represents the model behind the search form about `common\models\ProdutoFilial`.
 */
class ProdutoFilialSearch extends ProdutoFilial
{
    public $codigo_global;
    public $produto_nome;
    public $filial_nome;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'produto_id', 'filial_id', 'quantidade', 'envio'], 'integer'],
            [['meli_id', 'meli_id_sem_juros', 'meli_id_full'], 'safe'],
            [['status_b2w'], 'boolean'],
            [['codigo_global','produto_nome','filial_nome'], 'safe'],
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
        //$query = ProdutoFilial::find();
        $query = ProdutoFilial::find()->select(['produto_filial.*','produto.nome', 'produto.codigo_global','filial.nome'])
                                      ->joinWith(['produto', 'filial'])
                                      ->addOrderBy(['produto_filial.id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'produto_filial.id' => $this->id,
            'produto_id' => $this->produto_id,
            'filial_id' => $this->filial_id,
            'quantidade' => $this->quantidade,
            'status_b2w' => $this->status_b2w,
            'envio' => $this->envio,
        ]);

        $query->andFilterWhere(['like', 'meli_id', $this->meli_id]);
	$query->andFilterWhere(['like', 'meli_id_sem_juros', $this->meli_id_sem_juros]);
	$query->andFilterWhere(['like', 'meli_id_full', $this->meli_id_full]);

        $query->andFilterWhere(['like', 'produto.codigo_global', $this->codigo_global]);
        $query->andFilterWhere(['like', 'filial.nome', $this->filial_nome]);
        $query->andFilterWhere(['like', 'produto.nome', $this->produto_nome]);

	//$query->andFilterWhere(['<>', 'produto_filial.filial_id', 43]);
	$query->andFilterWhere(['<>', 'produto_filial.filial_id', 98]);

        return $dataProvider;
    }
}
