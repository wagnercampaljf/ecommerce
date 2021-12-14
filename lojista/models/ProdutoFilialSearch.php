<?php

namespace lojista\models;

use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * ProdutoFilialSearch represents the model behind the search form about `common\models\ProdutoFilial`.
 */
class ProdutoFilialSearch extends ProdutoFilial
{
    public $nome_produto;
    public $cod_globalProduto;
    public $dt_inicio;
    public $dt_fim;
    public $valor;
    public $valor_cnpj;
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'produto_id', 'filial_id', 'quantidade'], 'integer'],
            [['nome_produto', 'cod_globalProduto', 'status'], 'string'],
            [['dt_inicio', 'dt_fim'], 'date', 'format' => 'dd/mm/yyyy'],
            [['valor', 'valor_cnpj'], 'safe']
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
        $query = ProdutoFilial::find()->joinWith(['produto'])
            ->comValorRecente()
            ->andWhere(['produto_filial.filial_id' => Yii::$app->user->identity->filial_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'quantidade',
                    'nome_produto' => [
                        'asc' => ['produto.nome' => SORT_ASC],
                        'desc' => ['produto.nome' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                    'cod_globalProduto' => [
                        'asc' => ['produto.codigo_global' => SORT_ASC],
                        'desc' => ['produto.codigo_global' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                    'valor' => [
                        'asc' => ['valor_produtoFilial.valor' => SORT_ASC],
                        'desc' => ['valor_produtoFilial.valor' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                    'valor_cnpj' => [
                        'asc' => ['valor_produtoFilial.valor_cnpj' => SORT_ASC],
                        'desc' => ['valor_produtoFilial.valor_cnpj' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $this->valor = str_replace(',', '.', $this->valor);
        $query->andFilterWhere([
            'produto_filial.id' => $this->id,
            'produto_filial.produto_id' => $this->produto_id,
            'produto_filial.filial_id' => $this->filial_id,
            'produto_filial.quantidade' => $this->quantidade,
            'valor_produtoFilial.valor' => $this->valor,
            'valor_produtoFilial.valor_cnpj' => $this->valor_cnpj,
        ]);
        $query->andFilterWhere(['like', 'lower(produto.nome)', strtolower($this->nome_produto)]);
        $query->andFilterWhere(['like', 'lower(produto.codigo_global)', strtolower($this->cod_globalProduto)]);
        $this->filterDates($query);

        $dataProvider->totalCount = $query->count('DISTINCT "produto_filial"."id"');

        return $dataProvider;
    }

    /**
     * @param $query Query
     */
    private function filterDates(&$query)
    {
        if ($dt = date_create_from_format('d/m/Y', $this->dt_inicio)) {
            $query->andFilterWhere([
                'between',
                'valor_produtoFilial.dt_inicio',
                $dt->format('Y-m-d 00:00:00'),
                $dt->format('Y-m-d 23:59:59')
            ]);
        }
        if ($dt = date_create_from_format('d/m/Y', $this->dt_fim)) {
            $query->andFilterWhere([
                'between',
                'valor_produtoFilial.dt_fim',
                $dt->format('Y-m-d 00:00:00'),
                $dt->format('Y-m-d 23:59:59')
            ]);
        }
    }
}
