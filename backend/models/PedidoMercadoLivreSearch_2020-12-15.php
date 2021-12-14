<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PedidoMercadoLivre;

/**
 * PedidoMercadoLivreSearch represents the model behind the search form about `common\models\PedidoMercadoLivre`.
 */
class PedidoMercadoLivreSearch extends PedidoMercadoLivre
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['pedido_meli_id', 'date_created', 'date_closed', 'last_updated', 'shipping_id', 'status', 'buyer_id', 'buyer_nickname', 'buyer_email', 'buyer_first_name', 'buyer_last_name', 'buyer_doc_type', 'buyer_doc_number', 'shipping_status', 'shipping_substatus', 'shipping_date_created', 'shipping_last_updated', 'shipping_tracking_number', 'shipping_tracking_method', 'shipping_service_id', 'receiver_id', 'receiver_address_id', 'receiver_address_line', 'receiver_street_name', 'receiver_street_number', 'receiver_comment', 'receiver_zip_code', 'receiver_city_id', 'receiver_city_name', 'receiver_state_id', 'receiver_state_name', 'receiver_country_id', 'receiver_country_name', 'receiver_neighborhood_id', 'receiver_neighborhood_name', 'receiver_municipality_id', 'receiver_municipality_name', 'receiver_delivery_preference', 'receiver_name', 'receiver_phone', 'shipping_option_id', 'shipping_option_shipping_method_id', 'shipping_option_name', 'shipping_option_delivery_type', 'user_id'], 'safe'],
            [['total_amount', 'paid_amount', 'shipping_base_cost', 'shipping_option_list_cost', 'shipping_option_cost'], 'number'],
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
        //echo "<pre>"; print_r($params); echo "</pre>"; die;
        
        $query = PedidoMercadoLivre::find()->orderBy(["id" => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_created' => $this->date_created,
            'date_closed' => $this->date_closed,
            'last_updated' => $this->last_updated,
            'total_amount' => $this->total_amount,
            'paid_amount' => $this->paid_amount,
            'shipping_base_cost' => $this->shipping_base_cost,
            'shipping_date_created' => $this->shipping_date_created,
            'shipping_last_updated' => $this->shipping_last_updated,
            'shipping_option_list_cost' => $this->shipping_option_list_cost,
            'shipping_option_cost' => $this->shipping_option_cost,
        ]);

        $query->andFilterWhere(['like', 'pedido_meli_id', $this->pedido_meli_id])
            ->andFilterWhere(['like', 'shipping_id', $this->shipping_id])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'buyer_id', $this->buyer_id])
            ->andFilterWhere(['like', 'buyer_nickname', $this->buyer_nickname])
            ->andFilterWhere(['like', 'buyer_email', $this->buyer_email])
            ->andFilterWhere(['like', 'buyer_first_name', $this->buyer_first_name])
            ->andFilterWhere(['like', 'buyer_last_name', $this->buyer_last_name])
            ->andFilterWhere(['like', 'buyer_doc_type', $this->buyer_doc_type])
            ->andFilterWhere(['like', 'buyer_doc_number', $this->buyer_doc_number])
            ->andFilterWhere(['like', 'shipping_status', $this->shipping_status])
            ->andFilterWhere(['like', 'shipping_substatus', $this->shipping_substatus])
            ->andFilterWhere(['like', 'shipping_tracking_number', $this->shipping_tracking_number])
            ->andFilterWhere(['like', 'shipping_tracking_method', $this->shipping_tracking_method])
            ->andFilterWhere(['like', 'shipping_service_id', $this->shipping_service_id])
            ->andFilterWhere(['like', 'receiver_id', $this->receiver_id])
            ->andFilterWhere(['like', 'receiver_address_id', $this->receiver_address_id])
            ->andFilterWhere(['like', 'receiver_address_line', $this->receiver_address_line])
            ->andFilterWhere(['like', 'receiver_street_name', $this->receiver_street_name])
            ->andFilterWhere(['like', 'receiver_street_number', $this->receiver_street_number])
            ->andFilterWhere(['like', 'receiver_comment', $this->receiver_comment])
            ->andFilterWhere(['like', 'receiver_zip_code', $this->receiver_zip_code])
            ->andFilterWhere(['like', 'receiver_city_id', $this->receiver_city_id])
            ->andFilterWhere(['like', 'receiver_city_name', $this->receiver_city_name])
            ->andFilterWhere(['like', 'receiver_state_id', $this->receiver_state_id])
            ->andFilterWhere(['like', 'receiver_state_name', $this->receiver_state_name])
            ->andFilterWhere(['like', 'receiver_country_id', $this->receiver_country_id])
            ->andFilterWhere(['like', 'receiver_country_name', $this->receiver_country_name])
            ->andFilterWhere(['like', 'receiver_neighborhood_id', $this->receiver_neighborhood_id])
            ->andFilterWhere(['like', 'receiver_neighborhood_name', $this->receiver_neighborhood_name])
            ->andFilterWhere(['like', 'receiver_municipality_id', $this->receiver_municipality_id])
            ->andFilterWhere(['like', 'receiver_municipality_name', $this->receiver_municipality_name])
            ->andFilterWhere(['like', 'receiver_delivery_preference', $this->receiver_delivery_preference])
            ->andFilterWhere(['like', 'receiver_name', $this->receiver_name])
            ->andFilterWhere(['like', 'receiver_phone', $this->receiver_phone])
            ->andFilterWhere(['like', 'shipping_option_id', $this->shipping_option_id])
            ->andFilterWhere(['like', 'shipping_option_shipping_method_id', $this->shipping_option_shipping_method_id])
            ->andFilterWhere(['like', 'shipping_option_name', $this->shipping_option_name])
            ->andFilterWhere(['like', 'shipping_option_delivery_type', $this->shipping_option_delivery_type])
            ->andFilterWhere(['like', 'user_id', $this->user_id]);
        
        //FILTROS
            if(!is_null($params["PedidoMercadoLivreSearch"]["pedido_meli_id"])){
                
                $filtro = strtoupper($params["PedidoMercadoLivreSearch"]["pedido_meli_id"]);
                
                $query->orWhere(['like', 'upper(buyer_first_name)', $filtro]);
                $query->orWhere(['like', 'upper(buyer_last_name)', $filtro]);
                $query->orWhere(['like', 'upper(receiver_name)', $filtro]);
            }
            

        return $dataProvider;
    }
}
