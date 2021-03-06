<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreProduto;
use common\models\Filial;
use Livepixel\MercadoLivre\Meli;

class ReceberContasAReceberAction extends Action
{
    public function run()
    {

	$arquivo_log = fopen("/var/tmp/log_aplicar_desconto_".date("Y-m-d_H-i-s").".csv", "a");

        fwrite($arquivo_log,"Conta principal\n\n\numero_pedido;numero_documento_fiscal;status_titulo;data_vencimento;id_conta_corrente;pedido_mercado_livre_id;status");

        echo "Sincronizar estoque omie...\n\n";
        $omie = new Omie(static::APP_ID, static::SECRET_KEY);

        $APP_KEY_OMIE_SP              = '468080198586';
        $APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';

        $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';

        //TESTE

	$meli = new Meli(static::APP_ID, static::SECRET_KEY);

        $filial = Filial::find()->andWhere(['=','id',72])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;

	$x = 0;
	$y = 0;
	$data_inicial 	= "2010-01-01";
	$data_final	= date("Y-m-d");

	$pedidos_mercado_livre = array();

        $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from='.$data_inicial.'T00:00:00.000-00:00&order.date_created.to='.$data_final.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
        while (ArrayHelper::getValue($response_order, 'body.results') != null){
                break;
		foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
			echo "\n".$y++." - ".$venda->id." - ".$venda->date_created." - ";
			
			$response_order = $meli->get('https://api.mercadolibre.com/shipments/'.$venda->shipping->id.'/invoice_data?siteId=MLB&access_token=' . $meliAccessToken);
			if($response_order["httpCode"] < 300){
				echo print_r($response_order["body"]->fiscal_key);
				$pedidos_mercado_livre[] = [$venda->id, $response_order["body"]->fiscal_key];
			}
			else{
				echo "Envio nao encontrado";
			}
                }
                $x += 50;
		$response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from='.$data_inicial.'T00:00:00.000-00:00&order.date_created.to='.$data_final.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
        }

        //TESTE

        //CONTA RECEBER CONTA PRINCIPAL
        $body = [
            "call" => "ListarContasReceber",
            "app_key" => $APP_KEY_OMIE_SP,
            "app_secret" => $APP_SECRET_OMIE_SP,
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 100,
                "apenas_importado_api" => "N",
                //"filtrar_por_status" => "ATRASADOssss" //'CANCELADO', 'PAGO', 'LIQUIDADO', 'EMABERTO', 'PAGTO_PARCIAL', 'VENCEHOJE', 'AVENCER', 'ATRASADO'
		"filtrar_por_status" => "ATRASADO" //'CANCELADO', 'PAGO', 'LIQUIDADO', 'EMABERTO', 'PAGTO_PARCIAL', 'VENCEHOJE', 'AVENCER', 'ATRASADO'
            ]
        ];
        $response_conta_receber = $omie->consulta("/api/v1/financas/contareceber/?JSON=",$body);
        //print_r($response_conta_receber);die;
        
        $contas_receber = [];
        
        $total_de_paginas = ArrayHelper::getValue($response_conta_receber, 'body.total_de_paginas');
        for($x = 1; $x <= $total_de_paginas; $x++){ //$x = 210
            //break;
            echo "\nP??gina: ".$x;
            $body = [
                "call" => "ListarContasReceber",
                "app_key" => $APP_KEY_OMIE_SP,
                "app_secret" => $APP_SECRET_OMIE_SP,
                "param" => [
                    "pagina" => $x,
                    "registros_por_pagina" => 100,
                    "apenas_importado_api" => "N",
		    "filtrar_por_status" => "ATRASADO"
                    
                ]
            ];
            $response_conta_receber = $omie->consulta("/api/v1/financas/contareceber/?JSON=",$body);
            
            foreach(ArrayHelper::getValue($response_conta_receber, 'body.conta_receber_cadastro') as $k => $conta_receber){

		//if(ArrayHelper::getValue($conta_receber, 'numero_documento_fiscal') == "00022451"){
                //	print_r($conta_receber); die;
		//}
                echo "\n".$k;
                echo " - ".ArrayHelper::getValue($conta_receber, 'numero_pedido');
                echo " - ".ArrayHelper::getValue($conta_receber, 'numero_documento_fiscal');
                echo " - ".ArrayHelper::getValue($conta_receber, 'status_titulo');
                echo " - ".ArrayHelper::getValue($conta_receber, 'data_vencimento');
		echo " - ".ArrayHelper::getValue($conta_receber, 'chave_nfe');

		fwrite($arquivo_log,"\n".ArrayHelper::getValue($conta_receber, 'numero_pedido').";".ArrayHelper::getValue($conta_receber, 'numero_documento_fiscal').";".ArrayHelper::getValue($conta_receber, 'status_titulo').";".ArrayHelper::getValue($conta_receber, 'data_vencimento').";".ArrayHelper::getValue($conta_receber, 'id_conta_corrente'));

                continue;

                if(ArrayHelper::getValue($conta_receber, 'status_titulo') == "RECEBIDO" || ArrayHelper::getValue($conta_receber, 'status_titulo') == "CANCELADO"){
                    echo " - pular";
                    continue;
                }

                $contas_receber = $conta_receber;

                //REQUISI????O COMPRA OMIE PRINCIPAL
                $body = [
                    "call" => "ConsultarPedido",
                    "app_key" => $APP_KEY_OMIE_SP,
                    "app_secret" => $APP_SECRET_OMIE_SP,
                    "param" => [
                        //"codigo_pedido"   => 1018996919,
                        "numero_pedido"     => ArrayHelper::getValue($conta_receber, 'numero_pedido')
                    ]
                ];
                $response_pedido = $meli->consulta("/api/v1/produtos/pedido/?JSON=",$body);

                if(ArrayHelper::getValue($response_pedido, 'httpCode') < 300){
                    if(array_key_exists("codigo_pedido_integracao", ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto.cabecalho'))){
                        $pedido_mrecado_livre = PedidoMercadoLivre::find()->andWhere(['=','pedido_meli_id',ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto.cabecalho.codigo_pedido_integracao')])->one();

                        if($pedido_mrecado_livre){

			    fwrite($arquivo_log,";".$pedido_mrecado_livre->id);

                            echo "\n\nPedido encontrado no Pe??a";

            			    if(ArrayHelper::getValue($conta_receber, 'valor_documento') != $pedido_mrecado_livre->total_amount){

                				echo " - Valor pedido e nota diferentes";

                				$body = [
                                	                "call" => "AlterarContaReceber",
                                	                "app_key" => $APP_KEY_OMIE_SP,
                                	                "app_secret" => $APP_SECRET_OMIE_SP,
                                	                "param" => [
                        	                           "codigo_lancamento_omie"     => ArrayHelper::getValue($conta_receber, 'codigo_lancamento_omie'),
                	                                    "observacao"            => "Valores do pedido e nota diferentes. Pedido: ".$pedido_mrecado_livre->total_amount.", Nota: ".ArrayHelper::getValue($conta_receber, 'valor_documento'),
                	                                ]
                	                        ];

    	                        $response_recebimento = $meli->consulta("/api/v1/financas/contareceber/?JSON=",$body);

    	                        print_r($response_recebimento);

						fwrite($arquivo_log,";Valores do pedido e nota diferentes. Pedido: ".$pedido_mrecado_livre->total_amount.", Nota: ".ArrayHelper::getValue($conta_receber, 'valor_documento'));

                				continue;
            			    }

                            $tarifa_envio = $pedido_mrecado_livre->shipping_option_cost;

                            $pedido_mercado_livre_produtos = PedidoMercadoLivreProduto::find()  ->select(['produto_filial_id', 'sale_fee', 'quantity'])
                                                                                                ->andWhere(['=','pedido_mercado_livre_id', $pedido_mrecado_livre->id])
                                                                                                ->distinct()
                                                                                                ->all();
                                      
                            $tarifas_produtos = 0;
                                                                                                
                            foreach($pedido_mercado_livre_produtos as $pedido_mercado_livre_produto){
                                $tarifas_produtos = $pedido_mercado_livre_produto->sale_fee * $pedido_mercado_livre_produto->quantity;
                            }
                            
                            echo "\n\nTarifa Envio: ".$tarifa_envio;
                            echo "\nTarifa Produto: ".$tarifas_produtos;
                            echo "\nValor:".ArrayHelper::getValue($conta_receber, 'valor_documento');
                            //die;
                            
                            $valor = ArrayHelper::getValue($conta_receber, 'valor_documento')+$tarifa_envio-$tarifas_produtos;
                                                        
                            //RECEBIMENTO DA CONTA A RECEBER
                            //RECEBIMENTO AUTOM??TICO
                            $body = [
                                "call" => "LancarRecebimento",
                                "app_key" => $APP_KEY_OMIE_SP,
                                "app_secret" => $APP_SECRET_OMIE_SP,
                                "param" => [
                                    "codigo_lancamento"     => ArrayHelper::getValue($conta_receber, 'codigo_lancamento_omie'),
                                    "valor"                 => $valor,//ArrayHelper::getValue($conta_receber, 'valor_documento'),//+$tarifa_envio,
                                    "desconto"              => $tarifas_produtos,
                                    "juros"                 => $tarifa_envio,
                                    "data"                  => date("d/m/Y"),
                                    "observacao"            => "(Recebido pela API) Valor: ".ArrayHelper::getValue($conta_receber, 'valor_documento')." | Desconto: ".$tarifas_produtos." | Juros: ".$tarifa_envio,
                                    "codigo_conta_corrente" => 502875713
                                ]
                            ];
                            
                            $response_recebimento = $meli->consulta("/api/v1/financas/contareceber/?JSON=",$body);
                            
                            print_r($response_recebimento);

			    fwrite($arquivo_log,";(Recebido pela API) Valor: ".ArrayHelper::getValue($conta_receber, 'valor_documento')." | Desconto: ".$tarifas_produtos." | Juros: ".$tarifa_envio);
                            
                            //RECEBIMENTO AUTOM??TICO
                            
                            //ALTERAR OBSERVA????O DA CONTA A RECEBER
                            $body = [
                                 "call" => "AlterarContaReceber",
                                 "app_key" => $APP_KEY_OMIE_SP,
                                 "app_secret" => $APP_SECRET_OMIE_SP,
                                 "param" => [
                                    "codigo_lancamento_omie"     => ArrayHelper::getValue($conta_receber, 'codigo_lancamento_omie'),
                                     "observacao"            => "(Recebido pela API) Valor: ".ArrayHelper::getValue($conta_receber, 'valor_documento')." | Desconto: ".$tarifas_produtos." | Juros: ".$tarifa_envio,
                                 ]
                             ];
                            
                             //$response_recebimento = $meli->consulta("/api/v1/financas/contareceber/?JSON=",$body);
                             //die;   
                        }
                        else{
                            echo " - Pedido n??o encontrado no Pe??a";
			    fwrite($arquivo_log,";;Pedido nao encontrado no Peca");
                        }
                    }
                    else{
                        echo " - Sem Codigo Pedido Integra????o";
			fwrite($arquivo_log,";;Sem Codigo Pedido Integracao");
                    }
                }
                else{
                    echo " - Conta a receber n??o encontrada";
		    fwrite($arquivo_log,";;;;;Conta a receber nao encontrada");
                }
            }
        }

	fclose($arquivo_log);

        echo "\n\nFim da rotina de aplica????o do desconto";                
    }
}

