<?php
//123
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
        
        $query = PedidoMercadoLivre::find()
->join("LEFT JOIN", "pedido_mercado_livre_produto", " pedido_mercado_livre_produto.pedido_mercado_livre_id = pedido_mercado_livre.id ")
->join("LEFT JOIN", "pedido_mercado_livre_produto_produto_filial", " pedido_mercado_livre_produto.id = pedido_mercado_livre_produto_produto_filial.pedido_mercado_livre_produto_id ")
->join("LEFT JOIN", "produto_filial", " pedido_mercado_livre_produto_produto_filial.produto_filial_id = produto_filial.id ")
->join("LEFT JOIN", "produto", " produto_filial.produto_id = produto.id ")
->join("LEFT JOIN", "nota_fiscal", " pedido_mercado_livre.nota_fiscal_id = nota_fiscal.id ")
->orderBy(["id" => SORT_DESC]);

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

        $query->andFilterWhere(['like', 'pedido_meli_id', rtrim($this->pedido_meli_id)])
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
        
            //var_dump($params["PedidoMercadoLivreSearch"]); die;
            
            if(!is_null($params["PedidoMercadoLivreSearch"]["pedido_meli_id"])){

                $filtro = rtrim(strtoupper($params["PedidoMercadoLivreSearch"]["pedido_meli_id"]));

                $query->orWhere(['like', 'upper(buyer_first_name)', $filtro]);
                $query->orWhere(['like', 'upper(buyer_last_name)', $filtro]);
                $query->orWhere(['like', 'upper(receiver_name)', $filtro]);
		$query->orWhere(['like', 'upper(buyer_doc_number)', str_replace(" ","",str_replace("-", "", str_replace(".", "", str_replace("/", "", $filtro))))]);

		$query->orWhere(['like', 'upper(title)', $filtro]);
		$query->orWhere(['like', 'upper(codigo_fabricante)', str_replace(" ","",$filtro)]);
		//echo $filtro; die;
		$query->orWhere(['like', 'upper(pedido_mercado_livre_produto_produto_filial.email)', $filtro]);

		$query->orWhere(['=', 'chave_nf', str_replace(" ","",$filtro)]);

		$filtro_pa = str_replace("P", "", str_replace("A", "", str_replace(" ","",$filtro)));
		if(strlen($filtro_pa) <= 8 ){
			$filtro_pa = (int) $filtro_pa;
			if(is_int($filtro_pa)){
				$query->orWhere(['=', 'produto.id', $filtro_pa]);
			}
		}
            }
            
            if(isset($params["PedidoMercadoLivreSearch"]['e_xml_subido'])){
                $query->andWhere(['=', 'e_xml_subido', $params["PedidoMercadoLivreSearch"]['e_xml_subido']]);
            }

	if(isset($params["PedidoMercadoLivreSearch"]['e_tela_expedicao'])){
                if($params["PedidoMercadoLivreSearch"]['e_tela_expedicao']){
                    $data_filtro = date('Y-m-d H:i:s', strtotime("-30 days",strtotime(date('Y-m-d H:i:s'))));
                    //print_r($data_filtro); die;
                    $query->andWhere(['>=', 'date_created', $data_filtro]);
                }
        }

        if(isset($params["PedidoMercadoLivreSearch"]['e_pedido_cancelado'])){

            $filtro_status_cancelado = strtoupper($params["PedidoMercadoLivreSearch"]["e_pedido_cancelado"]);

            $query->andWhere(['=', 'e_pedido_cancelado', $params["PedidoMercadoLivreSearch"]['e_pedido_cancelado']]);
            //echo "<pre>"; print_r($params); echo "</pre>";

        }

	if(isset($params["PedidoMercadoLivreSearch"]['e_pedido_autorizado'])){

            $filtro_status_autorizado = strtoupper($params["PedidoMercadoLivreSearch"]["e_pedido_autorizado"]);

            $query->andWhere(['=', 'e_pedido_autorizado', $params["PedidoMercadoLivreSearch"]['e_pedido_autorizado']]);
            //echo "<pre>"; print_r($params); echo "</pre>";

        }

        if(isset($params["PedidoMercadoLivreSearch"]['e_pedido_faturado'])){

            $filtro_status_etiqueta_impressa = strtoupper($params["PedidoMercadoLivreSearch"]["e_pedido_faturado"]);

            $query->andWhere(['=', 'e_etiqueta_impressa', $params["PedidoMercadoLivreSearch"]['e_pedido_faturado']]);
            // echo "<pre>"; print_r($params); echo "</pre>";

        }
        //var_dump($params["PedidoMercadoLivreSearch"]['filtro_status_pedido_enviado']);
        //var_dump($params["PedidoMercadoLivreSearch"]['filtro_status_pedido_nao_enviado']);
        if(isset($params["PedidoMercadoLivreSearch"]['filtro_status_pedido_enviado']) && isset($params["PedidoMercadoLivreSearch"]['filtro_status_pedido_nao_enviado'])) {
             $filtro_enviado = (bool)$params["PedidoMercadoLivreSearch"]['filtro_status_pedido_enviado'];
             $filtro_nao_enviado = (bool)$params["PedidoMercadoLivreSearch"]['filtro_status_pedido_nao_enviado'];

             if ($filtro_enviado && $filtro_nao_enviado) {

             } elseif ($filtro_enviado && (!$filtro_nao_enviado)) {
                 $query->andWhere(['=', 'e_pedido_enviado', true]);
             } elseif ((!$filtro_enviado) && $filtro_nao_enviado) {
                 $query->andWhere(['=', 'e_pedido_enviado', false]);
             }
        }
	elseif(!isset($params["PedidoMercadoLivreSearch"]['filtro_status_pedido_enviado']) && isset($params["PedidoMercadoLivreSearch"]['filtro_status_pedido_nao_enviado'])){
	     $query->andWhere(['=', 'e_pedido_enviado', false]);
	}
	elseif(isset($params["PedidoMercadoLivreSearch"]['filtro_status_pedido_enviado']) && !isset($params["PedidoMercadoLivreSearch"]['filtro_status_pedido_nao_enviado'])){
	     $query->andWhere(['=', 'e_pedido_enviado', true]);
	}

        /*if(isset($params["PedidoMercadoLivreSearch"]['filtro_status_pedido_nao_enviado'])) {

            $filtro_enviado = (bool)$params["PedidoMercadoLivreSearch"]['filtro_status_pedido_enviado'];
            $filtro_nao_enviado = (bool)$params["PedidoMercadoLivreSearch"]['filtro_status_pedido_nao_enviado'];

            //var_dump($filtro_enviado);
            //var_dump($filtro_nao_enviado);

            if ($filtro_enviado && $filtro_nao_enviado) {
            } elseif ($filtro_enviado && (!$filtro_nao_enviado)) {
                $query->andWhere(['=', 'e_pedido_enviado', true]);
            } elseif ((!$filtro_enviado) && $filtro_nao_enviado) {
                $query->andWhere(['=', 'e_pedido_enviado', false]);
            }
        }*/



        if(isset($params["PedidoMercadoLivreSearch"]['filtro_status_etiqueta_impressa'])) {

            $filtro_etiqueta_impressa = (bool)$params["PedidoMercadoLivreSearch"]['filtro_status_etiqueta_impressa'];
            $filtro_etiqueta_nao_impressa = (bool)$params["PedidoMercadoLivreSearch"]['filtro_status_etiqueta_nao_impressa'];

            // var_dump($filtro_etiqueta_impressa);
            //var_dump($filtro_etiqueta_nao_impressa);


            if ($filtro_etiqueta_impressa && $filtro_etiqueta_nao_impressa) {


            } elseif ($filtro_etiqueta_impressa && (!$filtro_etiqueta_nao_impressa)) {
                $query->andWhere(['=', 'e_etiqueta_impressa', true]);


            } elseif ((!$filtro_etiqueta_impressa) && $filtro_etiqueta_nao_impressa) {
                $query->andWhere(['=', 'e_etiqueta_impressa', false]);

            }
        }


        if(isset($params["PedidoMercadoLivreSearch"]['filtro_status_etiqueta_nao_impressa'])) {

            $filtro_etiqueta_impressa = (bool)$params["PedidoMercadoLivreSearch"]['filtro_status_etiqueta_impressa'];
            $filtro_etiqueta_nao_impressa = (bool)$params["PedidoMercadoLivreSearch"]['filtro_status_etiqueta_nao_impressa'];

            // var_dump($filtro_etiqueta_impressa);
            //var_dump($filtro_etiqueta_nao_impressa);


            if ($filtro_etiqueta_impressa && $filtro_etiqueta_nao_impressa) {


            } elseif ($filtro_etiqueta_impressa && (!$filtro_etiqueta_nao_impressa)) {
                $query->andWhere(['=', 'e_etiqueta_impressa', true]);


            } elseif ((!$filtro_etiqueta_impressa) && $filtro_etiqueta_nao_impressa) {
                $query->andWhere(['=', 'e_etiqueta_impressa', false]);

            }
        }

	if(isset($params["PedidoMercadoLivreSearch"]['data_inicial'])){
            if(!is_null($params["PedidoMercadoLivreSearch"]['data_inicial']) && $params["PedidoMercadoLivreSearch"]['data_inicial'] != ""){
		$data_incial_formatada = substr($params["PedidoMercadoLivreSearch"]['data_inicial'], 6, 4)."-".
					 substr($params["PedidoMercadoLivreSearch"]['data_inicial'], 3, 2)."-".
					 substr($params["PedidoMercadoLivreSearch"]['data_inicial'], 0, 2)." 00:00:00";
                $query->andWhere(['>=', 'data_hora_autorizacao', $data_incial_formatada]);
            }
        }
            
        if(isset($params["PedidoMercadoLivreSearch"]['data_final'])){
            if(!is_null($params["PedidoMercadoLivreSearch"]['data_final']) && $params["PedidoMercadoLivreSearch"]['data_final'] != ""){
		$data_final_formatada = substr($params["PedidoMercadoLivreSearch"]['data_final'], 6, 4)."-".
                                        substr($params["PedidoMercadoLivreSearch"]['data_final'], 3, 2)."-".
                                        substr($params["PedidoMercadoLivreSearch"]['data_final'], 0, 2)." 23:59:59";
                $query->andWhere(['<=', 'data_hora_autorizacao', $data_final_formatada]);
            }
        }

        if(isset($params["PedidoMercadoLivreSearch"]['e_pedido_cancelado'])){

            $query->andWhere(['=', 'e_pedido_cancelado', $params["PedidoMercadoLivreSearch"]['e_pedido_cancelado']]);
            // echo "<pre>"; print_r($params); echo "</pre>";

	    if(!$params["PedidoMercadoLivreSearch"]['e_pedido_cancelado']){
		$query->andWhere(['<>', 'status', "cancelled"]);
	    }

        }

        if(isset($params["PedidoMercadoLivreSearch"]['e_pedido_enviado'])){

            $query->andWhere(['=', 'e_pedido_enviado', $params["PedidoMercadoLivreSearch"]['e_pedido_enviado']]);
            // echo "<pre>"; print_r($params); echo "</pre>";

        }

	if(isset($params["PedidoMercadoLivreSearch"]['e_mercado_livre_principal']) && isset($params["PedidoMercadoLivreSearch"]['e_mercado_livre_filial']) && isset($params["PedidoMercadoLivreSearch"]['e_mercado_livre_mg4'])){

            if($params["PedidoMercadoLivreSearch"]['e_mercado_livre_principal'] && !$params["PedidoMercadoLivreSearch"]['e_mercado_livre_filial'] && !$params["PedidoMercadoLivreSearch"]['e_mercado_livre_mg4']){
                $query->andWhere(['=', 'user_id', '193724256']);
            }
            if(!$params["PedidoMercadoLivreSearch"]['e_mercado_livre_principal'] && $params["PedidoMercadoLivreSearch"]['e_mercado_livre_filial'] && !$params["PedidoMercadoLivreSearch"]['e_mercado_livre_mg4']){
                $query->andWhere(['=', 'user_id', '435343067']);
            }
	    if(!$params["PedidoMercadoLivreSearch"]['e_mercado_livre_principal'] && !$params["PedidoMercadoLivreSearch"]['e_mercado_livre_filial'] && $params["PedidoMercadoLivreSearch"]['e_mercado_livre_mg4']){
                $query->andWhere(['=', 'user_id', '195972862']);
            }
        }

	if(isset($params["PedidoMercadoLivreSearch"]['e_pedido_mercado_envios'])){

            if($params["PedidoMercadoLivreSearch"]['e_pedido_mercado_envios']){
                $query->andWhere(['<>', 'shipping_id', '']);
            }
            else{
                $query->andWhere(['=', 'shipping_id', '']);
            }
        }

//echo "<pre>"; print_r($params["PedidoMercadoLivreSearch"]); echo "</pre>"; die;
	if(isset($params["PedidoMercadoLivreSearch"]['e_mail'])){
		$query->andWhere(['like', 'pedido_mercado_livre_produto_produto_filial.email', $params["PedidoMercadoLivreSearch"]['e_mail']]);
        }

	if(isset($params["PedidoMercadoLivreSearch"]['e_apenas_nao_impressos'])){
            if(!is_null($params["PedidoMercadoLivreSearch"]['e_apenas_nao_impressos'])){
                if($params["PedidoMercadoLivreSearch"]['e_apenas_nao_impressos']){
                    $query->andWhere(['=', 'e_pre_nota_impressa', false]);
                }
            }
        }

	$dataProvider->pagination = ['pageSize' => 15];
   
        return $dataProvider;
    }
}
