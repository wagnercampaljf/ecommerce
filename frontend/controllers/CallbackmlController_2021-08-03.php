<?php
//1111
namespace frontend\controllers;

use Yii;
use yii\base\Controller;
use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use console\controllers\actions\omie\Omie;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreShipmentsItem;
use common\models\ProdutoFilial;
use common\models\Filial;
use common\models\PedidoMercadoLivreProduto;
use common\models\PedidoMercadoLivrePagamento;
use common\models\Produto;
use common\models\PedidoMercadoLivreShipments;

class CallbackmlController extends Controller
{

    const APP_KEY_OMIE_SP                   = '468080198586';
    const APP_SECRET_OMIE_SP                = '7b3fb2b3bae35eca3b051b825b6d9f43';

    const APP_KEY_OMIE_MG                   = '469728530271';
    const APP_SECRET_OMIE_MG                = '6b63421c9bb3a124e012a6bb75ef4ace';
    
    const APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
    const APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';

    public function actionIndex()
    {
        //Yii::$app->user->setReturnUrl(Url::to(['site/index']));

        return $this->render('index');
    }

    public function actionPedidoml(){
        
        $arquivo_log_nome = "/var/tmp/callbackml/log_callback_ml_".date("Y-m-d_H-i-s").".csv";
        $arquivo_log = fopen($arquivo_log_nome, "a");
        fwrite($arquivo_log, date("Y-m-d_H-i-s")."\n");

        //$produto_filial = ProdutoFilial::find()->andWhere(['=','produto_id',355259])->one();
        //$imposto = $this->gerarImposto(12, $produto_filial->produto);

        $json = file_get_contents('php://input');
        $post_ml = json_decode($json);
        //Treco de teste, começo
	//$post_ml = ["resource"=>"/orders/4398238575","user_id"=>193724256,"topic"=>"orders_v2","application_id"=>3029992417140266,"attempts"=>1,"sent"=>"2019-01-29T11:26:26.150Z","received"=>"2019-01-29T11:26:26.126Z"];
    	/*$post_ml = [
            "resource"          => "/orders/4696972078",
            "user_id"           => 193724256,
            "topic"             => "orders_v2",
            "application_id"    => 3029992417140266,
            "attempts"          => 1,
            "sent"              => "2019-01-29T11:26:26.150Z",
            "received"          => "2019-01-29T11:26:26.126Z"
        ];*/
	/*$post_ml = [
            "resource"          => "/orders/4429842456",
            "user_id"           => 435343067,
            "topic"             => "orders_v2",
            "application_id"    => 3029992417140266,
            "attempts"          => 1,
            "sent"              => "2019-01-29T11:26:26.150Z",
            "received"          => "2019-01-29T11:26:26.126Z"
        ];*/
        //Treco de teste, fim
        if (empty($post_ml)){
		fwrite($arquivo_log, "\nPOST vazio\n");
		die();
	}
	else{
		fwrite($arquivo_log, "\nPOST não vazio\n");
		//$texto_post = implode(";", $post_ml);
	        //fwrite($arquivo_log, "\n".$texto_post."\n");
	}

	fwrite($arquivo_log, "\nblablabla\n");
	fclose($arquivo_log);

	$nome = "_SEM_RESOURCE";
	if(array_key_exists("resource", $post_ml)){
		$nome = str_replace("/", "_", ArrayHelper::getValue($post_ml, 'resource'));
	}
	$arquivo_log_nome = "/var/tmp/callbackml/log_callback_ml".$nome."_".date("Y-m-d_H-i-s").".csv";
        $arquivo_log = fopen($arquivo_log_nome, "a");
        fwrite($arquivo_log, date("Y-m-d_H-i-s")."\n");
	//$texto_post = implode(";", $post_ml);
	//fwrite($arquivo_log, "\n".$texto_post."\n");

        //echo "<pre>"; print_r($post_ml); echo "</pre>";
        
        //Variáveis de status
        $e_pedido_criado_alterado_peca_agora = false;
        $e_pedido_criado_alterado_omie       = false;

    	fwrite($arquivo_log, "\n".ArrayHelper::getValue($post_ml, 'user_id').";".ArrayHelper::getValue($post_ml, 'resource'));
    	fwrite($arquivo_log, "\n\n".date("Y-m-d_H-i-s"));
        fclose($arquivo_log);

        if (ArrayHelper::getValue($post_ml, 'topic')=="orders" or ArrayHelper::getValue($post_ml, 'topic')=="created_orders" or ArrayHelper::getValue($post_ml, 'topic')=="orders_v2"){

	    //MANEIRA NOVA DE PUXAR OS PEDIDOS
	    $pedido_meli_id = str_replace("/orders/", "", ArrayHelper::getValue($post_ml, 'resource'));
	    //PedidoMercadoLivre::baixarPedidoML($pedido_meli_id, false);
	    http_response_code(200);
	    return PedidoMercadoLivre::baixarPedidoML($pedido_meli_id, true);

            $meli = new Meli('3029992417140266', '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh');
    	    //$user = $meli->refreshAccessToken('TG-5e2efe08144ef6000642cdb6-193724256');
    	    $filial = Filial::find()->andWhere(['=', 'id', 72])->one();
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);

            $codigo_cenario_impostos = "2602379535";

            if (ArrayHelper::getValue($post_ml, 'user_id')=="435343067"){
        		$filial = Filial::find()->andWhere(['=', 'id', 98])->one();
        		$user = $meli->refreshAccessToken($filial->refresh_token_meli);
			    $codigo_cenario_impostos = "1128355333";
        		//print_r($user); die;
    	    }

            $response = ArrayHelper::getValue($user, 'body');

            //print_r($response);
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                
                //Obter dados do pedido do ML
                $response_order = $meli->get(ArrayHelper::getValue($post_ml, 'resource')."?access_token=" . $meliAccessToken);
                
                $billing_info   = $meli->get(ArrayHelper::getValue($post_ml, 'resource')."/billing_info;?access_token=" . $meliAccessToken);
                
                //echo "<pre>"; print_r($response_order); echo "</pre>"; die;
                if ($response_order['httpCode'] >= 300) {
                    echo "Sem informações do Pedido";
                }
                else {
                    $e_pedido_criado_alterado_peca_agora = $this->criarPedidoMercadoLivre($response_order);
                }
                //echo "<pre>"; print_r($response_order); echo "</pre>"; //die;

                /*if(ArrayHelper::keyExists('body.shipping.receiver_address', $response_order, false)){
                    Yii::$app->response->statusCode = 400;
                    return "Sem endereço";
                }*/

        		if(ArrayHelper::keyExists('body.shipping.id', $response_order, false)){
                    Yii::$app->response->statusCode = 400;
                    return "Sem endere  o";
                }

                //echo "<pre>"; print_r($meliAccessToken); echo "</pre>";die;
                $envio_dados = $meli->get("/shipments/".ArrayHelper::getValue($response_order, 'body.shipping.id')."?access_token=" . $meliAccessToken);
                //echo "<pre>"; print_r($envio_dados); echo "</pre>"; die;

                if ($envio_dados['httpCode'] >= 300) {
                    echo "Sem informações de envio";
                }
                else {
                    $e_pedido_criado_alterado_peca_agora = $this->criarPedidoMercadoLivre($response_order, $envio_dados);
                }


                //die;
                $meli = new Omie(1,1);

                if (ArrayHelper::getValue($post_ml, 'user_id')!="435343067"){
                    $body = [
                        "call" => "ConsultarCliente",
                        "app_key" => static::APP_KEY_OMIE_SP,
                        "app_secret" => static::APP_SECRET_OMIE_SP,
                        "param" => [
                            "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                        ]
                    ];
                    $response_omie = $meli->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
                    
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
                                "razao_social"              => str_replace(" ","%20",substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'), 0, 60)),
                                "cnpj_cpf"                  => substr(ArrayHelper::getValue($response_order, 'body.buyer.billing_info.doc_number'),0,20),
                                //"cnpj_cpf"                  => substr($billing_info["body"]->billing_info->doc_number,0,20),
                                "nome_fantasia"             => str_replace(" ","%20",substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'),0,100)),
                                //"telefone1_ddd"             => substr(ArrayHelper::getValue($response_order, 'body.buyer.phone.area_code'),0,5),
                                //"telefone1_numero"          => substr(ArrayHelper::getValue($response_order, 'body.buyer.phone.number'),0,15),
                                "telefone1_ddd"             => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'),0,5),
                                "telefone1_numero"          => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'),0,15),
                                "contato"                   => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_name'),0,100)),
                                "endereco"                  => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_name'),0,60)),
                                "endereco_numero"           => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_number'),0,10),
                                "bairro"                    => str_replace(" ","%20",substr(((ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')=="") ? "Centro" : ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')),0,30)),
                                "complemento"               => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.comment'),0,40),
                                "estado"                    => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'),-2)),
                                "cidade"                    => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.city.name'),0,40)),
                                "cep"                       => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.zip_code'),0,10),
                                "email"                     => "cliente.pecaagora@gmail.com",//ArrayHelper::getValue($response_order, 'body.buyer.email'),
                            ]
                        ];
    
    
    
    
                        $response_omie = $meli->cria_cliente("api/v1/geral/clientes/?JSON=",$body);
                        echo "<br><br>"; print_r($response_omie);
    
                        $body = [
                            "call" => "ConsultarCliente",
                            "app_key" => static::APP_KEY_OMIE_SP,
                            "app_secret" => static::APP_SECRET_OMIE_SP,
                            "param" => [
                                "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                            ]
                        ];
                        $response_omie = $meli->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
                    }
    
                    //Verificar se existe pedido
                    $body = [
                        "call" => "ConsultarPedido",
                        "app_key" => static::APP_KEY_OMIE_SP,
                        "app_secret" => static::APP_SECRET_OMIE_SP,
                        "param" => [
                            "codigo_pedido_integracao" => ArrayHelper::getValue($response_order, 'body.id'),
                        ]
                    ];
                    $response_omie = $meli->consulta_pedido("api/v1/geral/pedidos/?JSON=",$body);
    
    
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
                        //echo " <==> Produto_filial: ";print_r($produtoML);
                        
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
                            
                            $produto_unitario   = Produto::find()->andWhere(['=', 'codigo_global', $codigo_global_limpo])->one();
                            $codigo_pa          = "PA".$produto_unitario->id;
                            
                        }
                        
                        //die("<br><br><br><br>");
                        
    
                        //$cfop   = "6.102";
                        $cfop   = "6.108";
                        $csosn  = "102";
                        if (substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'),-2) == "SP"){
                            $cfop   = "5.405";
                            $csosn  = "500";
                        }
    
                        /////////////////////////////
                        //IMPOSTO
                        /////////////////////////////
                        
                        $imposto = array();
                        $imposto = $this->gerarImposto($csosn, $produtoML->produto);
                        
                        /////////////////////////////
                        //IMPOSTO
                        /////////////////////////////
                        
                        $body = [
                            "call" => "IncluirPedido",
                            "app_key" => static::APP_KEY_OMIE_SP,
                            "app_secret" => static::APP_SECRET_OMIE_SP,
                            "param" => [
                                "cabecalho" => [
                                    "bloqueado"                 => "N",
                                    //"codigo_cliente"            => ArrayHelper::getValue($response_omie, 'body.codigo_cliente_omie'),
                                    "codigo_cenario_impostos"   => $codigo_cenario_impostos,//"2602379535",
                                    "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                                    "codigo_pedido_integracao"  => ArrayHelper::getValue($response_order, 'body.id'),
                                    "etapa"                     => "10",
                                    "data_previsao"             => substr(ArrayHelper::getValue($response_order, 'body.date_created'),8,2).'/'.substr(ArrayHelper::getValue($response_order, 'body.date_created'),5,2).'/'.substr(ArrayHelper::getValue($response_order, 'body.date_created'),0,4),
                                    "quantidade_itens"          => ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                                ],
                                "det"=> [
                                    "ide"=> [
                                        "codigo_item_integracao"    => $produtoML->produto->codigo_global,
                                        "regra_impostos"            => 0,
                                        "simples_nacional"           => "",
                                    ],
                                    "imposto" => $imposto,/*[
                                        "cofins_padrao" => [
                                            "cod_sit_trib_cofins"   => 49,
                                            "tipo_calculo_cofins"   => "B",
                                        ],
                                        "ipi" => [
                                            "cod_sit_trib_ipi"  => 99,
                                            "enquadramento_ipi" => 999,
                                            "tipo_calculo_ipi"  => "B",
                                        ],
                                        "pis_padrao" => [
                                            "cod_sit_trib_pis"  => 49,
                                            "tipo_calculo_pis"  => "B",
                                        ],
                                        "icms_sn" => [
                                            "cod_sit_trib_icms_sn" => $csosn,
                                        ],
                                    ],*/
                                    "produto" => [
                                        //"codigo_produto_integracao" => $produtoML->produto->codigo_global,
    				                    "codigo_produto_integracao" => $codigo_pa,//"PA".$produtoML->produto->id,
                                        //"codigo" => $produtoML->produto->codigo_global,
                                        "cfop"                      => $cfop,
                                        "quantidade"                => $quantidade,//ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                                        "valor_unitario"            => $valor_unitario,//ArrayHelper::getValue($response_order, 'body.order_items.0.unit_price'),
                                        //"tipo_desconto"             => "V",
                                        //"valor_desconto"            => ArrayHelper::getValue($response_order, 'body.order_items.0.sale_fee'),
                                    ],
                                ],
                                "frete" => [
                                    "codigo_transportadora" => 505552563,
                                    "modalidade"            => 0,
                                    "quantidade_volumes"    => 1,
                                    "especie_volumes"       => "CAIXA"
                                ],
                                "informacoes_adicionais"    => [
                                    "numero_contrato"           => ArrayHelper::getValue($response_order, 'body.payments.0.id'),
                                    "numero_pedido_cliente"     => ArrayHelper::getValue($response_order, 'body.id'),
                                    "consumidor_final"          => "S",
                                    "codigo_categoria"          => "1.01.03",
                                    "codVend"                   => 500726231,
                                    "codigo_conta_corrente"     => 502875713,
                                ],
                            ],
                        ];
    
                        //echo " <==> Body Pedido: ";print_r($body);
                        $response_omie = $meli->cria_pedido("api/v1/produtos/pedido/?JSON=",$body);
                        echo "<br><br> Resposta Pedido: "; print_r($response_omie);
                        
                        if (ArrayHelper::getValue($response_omie, 'httpCode') == 200){
                            $e_pedido_criado_alterado_omie = true;
                        }
                        else{
                            $e_pedido_criado_alterado_omie = false;
                        }
                    }
                }
                
                ////////////////////////////////////////////////////////////////////////////////
                //CONTA DUPLICADA OMIE
                ////////////////////////////////////////////////////////////////////////////////
                //if (ArrayHelper::getValue($post_ml, 'user_id')=="435343067"){
                else{
                    $body = [
                        "call" => "ConsultarCliente",
                        "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                        "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                        "param" => [
                            "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                        ]
                    ];
                    $response_omie = $meli->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
                    
                    if (ArrayHelper::getValue($response_omie, 'httpCode') == 200){
                        echo "Cliente já cadastrado <br><br>";
                    } else{
                        //Adicionar novo CLIENTE
                        
                        $billing_info   = $meli->get(ArrayHelper::getValue($post_ml, 'resource')."/billing_info;?access_token=" . $meliAccessToken);
                        
                        $body = [
                            "call" => "IncluirCliente",
                            "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                            "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                            "param" => [
                                "codigo_cliente_integracao" => substr(ArrayHelper::getValue($response_order, 'body.buyer.id'),0,20),
                                "razao_social"              => str_replace(" ","%20",substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'), 0, 59)),
                                "cnpj_cpf"                  => substr(ArrayHelper::getValue($response_order, 'body.buyer.billing_info.doc_number'),0,20),
                                //"cnpj_cpf"                  => substr($billing_info["body"]->billing_info->doc_number,0,20),
                                "nome_fantasia"             => str_replace(" ","%20",substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'),0,100)),
                                //"telefone1_ddd"             => substr(ArrayHelper::getValue($response_order, 'body.buyer.phone.area_code'),0,5),
                                //"telefone1_numero"          => substr(ArrayHelper::getValue($response_order, 'body.buyer.phone.number'),0,15),
                                "telefone1_ddd"             => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'),0,5),
                                "telefone1_numero"          => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'),0,15),
                                "contato"                   => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_name'),0,100)),
                                "endereco"                  => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_name'),0,60)),
                                "endereco_numero"           => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_number'),0,10),
                                "bairro"                    => str_replace(" ","%20",substr(((ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')=="") ? "Centro" : ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')),0,30)),
                                "complemento"               => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.comment'),0,40),
                                "estado"                    => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'),-2)),
                                "cidade"                    => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.city.name'),0,40)),
                                "cep"                       => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.zip_code'),0,10),
                                "email"                     => "cliente.pecaagora@gmail.com",//ArrayHelper::getValue($response_order, 'body.buyer.email'),
                            ]
                        ];
                        echo "<br><br>Cliente(Body)"; print_r($body);
                        $response_omie = $meli->cria_cliente("api/v1/geral/clientes/?JSON=",$body);
                        echo "<br><br>Cliente"; print_r($response_omie);
                        
                        $body = [
                            "call" => "ConsultarCliente",
                            "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                            "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                            "param" => [
                                "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                            ]
                        ];
                        $response_omie = $meli->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
                        echo "<br><br>Cliente(Resposta)"; print_r($response_omie);
                    }
                    
                    //Verificar se existe pedido
                    $body = [
                        "call" => "ConsultarPedido",
                        "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                        "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                        "param" => [
                            "codigo_pedido_integracao" => ArrayHelper::getValue($response_order, 'body.id'),
                        ]
                    ];
                    $response_omie = $meli->consulta_pedido("api/v1/geral/pedidos/?JSON=",$body);
                    
                    if (ArrayHelper::getValue($response_omie, 'httpCode') == 200){
                        echo "Pedido já cadastrado <br><br>";
                    } else{
                        //Adicionar novo PEDIDO
                        //echo ArrayHelper::getValue($response_order, 'body.order_items.0.item.id'); die;
                        $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                        if(!$produtoML){
                            $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id_sem_juros',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                            
                            if(!$produtoML){
                                $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id_full',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                            }
                        }
                        //echo " <==> Produto_filial: ";print_r($produtoML);
                        
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
                            
                            $produto_unitario   = Produto::find()->andWhere(['=', 'codigo_global', $codigo_global_limpo])->one();
                            $codigo_pa          = "PA".$produto_unitario->id;
                            
                        }
                        
                        //$cfop   = "6.102";
                        $cfop   = "6.108";
                        $csosn  = "102";
                        if (substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'),-2) == "SP"){
                            $cfop   = "5.405";
                            $csosn  = "500";
                        }
                        
                        $imposto = $this->gerarImposto($csosn, $produtoML->produto);
                        
                        $body = [
                            "call" => "IncluirPedido",
                            "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                            "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                            "param" => [
                                "cabecalho" => [
                                    "bloqueado"                 => "N",
                                    //"codigo_cliente"            => ArrayHelper::getValue($response_omie, 'body.codigo_cliente_omie'),
                                    "codigo_cenario_impostos"   => $codigo_cenario_impostos,//"2602379535",
                                    "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                                    "codigo_pedido_integracao"  => ArrayHelper::getValue($response_order, 'body.id'),
                                    "etapa"                     => "10",
                                    "data_previsao"             => substr(ArrayHelper::getValue($response_order, 'body.date_created'),8,2).'/'.substr(ArrayHelper::getValue($response_order, 'body.date_created'),5,2).'/'.substr(ArrayHelper::getValue($response_order, 'body.date_created'),0,4),
                                    "quantidade_itens"          => ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                                ],
                                "det"=> [
                                    "ide"=> [
                                        "codigo_item_integracao"    => $produtoML->produto->codigo_global,
                                        "regra_impostos"            => 0,
                                        "simples_nacional"           => "",
                                    ],
                                    "imposto" => $imposto,/*[                                        
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
                                    ],*/
                                    "produto" => [
                                        //"codigo_produto_integracao" => $produtoML->produto->codigo_global,
                                        "codigo_produto_integracao" => $codigo_pa,//"PA".$produtoML->produto->id,
                                        //"codigo" => $produtoML->produto->codigo_global,
                                        "cfop"                      => $cfop,
                                        "quantidade"                => $quantidade,//ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                                        "valor_unitario"            => $valor_unitario,//ArrayHelper::getValue($response_order, 'body.order_items.0.unit_price'),
                                        //"tipo_desconto"             => "V",
                                        //"valor_desconto"            => ArrayHelper::getValue($response_order, 'body.order_items.0.sale_fee'),
                                    ],
                                ],
                                "frete" => [
                                    "codigo_transportadora" => 1018250911,
                                    "modalidade"            => 0,
                                    "quantidade_volumes"    => 1,
                                    "especie_volumes"       => "CAIXA"
                                ],
                                "informacoes_adicionais"    => [
                                    "numero_contrato"           => ArrayHelper::getValue($response_order, 'body.payments.0.id'),
                                    "numero_pedido_cliente"     => ArrayHelper::getValue($response_order, 'body.id'),
                                    "consumidor_final"          => "S",
                                    "codigo_categoria"          => "1.01.03",
                                    "codVend"                   => 1018256043,
                                    "codigo_conta_corrente"     => 1018255531,
                                ],
                            ],
                        ];
                        
                        //echo " <==> Body Pedido: ";print_r($body);
                        $response_omie = $meli->cria_pedido("api/v1/produtos/pedido/?JSON=",$body);
                        echo "<br><br> Resposta Pedido: "; print_r($response_omie);
                        
                        if (ArrayHelper::getValue($response_omie, 'httpCode') == 200){
                            $e_pedido_criado_alterado_omie = true;
                        }
                        else{
                            $e_pedido_criado_alterado_omie = false;
                        }
                    }
                }
                ////////////////////////////////////////////////////////////////////////////////
                //CONTA DUPLICADA OMIE
                ////////////////////////////////////////////////////////////////////////////////
            }
        }
        
        if($e_pedido_criado_alterado_omie && $e_pedido_criado_alterado_peca_agora){
            http_response_code(200);
        }
        else{
            http_response_code(404);
            die;
        }
    }
    
    public function gerarImposto($csosn, $produto){

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
        
        //echo "<pre>"; print_r($imposto); echo "</pre>";
        
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
    
    public function criarPedidoMercadoLivre($order = null, $billing_info = null, $shipping = null){
        
        //echo "<pre>"; print_r($order); echo "</pre>";
        //echo "<pre>"; print_r($shipping); echo "</pre>";die;
        $status_pedido_pecaagora    = false;
        $e_pedido_criado_alterado   = false;
        $e_shipping_criado_alterado = false;
        
        if(is_null($order)){
            return;
        }
        
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
            //$pedido_mercado_livre->buyer_doc_type       = (string) (isset($order["body"]->buyer->billing_info->doc_type)) ? ArrayHelper::getValue($order, 'body.buyer.billing_info.doc_type') : "";
            //$pedido_mercado_livre->buyer_doc_number     = (string) (isset($order["body"]->buyer->billing_info->doc_number)) ? ArrayHelper::getValue($order, 'body.buyer.billing_info.doc_number') : "";
            $pedido_mercado_livre->buyer_doc_type       = (string) (isset($billing_info["body"]->billing_info->doc_type)) ? $billing_info["body"]->billing_info->doc_type : "";
            $pedido_mercado_livre->buyer_doc_number     = (string) (isset($billing_info["body"]->billing_info->doc_number)) ? $billing_info["body"]->billing_info->doc_number : "";
            $pedido_mercado_livre->user_id              = (string) ArrayHelper::getValue($order, 'body.seller.id');
            $pedido_mercado_livre->pack_id              = (string) ArrayHelper::getValue($order, 'body.pack_id');
            
            if(is_null($pedido_mercado_livre->email_enderecos)){
                //$pedido_mercado_livre->email_enderecos = "entregasp.pecaagora@gmail.com; notafiscal.pecaagora@gmail.com; compras.pecaagora@gmail.com; entregasp.pecaagora@gmail.com";
		$pedido_mercado_livre->email_enderecos = "notafiscal.pecaagora@gmail.com; compras.pecaagora@gmail.com";

            }
            
            if($pedido_mercado_livre->save()){
                $e_pedido_criado_alterado = true;
                echo "Pedido alterado";
            }
            else{
                $e_pedido_criado_alterado = false;
                echo "Pedido não alterado";
            }
        }
        else{
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
            //$pedido_mercado_livre->buyer_doc_type       = (string) ArrayHelper::getValue($order, 'body.buyer.billing_info.doc_type');
            //$pedido_mercado_livre->buyer_doc_number     = (string) ArrayHelper::getValue($order, 'body.buyer.billing_info.doc_number');
            $pedido_mercado_livre->buyer_doc_type       = (string) (isset($billing_info["body"]->billing_info->doc_type)) ? $billing_info["body"]->billing_info->doc_type : "";
            $pedido_mercado_livre->buyer_doc_number     = (string) (isset($billing_info["body"]->billing_info->doc_number)) ? $billing_info["body"]->billing_info->doc_number : "";
            $pedido_mercado_livre->user_id              = (string) ArrayHelper::getValue($order, 'body.seller.id');
            $pedido_mercado_livre->pack_id              = (string) ArrayHelper::getValue($order, 'body.pack_id');
            //$pedido_mercado_livre->email_enderecos      = "entregasp.pecaagora@gmail.com, notafiscal.pecaagora@gmail.com, compras.pecaagora@gmail.com";
	    $pedido_mercado_livre->email_enderecos      = "notafiscal.pecaagora@gmail.com, compras.pecaagora@gmail.com";
            
            if($pedido_mercado_livre->save()){
                $e_pedido_criado_alterado = true;
                echo "Pedido criado";
            }
            else{
                $e_pedido_criado_alterado = false;
                echo "Pedido não criado";
            }
        }
        
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
                    $e_pedido_criado_alterado = true;
                    echo "<br><br><br>Shipping alterado";
                }
                else{
                    $e_pedido_criado_alterado = true;
                    echo "<br><br><br>Shipping não alterado";
                }
                
                
                //SHIPPING
                $pedido_mercado_livre_shipments = PedidoMercadoLivreShipments::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($shipping, 'body.id')])->one();
                if($pedido_mercado_livre_shipments){
                    echo "<br><br>dados shipments já cadastrados";
                    
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
                        $e_shipping_criado_alterado = true;
                        echo "<br><br><br>Shipping tabela alterado";
                     
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
                                echo "<br><br><br>Shipping Item ".$status_item;
                            }
                            else{
                                echo "<br><br><br>Shipping Item não ".$status_item;
                            }
                        }
                    }
                    else{
                        $e_pedido_criado_alterado = false;
                        echo "<br><br><br>Shipping tabela não alterado";
                    }
                }
                else{
                    echo "<br><br>dados shipments não cadastrados";
                    
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
                        
                        $e_shipping_criado_alterado = true;
                        
                        echo "<br><br><br>Shipping tabela criado";
                        
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
                                echo "<br><br><br>Shipping Item ".$status_item;
                            }
                            else{
                                echo "<br><br><br>Shipping Item não ".$status_item;
                            }
                        }
                    }
                    else{
                        $e_shipping_criado_alterado = false;
                        echo "<br><br><br>Shipping tabela não criado";
                        
                        
                    }
                }
                //SHIPPING
            }
            
            //Cadastra os dados dos produtos
            
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
	    $pedido_mercado_livre->email_assunto = "Pedido {codigo_fabricante} * {quantidade} - {nome} ({margem}) - {pedido_meli_id}";
	    if($pedido_mercado_livre->user_id == "435343067"){
		$pedido_mercado_livre->email_assunto = "Novo Pedido {codigo_fabricante} * {quantidade} - {nome} ({margem}) - {pedido_meli_id}";
	    }
            $pedido_mercado_livre->save();
        }

        echo "<br><br><br>";
        
        if($e_pedido_criado_alterado && $e_shipping_criado_alterado){
            return true;
        }
        else{
            return false;
        }

    }
}


