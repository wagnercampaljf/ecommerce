<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use console\controllers\actions\omie\Omie;

/**
 * Este é o model para a tabela "pedido_mercado_livre".
 *
 * @property integer $id
 * @property string $pedido_meli_id
 * @property string $date_created
 * @property string $date_closed
 * @property string $last_updated
 * @property string $total_amount
 * @property string $paid_amount
 * @property string $shipping_id
 * @property string $status
 * @property string $buyer_id
 * @property string $buyer_nickname
 * @property string $buyer_email
 * @property string $buyer_first_name
 * @property string $buyer_last_name
 * @property string $buyer_doc_type
 * @property string $buyer_doc_number
 * @property string $shipping_base_cost
 * @property string $shipping_status
 * @property string $shipping_substatus
 * @property string $shipping_date_created
 * @property string $shipping_last_updated
 * @property string $shipping_tracking_number
 * @property string $shipping_tracking_method
 * @property string $shipping_service_id
 * @property string $receiver_id
 * @property string $receiver_address_id
 * @property string $receiver_address_line
 * @property string $receiver_street_name
 * @property string $receiver_street_number
 * @property string $receiver_comment
 * @property string $receiver_zip_code
 * @property string $receiver_city_id
 * @property string $receiver_city_name
 * @property string $receiver_state_id
 * @property string $receiver_state_name
 * @property string $receiver_country_id
 * @property string $receiver_country_name
 * @property string $receiver_neighborhood_id
 * @property string $receiver_neighborhood_name
 * @property string $receiver_municipality_id
 * @property string $receiver_municipality_name
 * @property string $receiver_delivery_preference
 * @property string $receiver_name
 * @property string $receiver_phone
 * @property string $shipping_option_id
 * @property string $shipping_option_shipping_method_id
 * @property string $shipping_option_name
 * @property string $shipping_option_list_cost
 * @property string $shipping_option_cost
 * @property string $shipping_option_delivery_type
 * @property string $user_id
 * @property boolean $e_pedido_autorizado
 * @property boolean $e_pedido_cancelado
 * @property boolean $e_nota_fiscal_anexada
 * @property boolean $e_pedido_faturado
 * @property string $email_texto
 * @property string $email_enderecos
 * @property string $pack_id
 * @property string $email_assunto
 * @property boolean $e_xml_subido
 * @property boolean $e_etiqueta_impressa
 *
 * @property PedidoMercadoLivrePagamento[] $pedidoMercadoLivrePagamentos
 * @property PedidoMercadoLivreProduto[] $pedidoMercadoLivreProdutos
 *
 * @author Unknown 15/06/2020
 */
class PedidoMercadoLivre extends \yii\db\ActiveRecord
{
    
    const APP_ID = '3029992417140266';
    const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';
    
    /**
     * @inheritdoc
     * @author Unknown 15/06/2020
     */
    public static function tableName()
    {
        return 'pedido_mercado_livre';
    }

    /**
     * @inheritdoc
     * @author Unknown 15/06/2020
     */
    public function rules()
    {
        return [
            [['pedido_meli_id', 'total_amount'], 'required'],
            [['date_created', 'date_closed', 'last_updated', 'shipping_date_created', 'shipping_last_updated'], 'safe'],
            [['total_amount', 'paid_amount', 'shipping_base_cost', 'shipping_option_list_cost', 'shipping_option_cost'], 'number'],
            [['e_pedido_autorizado','e_pedido_cancelado', 'e_nota_fiscal_anexada', 'e_pedido_faturado', 'e_xml_subido', 'e_etiqueta_impressa'], 'boolean'],
            [['email_texto', 'email_assunto'], 'string'],
            [['comentario'], 'string'],
            [['pedido_meli_id', 'shipping_id', 'status', 'buyer_id', 'buyer_doc_type', 'shipping_service_id', 'receiver_id', 'receiver_address_id', 'receiver_street_number', 'receiver_zip_code', 'receiver_country_id', 'receiver_neighborhood_id', 'receiver_municipality_id', 'shipping_option_id', 'shipping_option_name', 'shipping_option_delivery_type', 'user_id'], 'string', 'max' => 20],
            [['buyer_nickname', 'buyer_email', 'buyer_first_name', 'buyer_last_name', 'receiver_address_line', 'receiver_street_name'], 'string', 'max' => 200],
            [['buyer_doc_number', 'shipping_tracking_number', 'receiver_state_name', 'receiver_country_name'], 'string', 'max' => 30],
            [['shipping_status'], 'string', 'max' => 40],
            [['shipping_substatus', 'receiver_comment', 'receiver_neighborhood_name'], 'string', 'max' => 100],
            [['shipping_tracking_method', 'receiver_city_id', 'receiver_state_id', 'receiver_municipality_name', 'receiver_delivery_preference'], 'string', 'max' => 50],
            [['receiver_city_name', 'receiver_name', 'pack_id'], 'string', 'max' => 60],
            [['receiver_phone'], 'string', 'max' => 15],
            [['shipping_option_shipping_method_id'], 'string', 'max' => 10],
            [['email_enderecos'], 'string', 'max' => 400]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 15/06/2020
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pedido_meli_id' => 'Pedido Meli ID',
            'date_created' => 'Date Created',
            'date_closed' => 'Date Closed',
            'last_updated' => 'Last Updated',
            'total_amount' => 'Total Amount',
            'paid_amount' => 'Paid Amount',
            'shipping_id' => 'Shipping ID',
            'status' => 'Status',
            'buyer_id' => 'Buyer ID',
            'buyer_nickname' => 'Buyer Nickname',
            'buyer_email' => 'Buyer Email',
            'buyer_first_name' => 'Buyer First Name',
            'buyer_last_name' => 'Buyer Last Name',
            'buyer_doc_type' => 'Buyer Doc Type',
            'buyer_doc_number' => 'Buyer Doc Number',
            'shipping_base_cost' => 'Shipping Base Cost',
            'shipping_status' => 'Shipping Status',
            'shipping_substatus' => 'Shipping Substatus',
            'shipping_date_created' => 'Shipping Date Created',
            'shipping_last_updated' => 'Shipping Last Updated',
            'shipping_tracking_number' => 'Shipping Tracking Number',
            'shipping_tracking_method' => 'Shipping Tracking Method',
            'shipping_service_id' => 'Shipping Service ID',
            'receiver_id' => 'Receiver ID',
            'receiver_address_id' => 'Receiver Address ID',
            'receiver_address_line' => 'Receiver Address Line',
            'receiver_street_name' => 'Receiver Street Name',
            'receiver_street_number' => 'Receiver Street Number',
            'receiver_comment' => 'Receiver Comment',
            'receiver_zip_code' => 'Receiver Zip Code',
            'receiver_city_id' => 'Receiver City ID',
            'receiver_city_name' => 'Receiver City Name',
            'receiver_state_id' => 'Receiver State ID',
            'receiver_state_name' => 'Receiver State Name',
            'receiver_country_id' => 'Receiver Country ID',
            'receiver_country_name' => 'Receiver Country Name',
            'receiver_neighborhood_id' => 'Receiver Neighborhood ID',
            'receiver_neighborhood_name' => 'Receiver Neighborhood Name',
            'receiver_municipality_id' => 'Receiver Municipality ID',
            'receiver_municipality_name' => 'Receiver Municipality Name',
            'receiver_delivery_preference' => 'Receiver Delivery Preference',
            'receiver_name' => 'Receiver Name',
            'receiver_phone' => 'Receiver Phone',
            'shipping_option_id' => 'Shipping Option ID',
            'shipping_option_shipping_method_id' => 'Shipping Option Shipping Method ID',
            'shipping_option_name' => 'Shipping Option Name',
            'shipping_option_list_cost' => 'Shipping Option List Cost',
            'shipping_option_cost' => 'Shipping Option Cost',
            'shipping_option_delivery_type' => 'Shipping Option Delivery Type',
            'user_id' => 'User ID',
            'e_pedido_autorizado' => 'E Pedido Autorizado',
            'e_pedido_cancelado' => 'E Pedido Cancelado',
            'e_nota_fiscal_anexada' => 'E Nota Fiscal Anexada',
            'e_pedido_faturado' => 'E Pedido Faturado',
            'email_texto' => 'Email Texto',
            'email_enderecos' => 'Email Enderecos',
            'comentario' => 'Comentario',
            'pack_id' => 'Pack ID',
            'e_pedido_cancelado' => 'E pedido cancelado',
            'email_assunto' => 'Email Assunto',
            'e_xml_subido' => "E XML Subido",
            'e_etiqueta_impressa' => "E etiqueta impressa",
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/06/2020
    */
    public function getPedidoMercadoLivrePagamentos()
    {
        return $this->hasMany(PedidoMercadoLivrePagamento::className(), ['pedido_mercado_livre_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/06/2020
    */
    public function getPedidoMercadoLivreProdutos()
    {
        return $this->hasMany(PedidoMercadoLivreProduto::className(), ['pedido_mercado_livre_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/06/2020
    */
    public static function find()
    {
        return new PedidoMercadoLivreQuery(get_called_class());
    }
    
    public static function baixarPedidoML($order = null)
    {
        
        $status_retorno = "";

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        
        $filial = Filial::find()->andWhere(['=','id',72])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;
        $user_id = "193724256";
        
        $url_order = "/orders/".$order;
        
        $response_order = $meli->get($url_order."?access_token=" . $meliAccessToken);
        
        if($response_order["httpCode"] >= 300){
            $filial_conta_duplicada = Filial::find()->andWhere(['=','id',98])->one();
            $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
            $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
            $meliAccessToken = $response_conta_duplicada->access_token;
            
            $response_order = $meli->get("/orders/".$order."?access_token=" . $meliAccessToken);
            
            if($response_order["httpCode"] >= 300){
                return "VENDA NÃO ENCONTRADA EM NENHUMA CONTA DO MERCADO LIVRE";
            }

            $user_id = "435343067";
        }        

        $billing_info   = $meli->get($url_order."/billing_info;?access_token=" . $meliAccessToken);
        
        $status_retorno = self::criarAlterarPedidoMercadoLivre($response_order, $billing_info);
        
        $envio_dados = $meli->get("/shipments/".ArrayHelper::getValue($response_order, 'body.shipping.id')."?access_token=" . $meliAccessToken);
        //echo "<pre>"; print_r($envio_dados); echo "</pre>"; //die;
        
        if ($envio_dados['httpCode'] >= 300) {
            return $status_retorno .= "\nSem informações de envio";
        }
        else {
            $status_retorno .= self::criarAlterarPedidoMercadoLivreEnvio($response_order, $billing_info, $envio_dados);
        }
        
        return $status_retorno;
        die;
        
        self::criarPedidoOmie($user_id, $response_order, $envio_dados);
        
    }
    
    public static function criarPedidoOmie($user_id, $response_order, $envio_dados){
        
        $omie = new Omie(1,1);
        
        $app_key                    = "";
        $app_secret                 = "";
        $codigo_cenario_impostos    = "";
        $codigo_transportadora      = 0;
        $codVend                    = 0;
        $codigo_conta_corrente      = 0;
        
        switch($user_id){
            case "193724256":
                $app_key                    = "468080198586";
                $app_secret                 = "7b3fb2b3bae35eca3b051b825b6d9f43";
                $codigo_cenario_impostos    = "2602379535";
                $codigo_transportadora      = "505552563";
                $codVend                    = 500726231;
                $codigo_conta_corrente      = 502875713;
                break;
            case "435343067":
                $app_key    = "1017311982687";
                $app_secret = "78ba33370fac6178da52d42240591291";
                $codigo_cenario_impostos    = "1128355333";
                $codigo_transportadora      = "1018250911";
                $codVend                    = 1018256043;
                $codigo_conta_corrente      = 1018255531;
                break;
        }

        $body = [
            "call" => "ConsultarCliente",
            "app_key" => $app_key,
            "app_secret" => $app_secret,
            "param" => [
                "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
            ]
        ];
        $response_omie = $omie->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
        
        if (ArrayHelper::getValue($response_omie, 'httpCode') == 200){
            echo "Cliente já cadastrado <br><br>";
        } else{
            //Adicionar novo CLIENTE         
            $body = [
                "call" => "IncluirCliente",
                "app_key" => static::APP_KEY_OMIE_SP,
                "app_secret" => static::APP_SECRET_OMIE_SP,
                "param" => [
                    "codigo_cliente_integracao" => substr(ArrayHelper::getValue($response_order, 'body.buyer.id'),0,20),
                    "razao_social"              => str_replace(" ","%20",substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'), 0, 59)),
                    //"cnpj_cpf"                  => substr(ArrayHelper::getValue($response_order, 'body.buyer.billing_info.doc_number'),0,20),
                    "cnpj_cpf"                  => substr($billing_info["body"]->billing_info->doc_number,0,20),
                    "nome_fantasia"             => str_replace(" ","%20",substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'),0,100)),
                    //"telefone1_ddd"             => substr(ArrayHelper::getValue($response_order, 'body.buyer.phone.area_code'),0,5),
                    //"telefone1_numero"          => substr(ArrayHelper::getValue($response_order, 'body.buyer.phone.number'),0,15),
                    "telefone1_ddd"             => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'),0,5),
                    "telefone1_numero"          => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'),0,15),
                    "contato"                   => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_name'),0,100)),
                    "endereco"                  => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_name'),0,60)),
                    "endereco_numero"           => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_number'),0,10),
                    "bairro"                    => str_replace(" ","%20",substr(((ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')=="") ? "Centro" : ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')),0,29)),
                    "complemento"               => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.comment'),0,39),
                    "estado"                    => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'),-2)),
                    "cidade"                    => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.city.name'),0,40)),
                    "cep"                       => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.zip_code'),0,10),
                    "email"                     => "cliente.pecaagora@gmail.com",//ArrayHelper::getValue($response_order, 'body.buyer.email'),
                ]
            ];
            echo "<pre>"; print_r($body); echo "</pre>";
            $response_omie = $omie->cria_cliente("api/v1/geral/clientes/?JSON=",$body);
            echo "<br><br>"; print_r($response_omie);
            
            $body = [
                "call" => "ConsultarCliente",
                "app_key" => $app_key,
                "app_secret" => $app_secret,
                "param" => [
                    "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                ]
            ];
            $response_omie = $omie->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
        }
        
        //Verificar se existe pedido
        $body = [
            "call" => "ConsultarPedido",
            "app_key" => $app_key,
            "app_secret" => $app_secret,
            "param" => [
                "codigo_pedido_integracao" => ArrayHelper::getValue($response_order, 'body.id'),
            ]
        ];
        $response_omie = $omie->consulta_pedido("api/v1/geral/pedidos/?JSON=",$body);
        print_r($response_omie);
        
        if (ArrayHelper::getValue($response_omie, 'httpCode') == 200){
            echo "Pedido já cadastrado <br><br>";
        } else{
            //Adicionar novo PEDIDO
            //echo "=====>>".ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')."<<====="; die;
            $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
            if(!$produtoML){
                $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id_sem_juros',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                
                if(!$produtoML){
                    $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id_full',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                }
            }
            //echo " <==> Produto_filial: ";var_dump(ArrayHelper::getValue($response_order, 'body.order_items.0.item.id'));die;
            
            if(!$produtoML){
                echo "<br><br>PRODUTO SEM VÍNCULO<br><br>";
                return null;
            }
            
            //print_r($produtoML); die;
            echo "<pre>"; print_r($produtoML->atualizarMLPreco()); echo "</pre>";
            
            $codigo_pa      = "PA".$produtoML->produto_id;
            $codigo_global  = $produtoML->produto->codigo_global;
            $multiplicador  = ((!is_null($produtoML->produto->multiplicador)) ? $produtoML->produto->multiplicador : 1);
            $quantidade     = ArrayHelper::getValue($response_order, 'body.order_items.0.quantity') * $multiplicador;
            $valor_unitario = ArrayHelper::getValue($response_order, 'body.order_items.0.unit_price')/$multiplicador;
            
            echo "<br><br>".$codigo_global;
            
            if(!(strpos($codigo_global,"CX.") === false) || !(strpos($codigo_global,"P.") === false)){
                
                $codigo_global_limpo = str_replace("CX.", "", $codigo_global);
                $codigo_global_limpo = str_replace("P.", "", $codigo_global_limpo);
                echo "<br><br>"; var_dump($codigo_global_limpo);
                
                if(!(strpos($codigo_global,"-") === false)){
                    
                    $codigo_global_limpo_sem_unidades   = explode("-",$codigo_global_limpo);
                    $codigo_global_limpo                = $codigo_global_limpo_sem_unidades[0];
                    
                }
                
                //print_r($codigo_global); die;
                $produto_unitario   = Produto::find()->andWhere(['=', 'codigo_global', $codigo_global_limpo])->one();
                
                if(!$produto_unitario){
                    echo "<br><br>SEM PRODUTO UNITARIO CADASTRADO<br><br>";
                    return;
                }
                
                $codigo_pa          = "PA".$produto_unitario->id;
                
            }
            
            //$cfop   = "6.102";
            $cfop   = "6.108";
            $csosn  = "102";
            if (substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'),-2) == "SP"){
                $cfop   = "5.405";
                $csosn  = "500";
            }
           
            $imposto = array();
            //echo "111<pre>"; print_r($produtoML); echo "</pre>"; die;
            $imposto = self::gerarImposto($csosn, $produtoML->produto);
            
            
            //DESCONTOS
            $tarifas        = 0.0;
            $frete          = 0.0;
            $valor          = 0.0;
            //$valor_unitario = 0.0;
            foreach(ArrayHelper::getValue($response_order, 'body.order_items') as $k => $produto){

                $tarifas        = ArrayHelper::getValue($produto, 'sale_fee') * $quantidade;
                $frete          = ArrayHelper::getValue($envio_dados, 'body.shipping_option.cost') - ArrayHelper::getValue($envio_dados, 'body.shipping_option.list_cost');
                $frete          = ($frete < 0) ? $frete*(-1) : $frete;
                $valor          = ArrayHelper::getValue($produto, 'full_unit_price') * $quantidade - ($tarifas + $frete);
                $valor_unitario  = $valor/$quantidade;
            }
            
            $body = [
                "call" => "IncluirPedido",
                "app_key" => $app_key,
                "app_secret" => $app_secret,
                "param" => [
                    "cabecalho" => [
                        "bloqueado"                 => "N",
                        //"codigo_cliente"            => ArrayHelper::getValue($response_omie, 'body.codigo_cliente_omie'),
                        "codigo_cenario_impostos"   => $codigo_cenario_impostos,
                        "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                        "codigo_pedido_integracao"  => ArrayHelper::getValue($response_order, 'body.id'),
                        "etapa"                     => "10",
                        "data_previsao"             => substr(ArrayHelper::getValue($response_order, 'body.date_created'),8,2).'/'.substr(ArrayHelper::getValue($response_order, 'body.date_created'),5,2).'/'.substr(ArrayHelper::getValue($response_order, 'body.date_created'),0,4),
                        "quantidade_itens"          => ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                    ],
                    "det"=> [
                        "ide"=> [
                            "codigo_item_integracao"    => substr($produtoML->produto->codigo_global,0,20),
                            "regra_impostos"            => 0,
                            "simples_nacional"           => "",
                        ],
                        "imposto" => $imposto,
                        
                        "produto" => [
                            "codigo_produto_integracao" => $codigo_pa,//"PA".$produtoML->produto->id,
                            "cfop"                      => $cfop,
                            "quantidade"                => $quantidade,//ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                            "valor_unitario"            => $valor_unitario,//ArrayHelper::getValue($response_order, 'body.order_items.0.unit_price'),
                        ],
                    ],
                    "frete" => [
                        "codigo_transportadora" => $codigo_transportadora,
                        "modalidade"            => 0,
                        "quantidade_volumes"    => 1,
                        "especie_volumes"       => "CAIXA",
                        "outras_despesas"       => $tarifas,
                        "valor_frete"           => $frete,
                    ],
                    "informacoes_adicionais"    => [
                        "numero_contrato"           => ArrayHelper::getValue($response_order, 'body.payments.0.id'),
                        "numero_pedido_cliente"     => ArrayHelper::getValue($response_order, 'body.id'),
                        "consumidor_final"          => "S",
                        "codigo_categoria"          => "1.01.03",
                        "codVend"                   => $codVend,
                        "codigo_conta_corrente"     => $codigo_conta_corrente,
                    ],
                ],
            ];
            
            echo " <==> Body Pedido: <pre>";print_r($body); echo "</pre>";
            $response_omie = $omie->cria_pedido("api/v1/produtos/pedido/?JSON=",$body);
            echo "<br><br> Resposta Pedido: "; print_r($response_omie);
        }
        
    }
    
    
    public static function gerarImposto($csosn, $produto){
        
        $imposto = [
            "ipi" => [
                "cod_sit_trib_ipi"  => 99,
                "enquadramento_ipi" => 999,
                "tipo_calculo_ipi"  => "B",
            ],
            "pis_padrao" => [
                "cod_sit_trib_pis"  => 49,
                "tipo_calculo_pis"  => "B",
            ],
            "cofins_padrao" => [
                "cod_sit_trib_cofins"   => 49,
                "tipo_calculo_cofins"   => "B",
            ],
            "icms_sn" => [
                "cod_sit_trib_icms_sn" => $csosn,
            ],
        ];
        
        $codigos = [
            //Anexo 1
            40161010,
            40169990,
            6813,
            70071100,
            70072100,
            70091000,
            73201000,
            83012000,
            83023000,
            84073390,
            84073490,
            840820,
            840991,
            840999,
            841330,
            84139100,
            84148021,
            84148022,
            841520,
            84212300,
            84213100,
            84314100,
            84314200,
            84339090,
            848310,
            84832000,
            848330,
            848340,
            848350,
            850520,
            85071000,
            8511,
            851220,
            85123000,
            851240,
            85129000,
            85272,
            85365090,
            853910,
            85443000,
            870600,
            8707,
            8708,
            90292010,
            90299010,
            90303921,
            90318040,
            9032892,
            91040000,
            94012000,
            
            //Anexo 2
            8429,
            843320,
            84333000,
            84334000,
            84335,
            8701,
            8702,
            8703,
            8704,
            8705,
            8706,
            8431,
            84089090,
            84122110,
            84122190,
            84123110,
            87012000,
            8702,
            8704,
            84136019,
            84148019,
            84149039,
            84329000,
            84324000,
            84328000,
            84811000,
            84812090,
            84818092,
            8483601,
            85011019
        ];
        
        //echo "<pre>"; print_r($imposto); echo "</pre>"; die;
        
        foreach($codigos as $k => $codigo){
            
            //echo "<br>".$k." - ".$codigo;
            
            $quantidade_caracteres = strlen($codigo);
            $ncm = str_replace('.','',$produto->codigo_montadora);
            $sub_ncm = substr($ncm,0,$quantidade_caracteres);
            
            //echo " - ".$quantidade_caracteres." - ".$sub_ncm;
            
            if($sub_ncm == $codigo){
                $imposto["cofins_padrao"]["cod_sit_trib_cofins"] = "04";
                $imposto["cofins_padrao"]["tipo_calculo_cofins"] = "";
                
                $imposto["pis_padrao"]["cod_sit_trib_pis"] = "04";
                $imposto["pis_padrao"]["tipo_calculo_pis"] = "";
                
                break;
            }
        }
        
        //echo "<pre>"; print_r($imposto); echo "</pre>";
        
    }
    
    
    public static function criarAlterarPedidoMercadoLivre($order, $billing_info, $shipping = null){
        
        //echo "<pre>"; print_r($order); echo "</pre>"; die;
        //echo "<pre>"; print_r($shipping); echo "</pre>";die;
        
        $status_retorno = "";
        
        $pedido_mercado_livre = PedidoMercadoLivre::find()->andwhere(['=', 'pedido_meli_id', ArrayHelper::getValue($order, 'body.id')])->one();
        if($pedido_mercado_livre){
            $pedido_mercado_livre->pedido_meli_id       = (string) ArrayHelper::getValue($order, 'body.id');
            $pedido_mercado_livre->total_amount         = ArrayHelper::getValue($order, 'body.total_amount');
            $pedido_mercado_livre->date_created         = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.date_created'),0,19));
            $pedido_mercado_livre->date_closed          = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.date_closed'),0,19));
            $pedido_mercado_livre->last_updated         = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.last_updated'),0,19));
            $pedido_mercado_livre->paid_amount          = (string) ArrayHelper::getValue($order, 'body.paid_amount');
            $pedido_mercado_livre->shipping_id          = (string) ArrayHelper::getValue($order, 'body.shipping.id');
            $pedido_mercado_livre->status               = (string) ArrayHelper::getValue($order, 'body.status');
            $pedido_mercado_livre->buyer_id             = (string) ArrayHelper::getValue($order, 'body.buyer.id');
            $pedido_mercado_livre->buyer_nickname       = (string) (isset($order["body"]->buyer->nickname)) ? ArrayHelper::getValue($order, 'body.buyer.nickname') : "";
            $pedido_mercado_livre->buyer_email          = (string) (isset($order["body"]->buyer->email)) ? ArrayHelper::getValue($order, 'body.buyer.email') : "";
            $pedido_mercado_livre->buyer_first_name     = (string) (isset($order["body"]->buyer->first_name)) ? ArrayHelper::getValue($order, 'body.buyer.first_name') : "";
            $pedido_mercado_livre->buyer_last_name      = (string) (isset($order["body"]->buyer->last_name)) ? ArrayHelper::getValue($order, 'body.buyer.last_name') : "";
            $pedido_mercado_livre->buyer_doc_type       = (string) (isset($billing_info["body"]->billing_info->doc_type)) ? $billing_info["body"]->billing_info->doc_type : "";
            $pedido_mercado_livre->buyer_doc_number     = (string) (isset($billing_info["body"]->billing_info->doc_number)) ? $billing_info["body"]->billing_info->doc_number : "";
            $pedido_mercado_livre->user_id              = (string) ArrayHelper::getValue($order, 'body.seller.id');
            $pedido_mercado_livre->pack_id              = (string) ArrayHelper::getValue($order, 'body.pack_id');
            
            if(is_null($pedido_mercado_livre->email_enderecos)){
                $pedido_mercado_livre->email_enderecos = "entregasp.pecaagora@gmail.com; notafiscal.pecaagora@gmail.com; compras.pecaagora@gmail.com; entregasp.pecaagora@gmail.com";
                
            }
            
            if($pedido_mercado_livre->save()){
                $status_retorno .= "\nPedido alterado";
            }
            else{
                $status_retorno .=  "\nPedido não alterado";
            }
        }
        else{
            //echo "<pre>"; print_r($order); echo "</pre>"; die;
            $pedido_mercado_livre = new PedidoMercadoLivre();
            $pedido_mercado_livre->pedido_meli_id       = (string) ArrayHelper::getValue($order, 'body.id');
            $pedido_mercado_livre->total_amount         = ArrayHelper::getValue($order, 'body.total_amount');
            $pedido_mercado_livre->date_created         = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.date_created'),0,19));
            $pedido_mercado_livre->date_closed          = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.date_closed'),0,19));
            $pedido_mercado_livre->last_updated         = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.last_updated'),0,19));
            $pedido_mercado_livre->paid_amount          = (string) ArrayHelper::getValue($order, 'body.paid_amount');
            $pedido_mercado_livre->shipping_id          = (string) ArrayHelper::getValue($order, 'body.shipping.id');
            $pedido_mercado_livre->status               = (string) ArrayHelper::getValue($order, 'body.status');
            $pedido_mercado_livre->buyer_id             = (string) ArrayHelper::getValue($order, 'body.buyer.id');
            $pedido_mercado_livre->buyer_nickname       = (string) (isset($order["body"]->buyer->nickname)) ? ArrayHelper::getValue($order, 'body.buyer.nickname') : "";
            $pedido_mercado_livre->buyer_email          = (string) (isset($order["body"]->buyer->email)) ? ArrayHelper::getValue($order, 'body.buyer.email') : "";
            $pedido_mercado_livre->buyer_first_name     = (string) ArrayHelper::getValue($order, 'body.buyer.first_name');
            $pedido_mercado_livre->buyer_last_name      = (string) ArrayHelper::getValue($order, 'body.buyer.last_name');
            $pedido_mercado_livre->buyer_doc_type       = (string) $billing_info["body"]->billing_info->doc_type;
            $pedido_mercado_livre->buyer_doc_number     = (string) $billing_info["body"]->billing_info->doc_number;
            $pedido_mercado_livre->user_id              = (string) ArrayHelper::getValue($order, 'body.seller.id');
            $pedido_mercado_livre->pack_id              = (string) ArrayHelper::getValue($order, 'body.pack_id');
            $pedido_mercado_livre->email_enderecos      = "entregasp.pecaagora@gmail.com, notafiscal.pecaagora@gmail.com, compras.pecaagora@gmail.com";
            
            if($pedido_mercado_livre->save()){
                $status_retorno .= "\nPedido criado";
            }
            else{
                $status_retorno .= "\nPedido não criado";
            }
        }
        
        if($pedido_mercado_livre){
                      
            $produtos_email = "
DESTACAR O ST RECOLHIDO ANTERIORMENTE EM INFORMAÇÕES ADICIONAIS E TAMBÉM NO XML DA NOTA, CASO CONTRÁRIO A MESMA SERÁ RECUSADA.
                
Cód.: {codigo}
Descrição: {descricao}
Quantidade: {quantidade}
Valor: R$ {valor}
Observação: {observacao}
NCM: {ncm}
PA{pa}
                
                
Envio: Carmópolis de Minas, 963, Vila Maria.
                
  
Atenciosamente,
                
                
Peça Agora
Site: https://www.pecaagora.com/
E-mail: compras.pecaagora@gmail.comSetor de Compras:(32)3015-0023Whatsapp:(32)988354007
Skype: pecaagora";
            
            
            
            foreach(ArrayHelper::getValue($order, 'body.order_items') as $k => $produto){
                
                $pedido_mercado_livre_produto = PedidoMercadoLivreProduto::find()->andWhere(['=', 'pedido_mercado_livre_id', $pedido_mercado_livre->id])
                ->andWhere(['=', 'produto_meli_id', ArrayHelper::getValue($produto, 'item.id')])
                ->one();
                if($pedido_mercado_livre_produto){
                    $pedido_mercado_livre_produto->pedido_mercado_livre_id = $pedido_mercado_livre->id;
                    $pedido_mercado_livre_produto->produto_meli_id         = ArrayHelper::getValue($produto, 'item.id');
                    $pedido_mercado_livre_produto->title                   = ArrayHelper::getValue($produto, 'item.title');
                    $pedido_mercado_livre_produto->categoria_meli_id       = ArrayHelper::getValue($produto, 'item.category_id');
                    $pedido_mercado_livre_produto->condition               = ArrayHelper::getValue($produto, 'item.condition');
                    $pedido_mercado_livre_produto->quantity                = ArrayHelper::getValue($produto, 'quantity');
                    $pedido_mercado_livre_produto->unit_price              = ArrayHelper::getValue($produto, 'unit_price');
                    $pedido_mercado_livre_produto->full_unit_price         = ArrayHelper::getValue($produto, 'full_unit_price');
                    $pedido_mercado_livre_produto->sale_fee                = ArrayHelper::getValue($produto, 'sale_fee');
                    $pedido_mercado_livre_produto->listing_type_id         = ArrayHelper::getValue($produto, 'listing_type_id');
                    
                    //$produto_filial = ProdutoFilial::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])->one();
                    $produto_filial = ProdutoFilial::find()	->orWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])
                    ->orWhere(['=', 'meli_id_sem_juros', ArrayHelper::getValue($produto, 'item.id')])
                    ->orWhere(['=', 'meli_id_full', ArrayHelper::getValue($produto, 'item.id')])
                    ->one();
                    if($produto_filial){
                        $pedido_mercado_livre_produto->produto_filial_id = $produto_filial->id;
                    }
                    
                    if($pedido_mercado_livre_produto->save()){
                        echo "<br><br><br>Produto alterado";
                    }
                    else{
                        echo "<br><br><br>Produto não alterado";
                    }
                }
                else{
                    //echo "<pre>==>"; print_r($pedido_mercado_livre->id); echo "<==</pre>"; die;
                    $pedido_mercado_livre_produto                          = new PedidoMercadoLivreProduto();
                    $pedido_mercado_livre_produto->pedido_mercado_livre_id = $pedido_mercado_livre->id;
                    $pedido_mercado_livre_produto->produto_meli_id         = ArrayHelper::getValue($produto, 'item.id');
                    $pedido_mercado_livre_produto->title                   = ArrayHelper::getValue($produto, 'item.title');
                    $pedido_mercado_livre_produto->categoria_meli_id       = ArrayHelper::getValue($produto, 'item.category_id');
                    $pedido_mercado_livre_produto->condition               = ArrayHelper::getValue($produto, 'item.condition');
                    $pedido_mercado_livre_produto->quantity                = ArrayHelper::getValue($produto, 'quantity');
                    $pedido_mercado_livre_produto->unit_price              = ArrayHelper::getValue($produto, 'unit_price');
                    $pedido_mercado_livre_produto->full_unit_price         = ArrayHelper::getValue($produto, 'full_unit_price');
                    $pedido_mercado_livre_produto->sale_fee                = ArrayHelper::getValue($produto, 'sale_fee');
                    $pedido_mercado_livre_produto->listing_type_id         = ArrayHelper::getValue($produto, 'listing_type_id');
                    
                    //$produto_filial = ProdutoFilial::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])->one();
                    $produto_filial = ProdutoFilial::find()     ->orWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])
                    ->orWhere(['=', 'meli_id_sem_juros', ArrayHelper::getValue($produto, 'item.id')])
                    ->orWhere(['=', 'meli_id_full', ArrayHelper::getValue($produto, 'item.id')])
                    ->one();
                    if($produto_filial){
                        $pedido_mercado_livre_produto->produto_filial_id = $produto_filial->id;
                    }
                    
                    if($pedido_mercado_livre_produto->save()){
                        echo "<br><br><br>Produto criado";
                    }
                    else{
                        echo "<br><br><br>Produto não criado";
                    }
                }
                
                /*$produtos_email .= "\n\nCódigo: {codigo}";
                 if($produto_filial){
                 $produtos_email .= $produto_filial->produto->codigo_fabricante;
                 }
                 $produtos_email .= "\nDescrição: {descricao}";//.ArrayHelper::getValue($produto, 'item.title');
                 $produtos_email .= "\nQuantidade: {quantidade}";//.ArrayHelper::getValue($produto, 'quantity');
                 $produtos_email .= "\nValor: {valor}";
                 $produtos_email .= "\nObservação: {observacao}";
                 if($pedido_mercado_livre_produto->valor_cotacao <> null && $pedido_mercado_livre_produto->valor_cotacao > 0){
                 $produtos_email .= $pedido_mercado_livre_produto->valor_cotacao;
                 }*/
                
                
            }
            
            //Cadastra os dados dos pagamentos
            foreach(ArrayHelper::getValue($order, 'body.payments') as $k => $pagamento){
                
                $pedido_mercado_livre_pagamento = PedidoMercadoLivrePagamento::find()   ->andWhere(['=', 'pedido_mercado_livre_id', $pedido_mercado_livre->id])
                ->andWhere(['=', 'pagamento_meli_id', ArrayHelper::getValue($pagamento, 'id')])
                ->one();
                if($pedido_mercado_livre_pagamento){
                    $pedido_mercado_livre_pagamento->pedido_mercado_livre_id    = $pedido_mercado_livre->id;
                    $pedido_mercado_livre_pagamento->pagamento_meli_id          = (string) ArrayHelper::getValue($pagamento, 'id');
                    $pedido_mercado_livre_pagamento->payer_id                   = (string) ArrayHelper::getValue($pagamento, 'payer_id');
                    $pedido_mercado_livre_pagamento->card_id                    = (string) ArrayHelper::getValue($pagamento, 'card_id');
                    $pedido_mercado_livre_pagamento->payment_method_id          = (string) ArrayHelper::getValue($pagamento, 'payment_method_id');
                    $pedido_mercado_livre_pagamento->operation_type             = (string) ArrayHelper::getValue($pagamento, 'operation_type');
                    $pedido_mercado_livre_pagamento->payment_type               = (string) ArrayHelper::getValue($pagamento, 'payment_type');
                    $pedido_mercado_livre_pagamento->status                     = (string) ArrayHelper::getValue($pagamento, 'status');
                    $pedido_mercado_livre_pagamento->status_detail              = (string) ArrayHelper::getValue($pagamento, 'status_detail');
                    $pedido_mercado_livre_pagamento->transaction_amount         = ArrayHelper::getValue($pagamento, 'transaction_amount');
                    $pedido_mercado_livre_pagamento->taxes_amount               = ArrayHelper::getValue($pagamento, 'taxes_amount');
                    $pedido_mercado_livre_pagamento->shipping_cost              = ArrayHelper::getValue($pagamento, 'shipping_cost');
                    $pedido_mercado_livre_pagamento->coupon_amount              = ArrayHelper::getValue($pagamento, 'coupon_amount');
                    $pedido_mercado_livre_pagamento->overpaid_amount            = ArrayHelper::getValue($pagamento, 'overpaid_amount');
                    $pedido_mercado_livre_pagamento->total_paid_amount          = ArrayHelper::getValue($pagamento, 'total_paid_amount');
                    $pedido_mercado_livre_pagamento->installment_amount         = ArrayHelper::getValue($pagamento, 'installment_amount');
                    $pedido_mercado_livre_pagamento->date_approved              = ArrayHelper::getValue($pagamento, 'date_approved');
                    $pedido_mercado_livre_pagamento->authorization_code         = (string) ArrayHelper::getValue($pagamento, 'authorization_code');
                    $pedido_mercado_livre_pagamento->date_created               = ArrayHelper::getValue($pagamento, 'date_created');
                    $pedido_mercado_livre_pagamento->date_last_modified         = ArrayHelper::getValue($pagamento, 'date_last_modified');
                    
                    if($pedido_mercado_livre_pagamento->save()){
                        echo "<br><br><br>Pagamento alterado";
                    }
                    else{
                        echo "<br><br><br>Pagamento não alterado";
                    }
                }
                else{
                    $pedido_mercado_livre_pagamento                             = new PedidoMercadoLivrePagamento();
                    $pedido_mercado_livre_pagamento->pedido_mercado_livre_id    = $pedido_mercado_livre->id;
                    $pedido_mercado_livre_pagamento->pagamento_meli_id          = (string) ArrayHelper::getValue($pagamento, 'id');
                    $pedido_mercado_livre_pagamento->payer_id                   = (string) ArrayHelper::getValue($pagamento, 'payer_id');
                    $pedido_mercado_livre_pagamento->card_id                    = (string) ArrayHelper::getValue($pagamento, 'card_id');
                    $pedido_mercado_livre_pagamento->payment_method_id          = (string) ArrayHelper::getValue($pagamento, 'payment_method_id');
                    $pedido_mercado_livre_pagamento->operation_type             = (string) ArrayHelper::getValue($pagamento, 'operation_type');
                    $pedido_mercado_livre_pagamento->payment_type               = (string) ArrayHelper::getValue($pagamento, 'payment_type');
                    $pedido_mercado_livre_pagamento->status                     = (string) ArrayHelper::getValue($pagamento, 'status');
                    $pedido_mercado_livre_pagamento->status_detail              = (string) ArrayHelper::getValue($pagamento, 'status_detail');
                    $pedido_mercado_livre_pagamento->transaction_amount         = ArrayHelper::getValue($pagamento, 'transaction_amount');
                    $pedido_mercado_livre_pagamento->taxes_amount               = ArrayHelper::getValue($pagamento, 'taxes_amount');
                    $pedido_mercado_livre_pagamento->shipping_cost              = ArrayHelper::getValue($pagamento, 'shipping_cost');
                    $pedido_mercado_livre_pagamento->coupon_amount              = ArrayHelper::getValue($pagamento, 'coupon_amount');
                    $pedido_mercado_livre_pagamento->overpaid_amount            = ArrayHelper::getValue($pagamento, 'overpaid_amount');
                    $pedido_mercado_livre_pagamento->total_paid_amount          = ArrayHelper::getValue($pagamento, 'total_paid_amount');
                    $pedido_mercado_livre_pagamento->installment_amount         = ArrayHelper::getValue($pagamento, 'installment_amount');
                    $pedido_mercado_livre_pagamento->date_approved              = ArrayHelper::getValue($pagamento, 'date_approved');
                    $pedido_mercado_livre_pagamento->authorization_code         = (string) ArrayHelper::getValue($pagamento, 'authorization_code');
                    $pedido_mercado_livre_pagamento->date_created               = ArrayHelper::getValue($pagamento, 'date_created');
                    $pedido_mercado_livre_pagamento->date_last_modified         = ArrayHelper::getValue($pagamento, 'date_last_modified');
                    
                    if($pedido_mercado_livre_pagamento->save()){
                        echo "<br><br><br>Pagamento criado";
                    }
                    else{
                        echo "<br><br><br>Pagamento não criado";
                    }
                }
            }
            
            $pedido_mercado_livre->email_texto = $produtos_email;
            $pedido_mercado_livre->email_assunto = "{de} Pedido {codigo_fabricante} * {quantidade} - {nome} ({margem})";
            if($pedido_mercado_livre->user_id == "435343067"){
                $pedido_mercado_livre->email_assunto = "{de} Novo Pedido {codigo_fabricante} * {quantidade} - {nome} ({margem})";
            }
            $pedido_mercado_livre->save();
        }
        
        echo "<br><br><br>";
    }
    
    public static function criarAlterarPedidoMercadoLivreEnvio($order, $billing_info, $shipping = null){
        
        $status_retorno = "";
        
        $pedido_mercado_livre = PedidoMercadoLivre::find()->andwhere(['=', 'pedido_meli_id', ArrayHelper::getValue($order, 'body.id')])->one();
        
        if($pedido_mercado_livre){
            
            //echo "123"; var_dump((string)ArrayHelper::getValue($shipping, 'body.receiver_address.city.id'));die;
            
            //Cadastra os dados de envio e do recebedor
            if(!is_null($shipping)){
                $pedido_mercado_livre->shipping_base_cost                   = ArrayHelper::getValue($shipping, 'body.base_cost');
                $pedido_mercado_livre->shipping_status                      = (string) ArrayHelper::getValue($shipping, 'body.status');
                $pedido_mercado_livre->shipping_substatus                   = (string) ArrayHelper::getValue($shipping, 'body.substatus');
                $pedido_mercado_livre->shipping_date_created                = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.date_created'),0,19));
                $pedido_mercado_livre->shipping_last_updated                = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.last_updated'),0,19));
                $pedido_mercado_livre->shipping_tracking_number             = (string) ArrayHelper::getValue($shipping, 'body.tracking_number');
                $pedido_mercado_livre->shipping_tracking_method             = (string) ArrayHelper::getValue($shipping, 'body.tracking_method');
                $pedido_mercado_livre->shipping_service_id                  = (string) ArrayHelper::getValue($shipping, 'body.service_id');
                $pedido_mercado_livre->receiver_id                          = (string) ArrayHelper::getValue($shipping, 'body.receiver_id');
                $pedido_mercado_livre->receiver_address_id                  = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.id');
                $pedido_mercado_livre->receiver_address_line                = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.address_line');
                $pedido_mercado_livre->receiver_street_name                 = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_name');
                $pedido_mercado_livre->receiver_street_number               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_number');
                $pedido_mercado_livre->receiver_comment                     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.comment');
                $pedido_mercado_livre->receiver_zip_code                    = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.zip_code');
                $pedido_mercado_livre->receiver_city_id                     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.id');
                $pedido_mercado_livre->receiver_city_name                   = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.name');
                $pedido_mercado_livre->receiver_state_id                    = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.id');
                $pedido_mercado_livre->receiver_state_name                  = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.name');
                $pedido_mercado_livre->receiver_country_id                  = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.id');
                $pedido_mercado_livre->receiver_country_name                = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.name');
                $pedido_mercado_livre->receiver_neighborhood_id             = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.id');
                $pedido_mercado_livre->receiver_neighborhood_name           = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.name');
                $pedido_mercado_livre->receiver_municipality_id             = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.id');
                $pedido_mercado_livre->receiver_municipality_name           = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.name');
                $pedido_mercado_livre->receiver_delivery_preference         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.delivery_preference');
                $pedido_mercado_livre->receiver_name                        = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_name');
                $pedido_mercado_livre->receiver_phone                       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_phone');
                $pedido_mercado_livre->shipping_option_id                   = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.id');
                $pedido_mercado_livre->shipping_option_shipping_method_id   = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.shipping_method_id');
                $pedido_mercado_livre->shipping_option_name                 = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.name');
                $pedido_mercado_livre->shipping_option_list_cost            = ArrayHelper::getValue($shipping, 'body.shipping_option.list_cost');
                $pedido_mercado_livre->shipping_option_cost                 = ArrayHelper::getValue($shipping, 'body.shipping_option.cost');
                $pedido_mercado_livre->shipping_option_delivery_type        = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.delivery_type');
                //echo "<pre>"; print_r($pedido_mercado_livre->receiver_city_id); echo "</pre>";
                //echo "<pre>"; var_dump( ArrayHelper::getValue($shipping, 'body.receiver_address.city.id')); echo "</pre>";
                //echo "<pre>"; print_r($pedido_mercado_livre); echo "</pre>";
                
                //var_dump($pedido_mercado_livre->save()); die;
                
                if($pedido_mercado_livre->save()){
                    $status_retorno .= "\nEnvio alterado";
                }
                else{
                    $status_retorno .= "\nEnvio não alterado";
                }
                
                
                //SHIPPING
                $pedido_mercado_livre_shipments = PedidoMercadoLivreShipments::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($shipping, 'body.id')])->one();
                if($pedido_mercado_livre_shipments){
                    
                    $pedido_mercado_livre_shipments->meli_id                                = (string) ArrayHelper::getValue($shipping, 'body.id');
                    $pedido_mercado_livre_shipments->mode                                   = (string) ArrayHelper::getValue($shipping, 'body.mode');
                    $pedido_mercado_livre_shipments->created_by                             = (string) ArrayHelper::getValue($shipping, 'body.created_by');
                    $pedido_mercado_livre_shipments->order_id                               = (string) ArrayHelper::getValue($shipping, 'body.order_id');
                    $pedido_mercado_livre_shipments->order_cost                             = (float) ArrayHelper::getValue($shipping, 'body.order_cost');
                    $pedido_mercado_livre_shipments->base_cost                              = (float) ArrayHelper::getValue($shipping, 'body.base_cost');
                    $pedido_mercado_livre_shipments->site_id                                = (string) ArrayHelper::getValue($shipping, 'body.site_id');
                    $pedido_mercado_livre_shipments->status                                 = (string) ArrayHelper::getValue($shipping, 'body.status');
                    $pedido_mercado_livre_shipments->substatus                              = (string) ArrayHelper::getValue($shipping, 'body.substatus');
                    $pedido_mercado_livre_shipments->history_date_cancelled                 = (isset($shipping["body"]->status_history->date_cancelled)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_cancelled'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_delivered                 = (isset($shipping["body"]->status_history->date_delivered)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_delivered'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_first_visit               = (isset($shipping["body"]->status_history->date_first_visit)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_first_visit'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_handling                  = (isset($shipping["body"]->status_history->date_handling)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_handling'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_not_delivered             = (isset($shipping["body"]->status_history->date_not_delivered)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_not_delivered'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_ready_to_ship             = (isset($shipping["body"]->status_history->date_ready_to_ship)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_ready_to_ship'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_shipped                   = (isset($shipping["body"]->status_history->date_shipped)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_shipped'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_returned                  = (isset($shipping["body"]->status_history->date_returned)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_returned'),0,19)) : "";
                    $pedido_mercado_livre_shipments->date_created                           = (isset($shipping["body"]->status_history->date_created)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_created'),0,19)) : "";
                    $pedido_mercado_livre_shipments->last_updated                           = (isset($shipping["body"]->status_history->last_updated)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.last_updated'),0,19)) : "";
                    $pedido_mercado_livre_shipments->tracking_number                        = (string) ArrayHelper::getValue($shipping, 'body.tracking_number');
                    $pedido_mercado_livre_shipments->tracking_method                        = (string) ArrayHelper::getValue($shipping, 'body.tracking_method');
                    $pedido_mercado_livre_shipments->service_id                             = (string) ArrayHelper::getValue($shipping, 'body.service_id');
                    $pedido_mercado_livre_shipments->sender_id                              = (string) ArrayHelper::getValue($shipping, 'body.sender_id');
                    $pedido_mercado_livre_shipments->receiver_id                            = (string) ArrayHelper::getValue($shipping, 'body.receiver_id');
                    $pedido_mercado_livre_shipments->receiver_address_id                    = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.id');
                    $pedido_mercado_livre_shipments->receiver_address_address_line          = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.address_line');
                    $pedido_mercado_livre_shipments->receiver_address_street_name           = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_name');
                    $pedido_mercado_livre_shipments->receiver_address_street_number         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_number');
                    $pedido_mercado_livre_shipments->receiver_address_comment               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.comment');
                    $pedido_mercado_livre_shipments->receiver_address_zip_code              = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.zip_code');
                    $pedido_mercado_livre_shipments->receiver_address_city_id               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.id');
                    $pedido_mercado_livre_shipments->receiver_address_city_name             = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.name');
                    $pedido_mercado_livre_shipments->receiver_address_state_id              = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.id');
                    $pedido_mercado_livre_shipments->receiver_address_state_name            = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.name');
                    $pedido_mercado_livre_shipments->receiver_address_country_id            = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.id');
                    $pedido_mercado_livre_shipments->receiver_address_country_name          = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.name');
                    $pedido_mercado_livre_shipments->receiver_address_neighborhood_id       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.id');
                    $pedido_mercado_livre_shipments->receiver_address_neighborhood_name     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.name');
                    $pedido_mercado_livre_shipments->receiver_address_municipality_id       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.id');
                    $pedido_mercado_livre_shipments->receiver_address_municipality_name     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.name');
                    $pedido_mercado_livre_shipments->receiver_address_delivery_preference   = (isset($shipping["body"]->receiver_address->delivery_preference)) ? (string) ArrayHelper::getValue($shipping, 'body.receiver_address.delivery_preference') : "";
                    $pedido_mercado_livre_shipments->receiver_address_receiver_name         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_name');
                    $pedido_mercado_livre_shipments->receiver_address_receiver_phone        = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_phone');
                    $pedido_mercado_livre_shipments->shipping_option_id                     = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.id');
                    $pedido_mercado_livre_shipments->shipping_option_shipping_method_id     = (isset($shipping["body"]->shipping_option->shipping_method_id)) ? (string) ArrayHelper::getValue($shipping, 'body.shipping_option.shipping_method_id') : "";
                    $pedido_mercado_livre_shipments->shipping_option_name                   = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.name');
                    $pedido_mercado_livre_shipments->shipping_option_currency_id            = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.currency_id');
                    $pedido_mercado_livre_shipments->shipping_option_list_cost              = (float) ArrayHelper::getValue($shipping, 'body.shipping_option.list_cost');
                    $pedido_mercado_livre_shipments->shipping_option_cost                   = (float) ArrayHelper::getValue($shipping, 'body.shipping_option.cost');
                    $pedido_mercado_livre_shipments->delivery_type                          = (isset($shipping["body"]->shipping_option->delivery_type)) ? (string) ArrayHelper::getValue($shipping, 'body.shipping_option.delivery_type') : "";
                    $pedido_mercado_livre_shipments->comments                               = (string) ArrayHelper::getValue($shipping, 'body.comments');
                    $pedido_mercado_livre_shipments->date_first_printed                     = str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.date_first_printed'),0,19));
                    $pedido_mercado_livre_shipments->market_place                           = (string) ArrayHelper::getValue($shipping, 'body.market_place');
                    $pedido_mercado_livre_shipments->type                                   = (isset($shipping["body"]->type)) ? (string) ArrayHelper::getValue($shipping, 'body.type') : "";
                    $pedido_mercado_livre_shipments->logistic_type                          = (isset($shipping["body"]->logistic_type)) ? (string) ArrayHelper::getValue($shipping, 'body.logistic_type') : "";
                    $pedido_mercado_livre_shipments->application_id                         = (isset($shipping["body"]->application_id)) ? (string) ArrayHelper::getValue($shipping, 'body.application_id') : "";
                    $pedido_mercado_livre_shipments->pedido_mercado_livre_id                = $pedido_mercado_livre->id;
                    
                    //echo "<pre>===>>"; print_r($pedido_mercado_livre_shipments); echo "<<==="; die;
                    
                    if($pedido_mercado_livre_shipments->save()){
                        $status_retorno .= "\nEnvio tabela alterado";
                        
                        //echo "<pre>===>>"; print_r($pedido_mercado_livre_shipments); echo "<<==="; die;
                        
                        foreach(ArrayHelper::getValue($shipping, 'body.shipping_items') as $shipping_item){
                            
                            //echo "<pre>"; print_r($shipping_item); echo "</pre>"; die;
                            
                            $status_item = "alterado";
                            $pedido_mercado_livre_shipments_item = PedidoMercadoLivreShipmentsItem::find()->andWhere(['=', 'pedido_mercado_livre_shipments_id', $pedido_mercado_livre_shipments->id])
                            ->andWhere(['=', 'meli_id', $shipping_item->id])
                            ->one();
                            if(!$pedido_mercado_livre_shipments_item){
                                $pedido_mercado_livre_shipments_item = new PedidoMercadoLivreShipmentsItem;
                                $status_item = "criado";
                            }
                            
                            $pedido_mercado_livre_shipments_item->pedido_mercado_livre_shipments_id   = $pedido_mercado_livre_shipments->id;
                            $pedido_mercado_livre_shipments_item->meli_id                             = (string) $shipping_item->id;
                            $pedido_mercado_livre_shipments_item->description                         = (string) $shipping_item->description;
                            $pedido_mercado_livre_shipments_item->quantity                            = $shipping_item->quantity;
                            $pedido_mercado_livre_shipments_item->dimensions                          = (string) $shipping_item->dimensions;
                            $pedido_mercado_livre_shipments_item->dimensions_source_id                = (string) $shipping_item->dimensions_source->id;
                            $pedido_mercado_livre_shipments_item->dimensions_source_origin            = (string) $shipping_item->dimensions_source->origin;
                            if($pedido_mercado_livre_shipments_item->save()){
                                $status_retorno .= "\nProduto do pedido ".$status_item;
                            }
                            else{
                                $status_retorno .= "\nProduto do pedido ".$status_item;
                            }
                        }
                    }
                    else{
                        $status_retorno = "\nEnvio tabela não alterado";
                    }
                }
                else{
                    
                    $pedido_mercado_livre_shipments = new PedidoMercadoLivreShipments;
                    $pedido_mercado_livre_shipments->meli_id                                = (string) ArrayHelper::getValue($shipping, 'body.id');
                    $pedido_mercado_livre_shipments->mode                                   = (string) ArrayHelper::getValue($shipping, 'body.mode');
                    $pedido_mercado_livre_shipments->created_by                             = (string) ArrayHelper::getValue($shipping, 'body.created_by');
                    $pedido_mercado_livre_shipments->order_id                               = (string) ArrayHelper::getValue($shipping, 'body.order_id');
                    $pedido_mercado_livre_shipments->order_cost                             = (float) ArrayHelper::getValue($shipping, 'body.order_cost');
                    $pedido_mercado_livre_shipments->base_cost                              = (float) ArrayHelper::getValue($shipping, 'body.base_cost');
                    $pedido_mercado_livre_shipments->site_id                                = (string) ArrayHelper::getValue($shipping, 'body.site_id');
                    $pedido_mercado_livre_shipments->status                                 = (string) ArrayHelper::getValue($shipping, 'body.status');
                    $pedido_mercado_livre_shipments->substatus                              = (string) ArrayHelper::getValue($shipping, 'body.substatus');
                    $pedido_mercado_livre_shipments->history_date_cancelled                 = (isset($shipping["body"]->status_history->date_cancelled)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_cancelled'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_delivered                 = (isset($shipping["body"]->status_history->date_delivered)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_delivered'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_first_visit               = (isset($shipping["body"]->status_history->date_first_visit)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_first_visit'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_handling                  = (isset($shipping["body"]->status_history->date_handling)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_handling'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_not_delivered             = (isset($shipping["body"]->status_history->date_not_delivered)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_not_delivered'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_ready_to_ship             = (isset($shipping["body"]->status_history->date_ready_to_ship)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_ready_to_ship'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_shipped                   = (isset($shipping["body"]->status_history->date_shipped)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_shipped'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_returned                  = (isset($shipping["body"]->status_history->date_returned)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_returned'),0,19)) : "";
                    $pedido_mercado_livre_shipments->date_created                           = (isset($shipping["body"]->status_history->date_created)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_created'),0,19)) : "";
                    $pedido_mercado_livre_shipments->last_updated                           = (isset($shipping["body"]->status_history->last_updated)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.last_updated'),0,19)) : "";
                    $pedido_mercado_livre_shipments->tracking_number                        = (string) ArrayHelper::getValue($shipping, 'body.tracking_number');
                    $pedido_mercado_livre_shipments->tracking_method                        = (string) ArrayHelper::getValue($shipping, 'body.tracking_method');
                    $pedido_mercado_livre_shipments->service_id                             = (string) ArrayHelper::getValue($shipping, 'body.service_id');
                    $pedido_mercado_livre_shipments->sender_id                              = (string) ArrayHelper::getValue($shipping, 'body.sender_id');
                    $pedido_mercado_livre_shipments->receiver_id                            = (string) ArrayHelper::getValue($shipping, 'body.receiver_id');
                    $pedido_mercado_livre_shipments->receiver_address_id                    = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.id');
                    $pedido_mercado_livre_shipments->receiver_address_address_line          = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.address_line');
                    $pedido_mercado_livre_shipments->receiver_address_street_name           = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_name');
                    $pedido_mercado_livre_shipments->receiver_address_street_number         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_number');
                    $pedido_mercado_livre_shipments->receiver_address_comment               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.comment');
                    $pedido_mercado_livre_shipments->receiver_address_zip_code              = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.zip_code');
                    $pedido_mercado_livre_shipments->receiver_address_city_id               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.id');
                    $pedido_mercado_livre_shipments->receiver_address_city_name             = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.name');
                    $pedido_mercado_livre_shipments->receiver_address_state_id              = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.id');
                    $pedido_mercado_livre_shipments->receiver_address_state_name            = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.name');
                    $pedido_mercado_livre_shipments->receiver_address_country_id            = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.id');
                    $pedido_mercado_livre_shipments->receiver_address_country_name          = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.name');
                    $pedido_mercado_livre_shipments->receiver_address_neighborhood_id       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.id');
                    $pedido_mercado_livre_shipments->receiver_address_neighborhood_name     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.name');
                    $pedido_mercado_livre_shipments->receiver_address_municipality_id       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.id');
                    $pedido_mercado_livre_shipments->receiver_address_municipality_name     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.name');
                    $pedido_mercado_livre_shipments->receiver_address_delivery_preference   = (isset($shipping["body"]->receiver_address->delivery_preference)) ? (string) ArrayHelper::getValue($shipping, 'body.receiver_address.delivery_preference') : "";
                    $pedido_mercado_livre_shipments->receiver_address_receiver_name         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_name');
                    $pedido_mercado_livre_shipments->receiver_address_receiver_phone        = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_phone');
                    $pedido_mercado_livre_shipments->shipping_option_id                     = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.id');
                    $pedido_mercado_livre_shipments->shipping_option_shipping_method_id     = (isset($shipping["body"]->shipping_option->shipping_method_id)) ? (string) ArrayHelper::getValue($shipping, 'body.shipping_option.shipping_method_id') : "";
                    $pedido_mercado_livre_shipments->shipping_option_name                   = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.name');
                    $pedido_mercado_livre_shipments->shipping_option_currency_id            = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.currency_id');
                    $pedido_mercado_livre_shipments->shipping_option_list_cost              = (float) ArrayHelper::getValue($shipping, 'body.shipping_option.list_cost');
                    $pedido_mercado_livre_shipments->shipping_option_cost                   = (float) ArrayHelper::getValue($shipping, 'body.shipping_option.cost');
                    $pedido_mercado_livre_shipments->delivery_type                          = (isset($shipping["body"]->shipping_option->delivery_type)) ? (string) ArrayHelper::getValue($shipping, 'body.shipping_option.delivery_type') : "";
                    $pedido_mercado_livre_shipments->comments                               = (string) ArrayHelper::getValue($shipping, 'body.comments');
                    $pedido_mercado_livre_shipments->date_first_printed                     = str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.date_first_printed'),0,19));
                    $pedido_mercado_livre_shipments->market_place                           = (string) ArrayHelper::getValue($shipping, 'body.market_place');
                    $pedido_mercado_livre_shipments->type                                   = (isset($shipping["body"]->type)) ? (string) ArrayHelper::getValue($shipping, 'body.type') : "";
                    $pedido_mercado_livre_shipments->logistic_type                          = (isset($shipping["body"]->logistic_type)) ? (string) ArrayHelper::getValue($shipping, 'body.logistic_type') : "";
                    $pedido_mercado_livre_shipments->application_id                         = (isset($shipping["body"]->application_id)) ? (string) ArrayHelper::getValue($shipping, 'body.application_id') : "";
                    $pedido_mercado_livre_shipments->pedido_mercado_livre_id                = $pedido_mercado_livre->id;
                    
                    
                    //echo "<pre>===>>"; var_dump($pedido_mercado_livre_shipments); echo "<<===";
                    //die;
                    
                    //echo "======>>>"; var_dump($pedido_mercado_livre_shipments->save()); echo "<<<======";
                    
                    if($pedido_mercado_livre_shipments->save()){
                        $status_retorno .= "\nEnvio tabela criado";
                        
                        foreach(ArrayHelper::getValue($shipping, 'body.shipping_items') as $shipping_item){
                            
                            $status_item = "alterado";
                            $pedido_mercado_livre_shipments_item = PedidoMercadoLivreShipmentsItem::find()->andWhere(['=', 'pedido_mercado_livre_shipments_id', $pedido_mercado_livre_shipments->id])
                            ->andWhere(['=', 'meli_id', $shipping_item->id])
                            ->one();
                            if(!$pedido_mercado_livre_shipments_item){
                                $pedido_mercado_livre_shipments_item = new PedidoMercadoLivreShipmentsItem;
                                $status_item = "criado";
                            }
                            
                            //echo "<pre>"; print_r($shipping_item); echo "</pre>"; die;
                            $pedido_mercado_livre_shipments_item->pedido_mercado_livre_shipments_id   = $pedido_mercado_livre_shipments->id;
                            $pedido_mercado_livre_shipments_item->meli_id                             = (string) $shipping_item->id;
                            $pedido_mercado_livre_shipments_item->description                         = (string) $shipping_item->description;
                            $pedido_mercado_livre_shipments_item->quantity                            = $shipping_item->quantity;
                            $pedido_mercado_livre_shipments_item->dimensions                          = (string) $shipping_item->dimensions;
                            $pedido_mercado_livre_shipments_item->dimensions_source_id                = (string) (isset($shipping["body"]->dimensions_source->id)) ? ArrayHelper::getValue($shipping, 'body.dimensions_source.id') : "";
                            $pedido_mercado_livre_shipments_item->dimensions_source_origin            = (string) (isset($shipping["body"]->dimensions_source->origin)) ? ArrayHelper::getValue($shipping, 'body.dimensions_source.origin') : "";
                            if($pedido_mercado_livre_shipments_item->save()){
                                $status_retorno .= "\nProduto do pedido ".$status_item;
                            }
                            else{
                                $status_retorno .= "\nProduto do pedido ".$status_item;
                            }
                        }
                    }
                    else{
                        echo "\nEnvio tabela não criado";
                        
                        
                    }
                }
                //SHIPPING
            }
        }        
    }
    
}

/**
 * Classe para contenção de escopos da PedidoMercadoLivre, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 15/06/2020
*/
class PedidoMercadoLivreQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/06/2020
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido_mercado_livre.nome' => $sort_type]);
    }
}
