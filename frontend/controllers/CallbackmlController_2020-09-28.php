<?php
namespace frontend\controllers;

use Yii;
use yii\base\Controller;
use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use console\controllers\actions\omie\Omie;
use common\models\PedidoMercadoLivre;
use common\models\ProdutoFilial;
use common\models\Filial;
use common\models\PedidoMercadoLivreProduto;
use common\models\PedidoMercadoLivrePagamento;
use common\models\Produto;

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
        
        //$produto_filial = ProdutoFilial::find()->andWhere(['=','produto_id',355259])->one();
        //$imposto = $this->gerarImposto(12, $produto_filial->produto);

        $json = file_get_contents('php://input');
        $post_ml = json_decode($json);
        //Treco de teste, começo
        /*$post_ml = [
            //"resource"          => "/orders/2595917388",
            "resource"          => "/orders/2516612783",
            "user_id"           => 193724256,
            "topic"             => "orders_v2",
            "application_id"    => 3029992417140266,
            "attempts"          => 1,
            "sent"              => "2019-01-29T11:26:26.150Z",
            "received"          => "2019-01-29T11:26:26.126Z"
        ];*/
        /*$post_ml = [
            "resource"          => "/orders/2597931131",
            "user_id"           => 435343067,
            "topic"             => "orders_v2",
            "application_id"    => 3029992417140266,
            "attempts"          => 1,
            "sent"              => "2019-01-29T11:26:26.150Z",
            "received"          => "2019-01-29T11:26:26.126Z"
        ];*/
        //Treco de teste, fim
        if (empty($post_ml)) die();

        //echo "<pre>"; print_r($post_ml); echo "</pre>";

        if (ArrayHelper::getValue($post_ml, 'topic')=="orders" or ArrayHelper::getValue($post_ml, 'topic')=="created_orders" or ArrayHelper::getValue($post_ml, 'topic')=="orders_v2"){
            $meli = new Meli('3029992417140266', '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh');
    	    //$user = $meli->refreshAccessToken('TG-5e2efe08144ef6000642cdb6-193724256');
    	    $filial = Filial::find()->andWhere(['=', 'id', 72])->one();
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);

            if (ArrayHelper::getValue($post_ml, 'user_id')=="435343067"){
        		$filial = Filial::find()->andWhere(['=', 'id', 98])->one();
        		$user = $meli->refreshAccessToken($filial->refresh_token_meli);
        		//print_r($user); die;
    	    }

            $response = ArrayHelper::getValue($user, 'body');

            //print_r($response);
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                
                //Obter dados do pedido do ML
                $response_order = $meli->get(ArrayHelper::getValue($post_ml, 'resource')."?access_token=" . $meliAccessToken);
                if ($response_order['httpCode'] >= 300) {
                    echo "Sem informações do Pedido";
                }
                else {
                    $this->criarPedidoMercadoLivre($response_order);
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

                $envio_dados = $meli->get("/shipments/".ArrayHelper::getValue($response_order, 'body.shipping.id')."?access_token=" . $meliAccessToken);
                //echo "<pre>"; print_r($envio_dados); echo "</pre>"; //die;

                if ($envio_dados['httpCode'] >= 300) {
                    echo "Sem informações de envio";
                }
                else {
                    $this->criarPedidoMercadoLivre($response_order, $envio_dados);
                }
                
                //die;
                $meli = new Omie(1,1);
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
                    //echo ArrayHelper::getValue($response_order, 'body.order_items.0.item.id'); die;
                    $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                    if(!$produtoML){
                        $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id_sem_juros',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                        
                        if(!$produtoML){
                            $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id_full',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                        }
                    }
                    //echo " <==> Produto_filial: ";print_r($produtoML);

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
                    
                    //echo "<pre>"; var_dump($produtoML); echo "</pre>"; die;
                    
                    $imposto = array();
                    $imposto = $this->gerarImposto($csosn, $produtoML->produto);
                    
                    /*$imposto = array();
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
                        40161010,
                        6813,
                        70071100,
                        70072100,
                        70091000,
                        83012000,
                        83023000,
                        84073390,
                        84073490,
                        840820,
                        840991,
                        840999,
                        841330,
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
                        94012000
                    ];
                    
                    //echo "<pre>"; print_r($imposto); echo "</pre>";
                    
                    foreach($codigos as $k => $codigo){
                        
                        //echo "<br>".$k." - ".$codigo;
                        
                        $quantidade_caracteres = strlen($codigo);
                        //echo "<pre>"; var_dump($produtoML); echo "</pre>"; die;
                        $ncm = str_replace('.','',$produtoML->produto->codigo_montadora);
                        $sub_ncm = substr($ncm,0,$quantidade_caracteres);
                        
                        //echo " - ".$quantidade_caracteres." - ".$sub_ncm;
                        
                        if($sub_ncm == $codigo){
                            $imposto["cofins_padrao"]["cod_sit_trib_cofins"] = "06";
                            $imposto["cofins_padrao"]["tipo_calculo_cofins"] = "";
                            
                            $imposto["pis_padrao"]["cod_sit_trib_pis"] = "06";
                            $imposto["pis_padrao"]["tipo_calculo_pis"] = "";
                            
                            break;
                        }
                    }*/
                    
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
				                    "codigo_produto_integracao" => "PA".$produtoML->produto->id,
                                    //"codigo" => $produtoML->produto->codigo_global,
                                    "cfop"                      => $cfop,
                                    "quantidade"                => ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                                    "valor_unitario"            => ArrayHelper::getValue($response_order, 'body.order_items.0.unit_price'),
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
                }
                
                ////////////////////////////////////////////////////////////////////////////////
                //CONTA DUPLICADA OMIE
                ////////////////////////////////////////////////////////////////////////////////
                if (ArrayHelper::getValue($post_ml, 'user_id')=="435343067"){
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
                        
                        $body = [
                            "call" => "IncluirCliente",
                            "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                            "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                            "param" => [
                                "codigo_cliente_integracao" => substr(ArrayHelper::getValue($response_order, 'body.buyer.id'),0,20),
                                "razao_social"              => str_replace(" ","%20",substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'), 0, 60)),
                                "cnpj_cpf"                  => substr(ArrayHelper::getValue($response_order, 'body.buyer.billing_info.doc_number'),0,20),
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
                            "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                            "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                            "param" => [
                                "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                            ]
                        ];
                        $response_omie = $meli->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
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
                                        "codigo_produto_integracao" => "PA".$produtoML->produto->id,
                                        //"codigo" => $produtoML->produto->codigo_global,
                                        "cfop"                      => $cfop,
                                        "quantidade"                => ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                                        "valor_unitario"            => ArrayHelper::getValue($response_order, 'body.order_items.0.unit_price'),
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
                    }
                }
                ////////////////////////////////////////////////////////////////////////////////
                //CONTA DUPLICADA OMIE
                ////////////////////////////////////////////////////////////////////////////////
            }
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
            40161010,
            6813,
            70071100,
            70072100,
            70091000,
            83012000,
            83023000,
            84073390,
            84073490,
            840820,
            840991,
            840999,
            841330,
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
            94012000
        ];
        
        //echo "<pre>"; print_r($imposto); echo "</pre>";
        
        foreach($codigos as $k => $codigo){
            
            //echo "<br>".$k." - ".$codigo;
            
            $quantidade_caracteres = strlen($codigo);
            $ncm = str_replace('.','',$produto->codigo_montadora);
            $sub_ncm = substr($ncm,0,$quantidade_caracteres);
            
            //echo " - ".$quantidade_caracteres." - ".$sub_ncm;
            
            if($sub_ncm == $codigo){
                $imposto["cofins_padrao"]["cod_sit_trib_cofins"] = "06";
                $imposto["cofins_padrao"]["tipo_calculo_cofins"] = "";
                
                $imposto["pis_padrao"]["cod_sit_trib_pis"] = "06";
                $imposto["pis_padrao"]["tipo_calculo_pis"] = "";

                break;
            }
        }
        
        //echo "<pre>"; print_r($imposto); echo "</pre>";
        
    }
    
    public function criarPedidoMercadoLivre($order = null, $shipping = null){
        
        //echo "<pre>"; print_r($order); echo "</pre>";
        //echo "<pre>"; print_r($shipping); echo "</pre>";die;
        
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
            $pedido_mercado_livre->buyer_nickname       = (string) ArrayHelper::getValue($order, 'body.buyer.nickname');
            $pedido_mercado_livre->buyer_email          = (string) ArrayHelper::getValue($order, 'body.buyer.email');
            $pedido_mercado_livre->buyer_first_name     = (string) ArrayHelper::getValue($order, 'body.buyer.first_name');
            $pedido_mercado_livre->buyer_last_name      = (string) ArrayHelper::getValue($order, 'body.buyer.last_name');
            $pedido_mercado_livre->buyer_doc_type       = (string) ArrayHelper::getValue($order, 'body.buyer.billing_info.doc_type');
            $pedido_mercado_livre->buyer_doc_number     = (string) ArrayHelper::getValue($order, 'body.buyer.billing_info.doc_number');
            $pedido_mercado_livre->user_id              = (string) ArrayHelper::getValue($order, 'body.seller.id');
            
            if(is_null($pedido_mercado_livre->email_enderecos)){
                $pedido_mercado_livre->email_enderecos = "entregasp.pecaagora@gmail.com; notafiscal.pecaagora@gmail.com; compras.pecaagora@gmail.com; entregasp.pecaagora@gmail.com";

            }
            
            if($pedido_mercado_livre->save()){
                echo "Pedido alterado";
            }
            else{
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
            $pedido_mercado_livre->buyer_nickname       = (string) ArrayHelper::getValue($order, 'body.buyer.nickname');
            $pedido_mercado_livre->buyer_email          = (string) ArrayHelper::getValue($order, 'body.buyer.email');
            $pedido_mercado_livre->buyer_first_name     = (string) ArrayHelper::getValue($order, 'body.buyer.first_name');
            $pedido_mercado_livre->buyer_last_name      = (string) ArrayHelper::getValue($order, 'body.buyer.last_name');
            $pedido_mercado_livre->buyer_doc_type       = (string) ArrayHelper::getValue($order, 'body.buyer.billing_info.doc_type');
            $pedido_mercado_livre->buyer_doc_number     = (string) ArrayHelper::getValue($order, 'body.buyer.billing_info.doc_number');
            $pedido_mercado_livre->user_id              = (string) ArrayHelper::getValue($order, 'body.seller.id');
            $pedido_mercado_livre->email_enderecos      = "entregasp.pecaagora@gmail.com; notafiscal.pecaagora@gmail.com; compras.pecaagora@gmail.com; entregasp.pecaagora@gmail.com";
            
            if($pedido_mercado_livre->save()){
                echo "Pedido criado";
            }
            else{
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
                    echo "<br><br><br>Shipping alterado";
                }
                else{
                    echo "<br><br><br>Shipping não alterado";
                }
            }
            
            //Cadastra os dados dos produtos
            
            $produtos_email = "";
            
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
                    
                    $produto_filial = ProdutoFilial::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])->one();
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
                    
                    $produto_filial = ProdutoFilial::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])->one();
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
                
                $produtos_email .= "\n\nCódigo: {codigo}";
                /*if($produto_filial){
                    $produtos_email .= $produto_filial->produto->codigo_fabricante;
                }*/
                $produtos_email .= "\nDescrição: {descricao}";//.ArrayHelper::getValue($produto, 'item.title');
                $produtos_email .= "\nQuantidade: {quantidade}";//.ArrayHelper::getValue($produto, 'quantity');
                $produtos_email .= "\nValor: {valor}";
		$produtos_email .= "\nObservação: {observacao}";
                /*if($pedido_mercado_livre_produto->valor_cotacao <> null && $pedido_mercado_livre_produto->valor_cotacao > 0){
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
            $pedido_mercado_livre->save();
        }
        
        echo "<br><br><br>";
    }
}


