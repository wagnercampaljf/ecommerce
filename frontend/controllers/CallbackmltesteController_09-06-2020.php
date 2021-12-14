<?php
namespace frontend\controllers;

use Yii;
use yii\base\Controller;
use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use console\controllers\actions\omie\Omie;
use common\models\ProdutoFilial;
use common\models\Filial;

class CallbackmltesteController extends Controller
{

    const APP_KEY_OMIE_SP              = '468080198586';
    const APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';
    const APP_KEY_OMIE_MG              = '469728530271';
    const APP_SECRET_OMIE_MG           = '6b63421c9bb3a124e012a6bb75ef4ace';

    public function actionIndex()
    {
        //Yii::$app->user->setReturnUrl(Url::to(['site/index']));

        return $this->render('index');
    }

    public function actionPedidoml(){

        $json = file_get_contents('php://input');
        $post_ml = json_decode($json);
        //Treco de teste, começo
        $post_ml = [
            "resource"          => "/orders/2444421889",
//            "resource"          => "/orders/2444318765",
            "user_id"           => 193724256,
            "topic"             => "orders_v2",
            "application_id"    => 3029992417140266,
            "attempts"          => 1,
            "sent"              => "2019-01-29T11:26:26.150Z",
            "received"          => "2019-01-29T11:26:26.126Z"
        ];
	/*$post_ml = [
            "resource"          => "/orders/2345557266",
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
	    }

	    $response = ArrayHelper::getValue($user, 'body');

	    //echo "<pre>"; print_r($response); echo "</pre>";die;

	    if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                $response_order = $meli->get(ArrayHelper::getValue($post_ml, 'resource')."?access_token=" . $meliAccessToken);

		//echo "<pre>"; print_r($response_order); echo "</pre>"; die;

		/*if(ArrayHelper::keyExists('body.shipping.receiver_address', $response_order, false)){
		    Yii::$app->response->statusCode = 400;
		    return "Sem endereço";
                }*/

		if(ArrayHelper::keyExists('body.shipping.id', $response_order, false)){
                    Yii::$app->response->statusCode = 400;
                    return "Sem endere  o";
                }

		$envio_dados = $meli->get("/shipments/".ArrayHelper::getValue($response_order, 'body.shipping.id')."?access_token=" . $meliAccessToken);
		//echo "<pre>"; print_r($envio_dados); echo "</pre>"; die;

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
                            "razao_social"              => substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'), 0, 60),
                            "cnpj_cpf"                  => substr(ArrayHelper::getValue($response_order, 'body.buyer.billing_info.doc_number'),0,20),
                            "nome_fantasia"             => substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'),0,100),
                            //"telefone1_ddd"             => substr(ArrayHelper::getValue($response_order, 'body.buyer.phone.area_code'),0,5),
                            //"telefone1_numero"          => substr(ArrayHelper::getValue($response_order, 'body.buyer.phone.number'),0,15),
                            "telefone1_ddd"             => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'),0,5),
                            "telefone1_numero"          => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'),0,15),
                            "contato"                   => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_name'),0,100),
                            "endereco"                  => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_name'),0,60),
                            "endereco_numero"           => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_number'),0,10),
                            "bairro"                    => substr(((ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')=="") ? "Centro" : ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')),0,30),
                            "complemento"               => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.comment'),0,40),
                            "estado"                    => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'),-2),
                            "cidade"                    => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.city.name'),0,40),
                            "cep"                       => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.zip_code'),0,10),
                            "email"                     => "cliente.pecaagora@pecaagora.com",//ArrayHelper::getValue($response_order, 'body.buyer.email'),
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
		    }
                    //echo " <==> Produto_filial: ";print_r($produtoML);

                    //$cfop   = "6.102";
		    $cfop   = "6.108";
                    $csosn  = "102";
                    if (substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'),-2) == "SP"){
                        $cfop   = "5.405";
                        $csosn  = "500";
                    }

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
                                "imposto" => [
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
                                ],
                                "produto" => [
                                    //"codigo_produto_integracao" => $produtoML->produto->codigo_global,
				    "codigo_produto_integracao" => "PA".$produtoML->produto->id,
                                    //"codigo" => $produtoML->produto->codigo_global,
                                    "cfop"                      => $cfop,
                                    "quantidade"                => ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                                    "valor_unitario"            => ArrayHelper::getValue($response_order, 'body.order_items.0.unit_price'),
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
            }
        }
    }
}


