<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\NotaFiscal;

/**
 * NotaFiscalSearch represents the model behind the search form about `backend\models\NotaFiscal`.
 */
class NotaFiscalSearch extends NotaFiscal
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_transportadora', 'finalizade_emissao', 'tipo_nf', 'tipo_ambiente', 'serie', 'codigo_modelo', 'indice_pagamento', 'cod_int_empresa', 'cod_empresa', 'cod_int_cliente_fornecedor', 'cod_cliente'], 'integer'],
            [['chave_nf', 'data_nf', 'modo_frete', 'data_cancelamento', 'data_emissao', 'data_inutilizacao', 'data_registro', 'data_saida', 'h_saida_entrada_nf', 'h_emissao'], 'safe'],
            [['valor_nf', 'id_nf', 'id_pedido', 'numero_nf', 'id_recebimento'], 'number'],
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
    public function search($params, $validada = 'false')
    {
        $query = NotaFiscal::find()->where("finalidade_emissao <> 4 and cod_cliente not in (2641483458, 1018587858) and tipo_nf = 0 and e_validada = $validada");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['data_nf' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'valor_nf' => $this->valor_nf,
            'data_nf' => $this->data_nf,
            'id_nf' => $this->id_nf,
            'id_pedido' => $this->id_pedido,
            'numero_nf' => $this->numero_nf,
            'id_recebimento' => $this->id_recebimento,
            'id_transportadora' => $this->id_transportadora,
            'data_cancelamento' => $this->data_cancelamento,
            'data_emissao' => $this->data_emissao,
            'data_inutilizacao' => $this->data_inutilizacao,
            'data_registro' => $this->data_registro,
            'data_saida' => $this->data_saida,
            'finalizade_emissao' => $this->finalizade_emissao,
            'tipo_nf' => $this->tipo_nf,
            'tipo_ambiente' => $this->tipo_ambiente,
            'serie' => $this->serie,
            'codigo_modelo' => $this->codigo_modelo,
            'indice_pagamento' => $this->indice_pagamento,
            'h_saida_entrada_nf' => $this->h_saida_entrada_nf,
            'h_emissao' => $this->h_emissao,
            'cod_int_empresa' => $this->cod_int_empresa,
            'cod_empresa' => $this->cod_empresa,
            'cod_int_cliente_fornecedor' => $this->cod_int_cliente_fornecedor,
            'cod_cliente' => $this->cod_cliente,
        ]);

        $query->andFilterWhere(['ilike', 'chave_nf', $this->chave_nf])
            ->andFilterWhere(['ilike', 'modo_frete', $this->modo_frete]);

        return $dataProvider;
    }

    public function searchNotasPedido($params, $id = null)
    {
        $query = NotaFiscal::find()->where('finalidade_emissao <> 4 and cod_cliente <> 2641483458 and tipo_nf = 0 and e_validada = true');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['data_nf' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'valor_nf' => $this->valor_nf,
            'data_nf' => $this->data_nf,
            'id_nf' => $this->id_nf,
            'id_pedido' => $this->id_pedido,
            'numero_nf' => $this->numero_nf,
            'id_recebimento' => $this->id_recebimento,
            'id_transportadora' => $this->id_transportadora,
            'data_cancelamento' => $this->data_cancelamento,
            'data_emissao' => $this->data_emissao,
            'data_inutilizacao' => $this->data_inutilizacao,
            'data_registro' => $this->data_registro,
            'data_saida' => $this->data_saida,
            'finalizade_emissao' => $this->finalizade_emissao,
            'tipo_nf' => $this->tipo_nf,
            'tipo_ambiente' => $this->tipo_ambiente,
            'serie' => $this->serie,
            'codigo_modelo' => $this->codigo_modelo,
            'indice_pagamento' => $this->indice_pagamento,
            'h_saida_entrada_nf' => $this->h_saida_entrada_nf,
            'h_emissao' => $this->h_emissao,
            'cod_int_empresa' => $this->cod_int_empresa,
            'cod_empresa' => $this->cod_empresa,
            'cod_int_cliente_fornecedor' => $this->cod_int_cliente_fornecedor,
            'cod_cliente' => $this->cod_cliente,
        ]);

        $query->andFilterWhere(['ilike', 'chave_nf', $this->chave_nf])
            ->andFilterWhere(['ilike', 'modo_frete', $this->modo_frete]);

        return $dataProvider;
    }
}
