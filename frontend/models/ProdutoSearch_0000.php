<?php

namespace frontend\models;

use common\models\Marca;
use common\models\Modelo;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;

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
        //if (isset($params['orcamento'])) {
        //    $quantidade = ($params['orcamento'] == 1 ? 'quantidade >= 0' : 'quantidade > 0');
        //} else {
            $quantidade = 'quantidade > 0';
        //}
        //$order_preco = (Yii::$app->params['isJuridica']() ? 'valor_cnpj' : 'valor');
	$order_preco = 'valor';
        $query = Produto::find()->addSelect([
	    '"produto"."id"',
            '"produto"."nome"',
            '"produto"."codigo_global"',
            '"produto"."aplicacao"',
            '"produto"."codigo_similar"',
            '"produto"."aplicacao_complementar"',
	    '"produto"."slug"',
            '"produto"."marca_produto_id"',
            "(case when upper(produto.nome) like upper('".ArrayHelper::getValue($params, 'nome')."%') then 0 else 1 end) as ordem_um",
            //'avg("produtoFilial"."' . $order_preco . '") as media_valor'
        ])->joinWith([
            'subcategoria.categoria',
        ])->addGroupBy(['produto.id'])
	->leftJoin('produto_filial','produto_filial.produto_id = produto.id')
        ->andWhere([">","produto_filial.quantidade","0"])
	->andWhere(["=","produto.e_ativo",true]);
        /*$subQuery = ProdutoFilial::find()
            ->select('produto_id, filial_id, valor_produtoFilial.' . $order_preco)
            ->andWhere($quantidade)
            ->innerJoin([
                'valor_produtoFilial' => ValorProdutoFilial::find()
                    ->select($order_preco . ', produto_filial_id')
                    ->andWhere('dt_inicio < now()')
                    ->andWhere('dt_fim > now() OR dt_fim IS NULL')
                    ->groupBy('produto_filial_id,' . $order_preco)
            ],
                '"valor_produtoFilial"."produto_filial_id" = "produto_filial"."id"')->lojistaAtivo();
        $query->innerJoin(['produtoFilial' => $subQuery], '"produtoFilial"."produto_id" = "produto"."id"');*/

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' =>51 ,

            ],
            'sort' => [
                'attributes' => [
                    'nome',
                    'preco' => [
                        'label' => 'Preço',
                        'asc' => [
                            'media_valor' => SORT_ASC,
                        ],
                        'desc' => [
                            'media_valor' => SORT_DESC,
                        ],


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
            'caracteristica.id' => $this->caracteristica_id
        ]);

        $preco = explode(',', $this->preco);
        $query->andFilterWhere(['between', 'produtoFilial.' . $order_preco, array_shift($preco), array_shift($preco)]);
        //$this->searchMarcaModelo();
        $this->searchNome($query);
        $dataProvider->totalCount = $query->count('DISTINCT "c"."id"');

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
                'pageSize' => 150,
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
        if (!empty($this->nome)) {
            $textOr = $this->normalizeTermo($this->nome, ' | ');
            $textAnd = $this->normalizeTermo($this->nome);
	        $codigo_global_filtro = "'%".$this->nome."%'";
	        
	        $id = str_replace("PA","",$this->nome);
	        $id = str_replace("P","",$id);
	        $id = str_replace("A","",$id);
	        $id = (int) $id;
	        if($id > 2147483647){
	            $id = 0;
	        }
	        

            $query->addSelect([
                "ts_rank_cd(
                    texto_vetor,
                        to_tsquery('tsc_pt_unaccent',unaccent('$textOr'))
                ) as relevancia",
                /*"ts_headline(
                    produto.nome,
                    to_tsquery('tsc_pt_unaccent',unaccent('$textOr'))
                ) as nome_search",
                "ts_headline(
                    produto.codigo_global,
                    to_tsquery('tsc_pt_unaccent',unaccent('$textOr'))
                ) as codigo_search",
                "ts_headline(
                    produto.aplicacao,
                    to_tsquery('tsc_pt_unaccent',unaccent('$textOr'))
                ) as aplicacao_search",
                "ts_headline(
                    produto.aplicacao_complementar,
                    to_tsquery('tsc_pt_unaccent',unaccent('$textOr'))
                ) as complementar_search",
                "ts_headline(
                    produto.codigo_similar,
                    to_tsquery('tsc_pt_unaccent',unaccent('$textOr'))
                ) as similar_search",
		"ts_headline(
                    produto.codigo_fabricante,
                    to_tsquery('tsc_pt_unaccent',unaccent('$textOr'))
                ) as codigo_fabricante_search",*/
            ])
            ->andFilterWhere(['@@', 'produto.texto_vetor', new Expression("to_tsquery('tsc_pt_unaccent',unaccent('$textAnd'))")])
            ->orFilterWhere(['like', 'produto.codigo_global', new Expression($codigo_global_filtro)])
            ->orFilterWhere(['=','produto.id',$id])
            ->addOrderby(["relevancia" => SORT_DESC]);
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
        $termos = str_replace(['/','(', ')','|','&','!','<?','<',':','"','\'\''], '', $termos);
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
