<?php

namespace lojista\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * PedidoSearch represents the model behind the search form about `common\models\Pedido`.
 */
class PedidoSearch extends Pedido
{
    public $Comprador;
    public $Vendedor;
    public $valor_produto;
    public $status;
    public $documento;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'comprador_id', 'filial_id', 'transportadora_id', 'forma_pagamento_id'], 'integer'],
            [
                ['valor_total', 'valor_frete'],
                'number',
                'message' => '"{attribute}" deve ser um número (Substitua a vírgula por ponto)'
            ],
            [['Comprador', 'Vendedor', 'valor_produto', 'status', 'documento'], 'safe'],
            [['data_prevista', 'dt_referencia'], 'date', 'format' => 'dd/mm/yyyy']
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
        $query = Pedido::find()->joinWith([
            'statusPedidos.tipoStatus',
            'filial',
            'comprador.empresa'
        ])->andWhere(['filial_id' => Yii::$app->user->identity->filial_id]);


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
            'pedido.valor_total' => $this->valor_total,
            'pedido.comprador_id' => $this->comprador_id,
            'pedido.filial_id' => $this->filial_id,
            'pedido.transportadora_id' => $this->transportadora_id,
            'pedido.valor_frete' => $this->valor_frete,
            'pedido.forma_pagamento_id' => $this->forma_pagamento_id,
        ]);

        $query->andFilterWhere(['like', 'empresa.documento', $this->documento]);
        $query->andFilterWhere(['like', 'lower(tipo_status_pedido.nome)', strtolower($this->status)]);
        $query->andFilterWhere(['like', 'lower(empresa.nome)', strtolower($this->Comprador)]);

        $this->filterDates($query);

        return $dataProvider;
    }

    /**
     * @param $query ActiveQuery
     */
    private function filterDates(&$query)
    {
        if ($dt = date_create_from_format('d/m/Y', $this->dt_referencia)) {
            $query->andFilterWhere([
                'between',
                'data_referencia',
                $dt->format('Y-m-d 00:00:00'),
                $dt->format('Y-m-d 23:59:59')
            ]);
        }
        if ($dt = date_create_from_format('d/m/Y', $this->data_prevista)) {
            $query->andFilterWhere(['data_prevista' => $dt->format('Y-m-d')]);
        }
    }
}
