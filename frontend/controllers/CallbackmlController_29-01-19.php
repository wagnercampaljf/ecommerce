<?php
namespace frontend\controllers;

use Yii;
use yii\base\Controller;
use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use console\controllers\actions\omie\Omie;
use common\models\ProdutoFilial;

class CallbackmlController extends Controller
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
	if (empty($post_ml)) die();

	if (ArrayHelper::getValue($post_ml, 'topic')=="orders" or ArrayHelper::getValue($post_ml, 'topic')=="created_orders" or ArrayHelper::getValue($post_ml, 'topic')=="orders_v2"){
		$meli = new Meli('3029992417140266', '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh');
	        //$user = $meli->refreshAccessToken('TG-5b5f1c7be4b09e746623a2ca-193724256');
	        $user = $meli->refreshAccessToken('TG-5c4f26ef9b69e60006493768-193724256');
		$response = ArrayHelper::getValue($user, 'body');

	        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
	            $meliAccessToken = $response->access_token;
        	    $response_order = $meli->get(ArrayHelper::getValue($post_ml, 'resource')."?access_token=" . $meliAccessToken);

		    //Adicionar novo CLIENTE
	     	    $body = [
	                "call" => "IncluirCliente",
	                "app_key" => static::APP_KEY_OMIE_SP,
	                "app_secret" => static::APP_SECRET_OMIE_SP,
	                "param" => [
	                    "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
	                    "razao_social"              => ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'),
	                    "cnpj_cpf"                  => ArrayHelper::getValue($response_order, 'body.buyer.billing_info.doc_number'),
			    "nome_fantasia"             => ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'),
	                    "telefone1_ddd"             => ArrayHelper::getValue($response_order, 'body.buyer.phone.area_code'),
	                    "telefone1_numero"          => ArrayHelper::getValue($response_order, 'body.buyer.phone.number'),
	                    "contato"                   => ArrayHelper::getValue($response_order, 'body.shipping.receiver_address.receiver_name'),
	                    "endereco"                  => ArrayHelper::getValue($response_order, 'body.shipping.receiver_address.street_name'),
	                    "endereco_numero"           => substr(ArrayHelper::getValue($response_order, 'body.shipping.receiver_address.street_number'),0,10),
	                    "bairro"                    => ((ArrayHelper::getValue($response_order, 'body.shipping.receiver_address.neighborhood.name')=="") ? "Centro" : ArrayHelper::getValue($response_order, 'body.shipping.receiver_address.neighborhood.name')),
	                    "complemento"               => ArrayHelper::getValue($response_order, 'body.shipping.receiver_address.comment'),
	                    "estado"                    => substr(ArrayHelper::getValue($response_order, 'body.shipping.receiver_address.state.id'),-2),
	                    "cidade"                    => ArrayHelper::getValue($response_order, 'body.shipping.receiver_address.city.name'),
	                    "cep"                       => ArrayHelper::getValue($response_order, 'body.shipping.receiver_address.zip_code'),
	                    "email"                     => "cliente.pecaagora@pecaagora.com",//ArrayHelper::getValue($response_order, 'body.buyer.email'),
	                ]
	            ];
	            $meli = new Omie(1,1);
		    $response_omie = $meli->cria_cliente("api/v1/geral/clientes/?JSON=",$body);

		    $body = [
	                "call" => "ConsultarCliente",
	                "app_key" => static::APP_KEY_OMIE_SP,
	                "app_secret" => static::APP_SECRET_OMIE_SP,
	                "param" => [
	                    "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
	                ]
	            ];
	            $response_omie = $meli->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
	            print_r(ArrayHelper::getValue($response_omie, 'body.codigo_cliente_omie'));
		    print_r($response_omie);

		    //Adicionar novo PEDIDO
		    $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
		    //echo " <==> Produto_filial: ";print_r($produtoML);

		    $cfop   = "6.102";
           	    $csosn  = "102";
            	    if (substr(ArrayHelper::getValue($response_order, 'body.shipping.receiver_address.state.id'),-2) == "SP"){
                	$cfop   = "5.405";
                	$csosn  = "500";
            	    }

		    $body = [
                        "call" => "IncluirPedido",
                        "app_key" => static::APP_KEY_OMIE_SP,
                        "app_secret" => static::APP_SECRET_OMIE_SP,
                        "param" => [
                            "cabecalho"                     => [
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
                                        "codigo_produto_integracao" => $produtoML->produto->codigo_global,
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
                            "informacoes_adicionais" => [
                                "numero_contrato"       => ArrayHelper::getValue($response_order, 'body.payments.0.id'),
                                "numero_pedido_cliente" => ArrayHelper::getValue($response_order, 'body.id'),
				"consumidor_final"      => "S",
                                "codigo_categoria"      => "1.01.03",
				"codVend"               => 500726231,
                                "codigo_conta_corrente" => 502875713,
                                ],
                            ],
                        ];
                        echo " <==> Body Pedido: ";print_r($body);

                    $response_omie = $meli->cria_pedido("api/v1/produtos/pedido/?JSON=",$body);
                    echo " <==> Resposta Pedido: "; print_r($response_omie);
            	}
	    }
    }
}

