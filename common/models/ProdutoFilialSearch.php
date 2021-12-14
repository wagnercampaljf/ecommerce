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
            [['meli_id'], 'safe'],
            [['status_b2w'], 'boolean'],
            [['codigo_global', 'produto_nome', 'filial_nome'], 'safe'],
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
        $query = ProdutoFilial::find()->select(['produto_filial.*', 'produto.nome', 'produto.codigo_global', 'filial.nome'])
            ->joinWith(['produto', 'filial'])
            ->addOrderBy(['produto_filial.id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

	//$query->andWhere(['<>', 'filial_id', 98]);


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

        $query->andFilterWhere(['like', 'produto.codigo_global', $this->codigo_global]);
        $query->andFilterWhere(['like', 'filial.nome', $this->filial_nome]);
        $query->andFilterWhere(['like', 'produto.nome', $this->produto_nome]);

	$query->andWhere(['<>', 'filial_id', 98]);
	$query->andWhere(['<>', 'filial_id', 100]);

        //echo 123; die;

        return $dataProvider;
    }
}
