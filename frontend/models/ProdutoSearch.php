<?php

namespace frontend\models;

use common\models\Marca;
use common\models\Modelo;
use common\models\Produto;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * ProdutoSearch represents the model behind the search form about `common\models\Produto`.
 */
class ProdutoSearch extends Produto
{
    public $cidade_id;
    public $categoria_id;
    public $estado_id;
    public $filial_id;
    public $preco;
    public $marca_id;
    public $modelo_id;
    public $caracteristica_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'fabricante_id',
                    'subcategoria_id',
                    'cidade_id',
                    'categoria_id',
                    'estado_id',
                    'filial_id',
                    'marca_id',
                    'caracteristica_id'
                ],
                'integer'
            ],
            [
                [
                    'nome',
                    'descricao',
                    'codigo_global',
                ],
                'string'
            ],
            [['nome'], 'trim'],
            [['peso', 'altura', 'largura', 'profundidade'], 'number'],
            [['preco', 'modelo_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $relevancia = explode(' ', ArrayHelper::getValue($params, 'nome'));
        $query = Produto::find()->addSelect([
            '"produto"."id"',
            '"produto"."nome"',
            '"produto"."codigo_global"',
            '"produto"."aplicacao"',
            '"produto"."codigo_similar"',
            '"produto"."aplicacao_complementar"',
            '"produto"."slug"',
            '"produto"."marca_produto_id"',
            '"vpmm"."menor_valor"',
            '(case when produto_filial.quantidade > 0 then 0 else 1 end) as estoque'
        ]);
        $query->distinct('produto.id');

        $caseWhen = count($relevancia);

        if ($caseWhen != 0) {
            $case = "(case when upper(produto.nome) like upper('" . $relevancia[0] . "%')";
            for ($i = 1; $i < $caseWhen; $i++) {
                $case .= " and upper(produto.nome) like upper('%" . $relevancia[$i] . "%')";
            }
            $case .= " then 0 else 1 end) as ordem_um";
        }

        $query->addSelect(["$case"])
            ->joinWith(['subcategoria.categoria',])
            ->addGroupBy(['produto.id', 'vpmm.menor_valor', 'produto_filial.quantidade'])
            ->innerJoin('produto_filial', 'produto_filial.produto_id = produto.id')
            ->innerJoin('valor_produto_menor_maior vpmm', 'vpmm.produto_id = produto.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'attributes' => [
                    'nome',
                    'preco' => [
                        'label' => 'Preço',
                        'asc' => [
                            'menor_valor' => SORT_ASC,
                        ],
                        'desc' => [
                            'menor_valor' => SORT_DESC,
                        ]
                    ]
                ]
            ]
        ]);

        $this->load($params, '');
        if (!$this->validate()) {

            return $dataProvider;
        }

        $query->andFilterWhere([
            'produto.id' => $this->id,
            'produto.peso' => $this->peso,
            'produto.altura' => $this->altura,
            'produto.largura' => $this->largura,
            'produto.profundidade' => $this->profundidade,
            'produto.fabricante_id' => $this->fabricante_id,
            'produto.subcategoria_id' => $this->subcategoria_id,
            'endereco_filial.cidade_id' => $this->cidade_id,
            'subcategoria.categoria_id' => $this->categoria_id,
            'produtoFilial.filial_id' => $this->filial_id,
            'cidade.estado_id' => $this->estado_id,
            'caracteristica.id' => $this->caracteristica_id,
        ]);

	//$query->orWhere(['like', 'produto.nome', ArrayHelper::getValue($params, 'nome')]);

        $this->searchNome($query);
        $dataProvider->totalCount = $query->count('DISTINCT "c"."id"');

        //echo "<pre>"; print_r($query); echo "</pre>"; die;

        return $dataProvider;
    }

    public function searchVazio($params)
    {

        $query = Produto::find()->addSelect([
            '"produto"."id"',
            '"produto"."nome"'
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'attributes' => [
                    'nome'
                ]
            ]
        ]);

        $query->andFilterWhere([
            'produto.id' => $this->id
        ]);

        return $dataProvider;
    }

    /**
     * @param $query ActiveQuery
     */
    private function searchNome(&$query)
    {
        //print_r($this->nome); die;
        if (!empty($this->nome)) {
            $textOr = $this->normalizeTermo($this->nome, ' | ');
            $textAnd = $this->normalizeTermo($this->nome);
            $codigo_global_filtro = "'%" . $this->nome . "%'";

            $id = str_replace("PA", "", $this->nome);
            $id = str_replace("P", "", $id);
            $id = str_replace("A", "", $id);
            $id = (int) $id;
            if ($id > 2147483647) {
                $id = 0;
            }

	//	print_r($this->nome);

            $query->addSelect([
                "ts_rank_cd(
                    texto_vetor,
                        to_tsquery('tsc_pt_unaccent',unaccent('$textOr'))
                ) as relevancia",
            ])
                ->andFilterWhere(['@@', 'produto.texto_vetor', new Expression("to_tsquery('tsc_pt_unaccent',unaccent('$textAnd'))")])
                ->orFilterWhere(['like', 'produto.codigo_global', new Expression($codigo_global_filtro)])
                ->orFilterWhere(['like', 'produto.codigo_fabricante', new Expression($codigo_global_filtro)])
		->orFilterWhere(['like', 'produto.codigo_fornecedor', new Expression($codigo_global_filtro)])
		->orFilterWhere(['like', 'produto.nome', $this->nome])
		->orFilterWhere(['like', 'upper(produto.nome)', strtoupper($this->nome)])
                ->orFilterWhere(['=', 'produto.id', $id])
                ->andWhere(['=', 'e_ativo', true])
                // ->andWhere(['>', 'produto_filial.quantidade', '0'])
                ->addOrderby(["estoque" => SORT_ASC])
                ->addOrderby(["ordem_um" => SORT_ASC])
                ->addOrderby(["vpmm.menor_valor" => SORT_ASC])
                ->addOrderby(["relevancia" => SORT_DESC]);

	    $termos = explode(" ", strtoupper($this->nome));
	    foreach($termos as $x => $termo){
		$query->orFilterWhere(['like', 'upper(produto.nome)', $termo]);
	    }
        }
    }

    public function searchMarcaModelo()
    {
        if ($marca = Marca::findOne($this->marca_id)) {
            $this->nome = $marca->nome;
            if ($modelo = Modelo::findOne($this->modelo_id)) {
                $this->nome .= ' ' . $modelo->nome;
            }
        }
    }

    private function normalizeTermo($texto, $glue = ' & ')
    {
        $termos = preg_replace('!\s+!', ' ', $texto);
        $termos = str_replace(['/', '(', ')', '|', '&', '!', '<?', '<', ':', '"', '\'\''], '', $termos);
        $termos = explode(' ', $termos);
        $lim = count($termos);
        for ($i = 0; $i < $lim; $i++) {
            if (trim($termos[$i])) {
                $termos[$i] .= ':*';
            } else {
                unset($termos[$i]);
            }
        }

        return implode($glue, $termos);
    }
}
