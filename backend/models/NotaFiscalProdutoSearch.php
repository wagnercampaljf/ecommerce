<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\NotaFiscalProduto;

/**
 * NotaFiscalProdutoSearch represents the model behind the search form about `backend\models\NotaFiscalProduto`.
 */
class NotaFiscalProdutoSearch extends NotaFiscalProduto
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nota_fiscal_id', 'aliquota_icms', 'qtd_comercial', 'qtd_tributavel'], 'integer'],
            [['valor_produto', 'cod_item', 'cod_produto', 'codigo_local_estoque', 'cmc_total', 'cmc_unitario', 'valor_desconto', 'valor_total_frete', 'valor_icms', 'outras_despesas', 'valor_unitario_tributacao'], 'number'],
            [['codigo_produto', 'descricao', 'pa_produto', 'cod_int_item', 'cod_int_produto', 'cod_fiscal_operacao_servico', 'cod_situacao_tributaria_icms', 'cod_ncm', 'ean', 'ean_tributável', 'codigo_produto_original', 'unid_tributavel', 'descricao_original'], 'safe'],
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
    public function search($params, $id)
    {
        $query = NotaFiscalProduto::find()->indexBy('id')->andFilterWhere([
            'nota_fiscal_id' => $id
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'nota_fiscal_id' => $this->nota_fiscal_id,
            'valor_produto' => $this->valor_produto,
            'cod_item' => $this->cod_item,
            'cod_produto' => $this->cod_produto,
            'codigo_local_estoque' => $this->codigo_local_estoque,
            'cmc_total' => $this->cmc_total,
            'cmc_unitario' => $this->cmc_unitario,
            'aliquota_icms' => $this->aliquota_icms,
            'qtd_comercial' => $this->qtd_comercial,
            'qtd_tributavel' => $this->qtd_tributavel,
            'valor_desconto' => $this->valor_desconto,
            'valor_total_frete' => $this->valor_total_frete,
            'valor_icms' => $this->valor_icms,
            'outras_despesas' => $this->outras_despesas,
            'valor_unitario_tributacao' => $this->valor_unitario_tributacao,
        ]);

        $query->andFilterWhere(['ilike', 'codigo_produto', $this->codigo_produto])
            ->andFilterWhere(['ilike', 'descricao', $this->descricao])
            ->andFilterWhere(['ilike', 'pa_produto', $this->pa_produto])
            ->andFilterWhere(['ilike', 'cod_int_item', $this->cod_int_item])
            ->andFilterWhere(['ilike', 'cod_int_produto', $this->cod_int_produto])
            ->andFilterWhere(['ilike', 'cod_fiscal_operacao_servico', $this->cod_fiscal_operacao_servico])
            ->andFilterWhere(['ilike', 'cod_situacao_tributaria_icms', $this->cod_situacao_tributaria_icms])
            ->andFilterWhere(['ilike', 'cod_ncm', $this->cod_ncm])
            ->andFilterWhere(['ilike', 'ean', $this->ean])
            ->andFilterWhere(['ilike', 'ean_tributável', $this->ean_tributável])
            ->andFilterWhere(['ilike', 'codigo_produto_original', $this->codigo_produto_original])
            ->andFilterWhere(['ilike', 'unid_tributavel', $this->unid_tributavel])
            ->andFilterWhere(['ilike', 'descricao_original', $this->descricao_original]);

        return $dataProvider;
    }

    public function searchValidadas($params, $numero_nf = null)
    {
        $query = (new \yii\db\Query())
            ->Select([
                'nota_fiscal_produto.id',
                'numero_nf',
                'codigo_produto_original',
                'pa_produto',
                'descricao',
                'valor_unitario_tributacao',
                'valor_icms',
                'valor_ipi',
                'valor_total_frete',
                'valor_desconto',
                'outras_despesas',
                'valor_seguro',
                'qtd_comercial'
            ])
            ->from("nota_fiscal_produto")
            ->innerJoin("nota_fiscal", "nota_fiscal.id = nota_fiscal_produto.nota_fiscal_id")
            ->where('nota_fiscal.finalidade_emissao <> 4 and nota_fiscal.cod_cliente not in (2641483458, 1018587858) and nota_fiscal.tipo_nf = 0 and nota_fiscal.e_validada = true')
            ->orderBy(['data_emissao' => SORT_DESC]);

        if ($numero_nf) {
            $query->andwhere('=', 'numero_nf', $numero_nf);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'nota_fiscal_id' => $this->nota_fiscal_id,
            'valor_produto' => $this->valor_produto,
            'cod_item' => $this->cod_item,
            'cod_produto' => $this->cod_produto,
            'codigo_local_estoque' => $this->codigo_local_estoque,
            'cmc_total' => $this->cmc_total,
            'cmc_unitario' => $this->cmc_unitario,
            'aliquota_icms' => $this->aliquota_icms,
            'qtd_comercial' => $this->qtd_comercial,
            'qtd_tributavel' => $this->qtd_tributavel,
            'valor_desconto' => $this->valor_desconto,
            'valor_total_frete' => $this->valor_total_frete,
            'valor_icms' => $this->valor_icms,
            'outras_despesas' => $this->outras_despesas,
            'valor_unitario_tributacao' => $this->valor_unitario_tributacao,
        ]);

        $query->andFilterWhere(['ilike', 'codigo_produto', $this->codigo_produto])
            ->andFilterWhere(['ilike', 'descricao', $this->descricao])
            ->andFilterWhere(['ilike', 'pa_produto', $this->pa_produto])
            ->andFilterWhere(['ilike', 'cod_int_item', $this->cod_int_item])
            ->andFilterWhere(['ilike', 'cod_int_produto', $this->cod_int_produto])
            ->andFilterWhere(['ilike', 'cod_fiscal_operacao_servico', $this->cod_fiscal_operacao_servico])
            ->andFilterWhere(['ilike', 'cod_situacao_tributaria_icms', $this->cod_situacao_tributaria_icms])
            ->andFilterWhere(['ilike', 'cod_ncm', $this->cod_ncm])
            ->andFilterWhere(['ilike', 'ean', $this->ean])
            ->andFilterWhere(['ilike', 'ean_tributável', $this->ean_tributável])
            ->andFilterWhere(['ilike', 'codigo_produto_original', $this->codigo_produto_original])
            ->andFilterWhere(['ilike', 'unid_tributavel', $this->unid_tributavel])
            ->andFilterWhere(['ilike', 'descricao_original', $this->descricao_original]);

        return $dataProvider;
    }
}
