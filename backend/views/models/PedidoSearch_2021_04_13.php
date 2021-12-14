<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Pedido;

/**
 * PedidoSearch represents the model behind the search form about `common\models\Pedido`.
 */
class PedidoSearch extends Pedido
{
    public $Comprador;
    public $Vendedor;
    public $valor_produto;
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'comprador_id', 'filial_id', 'transportadora_id', 'forma_pagamento_id'], 'integer'],
            [['valor_total', 'valor_frete'], 'number'],
            [['dt_referencia', 'token_moip'], 'safe'],
            [['Comprador', 'Vendedor', 'valor_produto', 'status'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'Nº Do Pedido',
            'valor_total' => 'Valor Total',
            'dt_referencia' => 'Data de Referência',
            'comprador_id' => 'Comprador ID',
            'filial_id' => 'Filial ID',
            'transportadora_id' => 'Transportadora ID',
            'valor_frete' => 'Valor Frete',
            'forma_pagamento_id' => 'Forma Pagamento ID',
            'token_moip' => 'Token MOIP',
            'valor_produto' => 'Valor Produto',
            'status' => 'Status',
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
        $query = Pedido::find()->joinWith(['statusPedidos', 'statusPedidos.tipoStatus', 'comprador', 'filial', 'comprador.empresa']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' =>
                [
                    'id' => SORT_DESC,
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pedido.id' => $this->id,
            'comprador_id' => $this->comprador_id,
            'filial_id' => $this->filial_id,
            'transportadora_id' => $this->transportadora_id,
            'valor_frete' => $this->valor_frete,
            'forma_pagamento_id' => $this->forma_pagamento_id,
            'valor_total' => $this->valor_produto,
        ]);

        if(!is_null($params["PedidoSearch"]["id"])){

            $filtro = strtoupper($params["PedidoSearch"]["id"]);

            $query->andFilterWhere(['like', 'token_moip', $this->token_moip]);
            $query->andFilterWhere(['like', 'empresa.nome', $this->Comprador]);
            $query->andFilterWhere(['like', 'filial.nome', $this->Vendedor]);
            $query->andFilterWhere(['like', 'tipo_status_pedido.nome', $this->status]);
        }



        
        return $dataProvider;
    }
}