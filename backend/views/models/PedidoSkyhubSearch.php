<?php

namespace common\models;

use console\models\SkyhubClient;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/**
 * PedidoSearch represents the model behind the search form about `common\models\Pedido`.
 */
class PedidoSkyhubSearch extends PedidoSkyhub
{
    //ID do skyhub

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'canal', 'comprador', 'documento', 'status'], 'safe']
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
     * @return ArrayDataProvider
     */
    public function cloudSearch($params)
    {
        $page = isset($params['page2']) ? $params['page2'] : 1;

        $skyhub = new SkyhubClient();
        $response = $skyhub->orders()->findAll($page, 20);
        $pedidos = isset($response['orders']) ? $response['orders'] : [];

        $provider = new \yii\data\ArrayDataProvider(
            [
                'pagination' => [
                    'pageParam' => 'page2',
                    'pageSize' => 20,
                    'totalCount' => $response['total']
                ],
                'models' => $pedidos,
                'totalCount' => $response['total']
            ]
        );

        return $provider;

    }

    public function search($params)
    {
        $user = Usuario::findOne(\Yii::$app->user->id);

        $query = PedidoSkyhub::find()->where(['filial_id' => $user->filial_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageParam' => 'page2',
                'pageSize' => 20,
            ],
            'sort' => ['defaultOrder' =>
                [
                    'id' => SORT_DESC,
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['ilike', 'id', $this->id]);
        $query->andFilterWhere(['ilike', 'canal', $this->canal]);
        $query->andFilterWhere(['ilike', 'comprador', $this->comprador]);
        $query->andFilterWhere(['ilike', 'documento', $this->documento]);
        $query->andFilterWhere(['ilike', 'status', $this->status]);
        return $dataProvider;
    }
}