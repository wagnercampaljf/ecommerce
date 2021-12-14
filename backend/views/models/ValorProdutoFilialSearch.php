<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ValorProdutoFilial;

/**
 * ValorProdutoFilialSearch represents the model behind the search form about `common\models\ValorProdutoFilial`.
 */
class ValorProdutoFilialSearch extends ValorProdutoFilial
{
    public $codigo_global;
    public $produto_nome;
    public $filial_nome;
    public $codigo_fabricante;

     /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'produto_filial_id'], 'integer'],
            [['valor', 'valor_cnpj'], 'number'],
            [['dt_inicio', 'dt_fim'], 'safe'],
            [['promocao'], 'boolean'],
	    [['codigo_global','produto_nome','filial_nome','codigo_fabricante'], 'safe'],
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
        //$query = ValorProdutoFilial::find();
        $query = ValorProdutoFilial::find() ->joinWith(['produtoFilial', 'produtoFilial.produto', 'produtoFilial.filial'])
                                            //->andWhere(['=','produto_filial.id','valor_produto_filial.produto_filial_id'])
                                            //->andWhere(['=','produto.id','produto_filial.produto_id'])
                                            //->andWhere(['=','filial.id','produto_filial.filial_id']);
                                            ->addOrderBy(['produto.nome'=>SORT_ASC])
					    ->addOrderBy(['filial.nome'=>SORT_ASC])
                                            ->addOrderBy(['valor_produto_filial.dt_inicio'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'valor' => $this->valor,
            'dt_inicio' => $this->dt_inicio,
            'produto_filial_id' => $this->produto_filial_id,
            'dt_fim' => $this->dt_fim,
            'promocao' => $this->promocao,
            'valor_cnpj' => $this->valor_cnpj,
	    //'codigo_fabricante' => $this->codigo_fabricante,
            //'produtoFilial.filial.nome' => $this->produtoFilial->filial->nome,
            //'produtoFilial.produto.nome' => $this->produtoFilial->produto->nome,
        ]);

	$query->andFilterWhere(['like', 'produto.codigo_global', $this->codigo_global]);
        $query->andFilterWhere(['like', 'filial.nome', $this->filial_nome]);
        $query->andFilterWhere(['like', 'produto.nome', $this->produto_nome]);
	$query->andFilterWhere(['like', 'produto.codigo_fabricante', $this->codigo_fabricante]);

        return $dataProvider;
    }
}
