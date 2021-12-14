<?php

/**
 * @author Igor Mageste
 */

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class ProdutoSearch extends Produto
{

    private $atributos = [
        'ano_id' => '"ano_modelo"."id"',
        'codigo_global' => '"produto"."codigo_global"',
        'categoria_id' => '"subcategoria"."categoria_id"',
        'categoria_modelo_id' => '"modelo"."categoria_modelo_id"',
        'cidade_id' => '"endereco_filial"."cidade_id"',
        'marca_id' => '"modelo"."marca_id"',
        'modelo_id' => '"modelo"."id"',
        'nome' => '"produto"."nome"',
        'subCategoria_id' => '"produto"."subcategoria_id"',
        'fabricante_id' => '"fabricante"."id"',
    ];

    public function search($queryParams = array())
    {
        $query = $this->find();

        $query->joinWith([
            'subcategoria.categoria',
            'fabricante',
            'filiaisProduto.filial.enderecoFilial',
        ], false, 'LEFT OUTER JOIN');
        $order_preco = (Yii::$app->params['isJuridica']() ? 'valor_cnpj' : 'valor');
        $subQuery = ProdutoFilial::find()
            ->select('produto_id, filial_id, valor_produtoFilial.' . $order_preco)
            ->andWhere('quantidade > 0')
            ->innerJoin([
                'valor_produtoFilial' => ValorProdutoFilial::find()
                    ->select($order_preco . ', produto_filial_id')
                    ->andWhere('dt_inicio < now()')
                    ->andWhere('dt_fim > now() OR dt_fim IS NULL')
                    ->groupBy('produto_filial_id,' . $order_preco)
            ], '"valor_produtoFilial"."produto_filial_id" = "produto_filial"."id"');
        $query->innerJoin(['produtoFilial' => $subQuery], '"produtoFilial"."produto_id" = "produto"."id"');

        foreach ($queryParams as $param => $val) {
            switch ($param) {
                case 'nome':
                    if (!empty($val)) {
                        $text = $this->normalizaTermo($val, ' | ');
                        $query->addSelect([
                            "ts_rank_cd(
                                texto_vetor,
                                to_tsquery('tsc_pt_unaccent','$text')
                            ) as relevancia",
                            "ts_headline(
                                produto.nome,
                                to_tsquery('tsc_pt_unaccent','$text')
                            ) as nome_search",
                            "ts_headline(
                                produto.codigo_global,
                                to_tsquery('tsc_pt_unaccent','$text')
                            ) as codigo_search",
                            "ts_headline(
                                produto.aplicacao,
                                to_tsquery('tsc_pt_unaccent','$text')
                            ) as aplicacao_search",
                        ]);
                        $text = $this->normalizaTermo($val, ' & ');
                        $query->andFilterWhere([
                            '@@',
                            "texto_vetor",
                            new Expression("to_tsquery('tsc_pt_unaccent','$text')")
                        ]);
                        $query->addOrderBy([
                            "relevancia" => SORT_DESC,
                        ]);
                    }
                    break;
                case 'codigo_global':
                    $query->orFilterWhere([
                        'like',
                        'LOWER(' . $this->atributos[$param] . ')',
                        strtolower(substr($val, 1))
                    ]);
                    break;
                default:
                    if (isset($this->atributos[$param]) && $val != '') {
                        $query->andFilterWhere(['=', $this->atributos[$param], $val]);
                    }
                    break;
            }
        }

        $order_preco = (Yii::$app->params['isJuridica']() ? 'valor_cnpj' : 'valor');
        $query->addSelect([
            '"produto".*',
            'avg("produtoFilial"."' . $order_preco . '") as media_valor'
        ]);
        $query->addGroupBy(['produto.id']);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'totalCount' => $query->count('DISTINCT "c"."id"'),
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
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function normalizaTermo($texto, $glue = '&')
    {
        $termos = trim($texto);
        $termos = preg_replace('/[^a-zA-Z0-9\s]/', '', $termos);
        $termos = preg_replace('!\s+!', ' ', $termos);
        $termos = explode(' ', $termos);
        $lim = count($termos);
        for ($i = 0; $i < $lim; $i++) {
            $termos[$i] .= ':*';
        }

        return implode($glue, $termos);
    }
}
