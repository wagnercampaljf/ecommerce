<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreProduto;
use common\models\Filial;
use Livepixel\MercadoLivre\Meli;
use yii\helpers\Json;

class ReceberContasAReceberContaDuplicadaAction extends Action
{
    public function run()
    {
        
        $arquivo_log        = fopen("/var/tmp/receber_pedidos_ml_omie/log_contas_receber_atrasado_CD_".date("Y-m-d_H-i-s").".csv", "a");
        
        fwrite($arquivo_log,"Conta principal\n\n\numero_pedido;numero_documento_fiscal;status_titulo;data_vencimento;id_conta_corrente;chave_nfe;pedido_mercado_livre_id;status");
        
        echo "Receber contas omie...\n\n";
        $omie = new Omie(static::APP_ID, static::SECRET_KEY);
        
        $APP_KEY_OMIE_SP              = '468080198586';
        $APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';
        
        //TESTE
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        
        $filial = Filial::find()->andWhere(['=','id',98])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;
        
        $x = 0;
        $y = 0;
        $data_inicial 	= "2010-01-01";//"2020-10-01";
        $data_final	    = date("Y-m-d");
        
        $pedidos_mercado_livre = array();
        
        //Baseado no ML
        /*$response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from='.$data_inicial.'T00:00:00.000-00:00&order.date_created.to='.$data_final.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
         while (ArrayHelper::getValue($response_order, 'body.results') != null){
         //break;
         foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
         echo "\n".$y++." - ".$venda->id." - ".$venda->date_created." - ";
         
         $response_order = $meli->get('https://api.mercadolibre.com/shipments/'.$venda->shipping->id.'/invoice_data?siteId=MLB&access_token=' . $meliAccessToken);
         if($response_order["httpCode"] < 300){
         echo print_r($response_order["body"]->fiscal_key);
         $pedidos_mercado_livre[$response_order["body"]->fiscal_key] = $venda->id;
         }
         else{
         echo "Envio nao encontrado";
         }
         }
         
         $x += 50;
         $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from='.$data_inicial.'T00:00:00.000-00:00&order.date_created.to='.$data_final.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
         }*/
        
        //Baseado no backend
        $arquivo_pedidos_nome = "/var/tmp/receber_pedidos_ml_omie/pedidos_2021-05-17_CD.csv";
        
        if (file_exists($arquivo_pedidos_nome)){
            $arquivo_pedidos    = fopen($arquivo_pedidos_nome, "r");
            while (($line = fgetcsv($arquivo_pedidos,null,';')) !== false)
            {
                //echo "\n".$line[0];
                $pedidos_mercado_livre[$line[1]] = $line[0];
            }
            fclose($arquivo_pedidos);
        }
        else{
            $pedidos_mercado_livre_tabela = PedidoMercadoLivre::find()  //->andWhere([">=", "date_created", "2010-02-01 00:00:00"])
                                                                        //->andWhere(["<=", "date_created", "2021-05-31 23:59:59"])
                                                                        ->andWhere(["<>", "user_id", "193724256"])
                                                                        ->orderBy(["id" => SORT_ASC])
                                                                        ->all();
            
            $arquivo_pedidos    = fopen($arquivo_pedidos_nome, "a");
            
            foreach ($pedidos_mercado_livre_tabela as $pedido_mercado_livre_tabela){
                //break;
                echo "\n".$y++." - ".$pedido_mercado_livre_tabela->pedido_meli_id." - ".$pedido_mercado_livre_tabela->date_created." - ";
                
                
                
                $response_order = $meli->get('https://api.mercadolibre.com/shipments/'.$pedido_mercado_livre_tabela->shipping_id.'/invoice_data?siteId=MLB&access_token=' . $meliAccessToken);
                if($response_order["httpCode"] < 300){
                    echo print_r($response_order["body"]->fiscal_key);
                    $pedidos_mercado_livre[$response_order["body"]->fiscal_key] = $pedido_mercado_livre_tabela->pedido_meli_id;
                    fwrite($arquivo_pedidos, $pedido_mercado_livre_tabela->pedido_meli_id.';"'.$response_order["body"]->fiscal_key.'"'."\n");
                }
                else{
                    echo "Envio nao encontrado";
                }
            }
            
            fclose($arquivo_pedidos);
        }
        
        //print_r($pedidos_mercado_livre);
        //TESTE
        
        //CONTA RECEBER CONTA PRINCIPAL
        $body = [
            "call" => "ListarContasReceber",
            "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
            "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 100,
                "apenas_importado_api" => "N",
                "filtrar_por_status" => "ATRASADO", //'CANCELADO', 'PAGO', 'LIQUIDADO', 'EMABERTO', 'PAGTO_PARCIAL', 'VENCEHOJE', 'AVENCER', 'ATRASADO'
                "filtrar_por_data_de" => "01/01/2010",
                "filtrar_por_data_ate" => "31/05/2021",
            ]
        ];
        $response_conta_receber = $omie->consulta("/api/v1/financas/contareceber/?JSON=",$body);
        //print_r($response_conta_receber);die;
        
        $contas_receber = [];
        
        $y = 0;
        
        $total_de_paginas = ArrayHelper::getValue($response_conta_receber, 'body.total_de_paginas');
        for($x = 1; $x <= $total_de_paginas; $x++){ //$x = 210
            //break;
            echo "\nPágina: ".$x;
            $body = [
                "call" => "ListarContasReceber",
                "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                "param" => [
                    "pagina" => $x,
                    "registros_por_pagina" => 100,
                    "apenas_importado_api" => "N",
                    "filtrar_por_status" => "ATRASADO",
                    "filtrar_por_data_de" => "01/01/2010",
                    "filtrar_por_data_ate" => "31/05/2021",
                ]
            ];
            $response_conta_receber = $omie->consulta("/api/v1/financas/contareceber/?JSON=",$body);
            
            foreach(ArrayHelper::getValue($response_conta_receber, 'body.conta_receber_cadastro') as $k => $conta_receber){
                
                //if(ArrayHelper::getValue($conta_receber, 'numero_documento_fiscal') == "00022451"){
                //print_r($conta_receber); die;
                //}
                
                $body = [
                    "call" => "ConsultarContaCorrente",
                    "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                    "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                    "param" => [
                        "nCodCC" => $conta_receber["id_conta_corrente"]
                    ]
                ];
                $response_conta_corrente = $omie->consulta("/api/v1/geral/contacorrente/?JSON=",$body);
                //print_r($response_conta_corrente); die;
                
                echo "\n".$k;
                echo " - ".ArrayHelper::getValue($conta_receber, 'numero_pedido');
                echo " - ".ArrayHelper::getValue($conta_receber, 'numero_documento_fiscal');
                echo " - ".ArrayHelper::getValue($conta_receber, 'status_titulo');
                echo " - ".ArrayHelper::getValue($conta_receber, 'data_vencimento');
                echo " - ".ArrayHelper::getValue($conta_receber, 'chave_nfe');
                echo " - ".ArrayHelper::getValue($conta_receber, 'numero_documento_fiscal');
                
                fwrite($arquivo_log,"\n".ArrayHelper::getValue($conta_receber, 'numero_pedido').";".ArrayHelper::getValue($conta_receber, 'numero_documento_fiscal').";".ArrayHelper::getValue($conta_receber, 'status_titulo').";".ArrayHelper::getValue($conta_receber, 'data_vencimento').";".ArrayHelper::getValue($conta_receber, 'id_conta_corrente').";".ArrayHelper::getValue($conta_receber, 'chave_nfe').";".((array_key_exists("descricao", $response_conta_corrente["body"])) ? $response_conta_corrente["body"]["descricao"] : ""));
                
                //fwrite($arquivo_log,"\n".ArrayHelper::getValue($conta_receber, 'numero_pedido').";".ArrayHelper::getValue($conta_receber, 'numero_documento_fiscal').";".ArrayHelper::getValue($conta_receber, 'status_titulo').";".ArrayHelper::getValue($conta_receber, 'data_vencimento').";".ArrayHelper::getValue($conta_receber, 'id_conta_corrente'));
                
                if(ArrayHelper::getValue($conta_receber, 'chave_nfe') != ""){
                    if(array_key_exists(ArrayHelper::getValue($conta_receber, 'chave_nfe'), $pedidos_mercado_livre)){
                        echo "\nPedido encontrado pela chave: ".$pedidos_mercado_livre[ArrayHelper::getValue($conta_receber, 'chave_nfe')];
                        
                        $venda_id = $pedidos_mercado_livre[ArrayHelper::getValue($conta_receber, 'chave_nfe')];
                        //$venda_id = "4117864762";
                        $url_teste = "/orders/".$venda_id."?access_token=".$meliAccessToken;
                        $response_order = $meli->get($url_teste);
                        //print_r($response_order); print_r($url_teste); //die;
                        $response_shipping = $meli->get('https://api.mercadolibre.com/shipments/'.$response_order["body"]->shipping->id.'?access_token=' . $meliAccessToken);
                        //print_r($response_shipping);
                        
                        if($response_order["httpCode"] == 200){
                            
                            fwrite($arquivo_log,";".$response_order["body"]->id);
                            
                            echo " - Pedido encontrado no ML";
                            
                            if(ArrayHelper::getValue($conta_receber, 'valor_documento') != $response_order["body"]->total_amount){
                                echo "\nValores do pedido e nota diferentes";
                                
                                $valor_envio                = $response_shipping["body"]->shipping_option->cost - $response_shipping["body"]->shipping_option->list_cost;
                                $valor_taxas_mercado_pago   = 0;
                                $valor_despesa              = 0;
                                
                                
                                foreach($response_order["body"]->payments as $pagamento){
                                    //print_r($pagamento); print_r($meliAccessToken); die;
                                    $response_payment = $meli->get("/payments/".$pagamento->id."?access_token=".$meliAccessToken);
                                    //print_r($response_payment);
                                    
                                    /////////////////////////////////////////////////////////////////////////////////////////
                                    $pagamento_id = $pagamento->id;
                                    //$pagamento_id = 12063478334;
                                    
                                    $url = "https://api.mercadopago.com/v1/payments/".$pagamento_id."?access_token=".$meliAccessToken;
                                    $ch = curl_init( $url );
                                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                                    $result = curl_exec($ch);
                                    curl_close($ch);
                                    $reposta_array = json_decode($result);
                                    //print_r($reposta_array);
                                    
                                    foreach($reposta_array->charges_details as $detalhe_mercado_pago){
                                        $valor_taxas_mercado_pago += $detalhe_mercado_pago->amounts->original;
                                    }
                                    
                                    $valor_despesa += $pagamento->transaction_amount;
                                    /////////////////////////////////////////////////////////////////////////////////////////
                                    
                                }
                                
                                $valor_total_mercado_pago = $valor_despesa + $valor_envio - $valor_taxas_mercado_pago;
                                
                                echo "\n\n";
                                echo "Valor despesa: ".$valor_despesa." Valor envio: ".$valor_envio." Valor taxas mercado pago:".$valor_taxas_mercado_pago." Valor total mercado pago:".$valor_total_mercado_pago;
                                echo "\n\n";
                                
                                $body = [
                                    "call" => "AlterarContaReceber",
                                    "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                                    "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                                    "param" => [
                                        "codigo_lancamento_omie"    => ArrayHelper::getValue($conta_receber, 'codigo_lancamento_omie'),
                                        "observacao"                => "Valores do pedido e nota diferentes. Pedido: ".$response_order["body"]->total_amount.", Nota: ".ArrayHelper::getValue($conta_receber, 'valor_documento'),
                                    ]
                                ];
                                $response_recebimento = $omie->consulta("/api/v1/financas/contareceber/?JSON=",$body);
                                //print_r($response_recebimento);
                                
                                fwrite($arquivo_log,";Valores do pedido e nota diferentes. Pedido: ".$response_order["body"]->total_amount.", Nota: ".ArrayHelper::getValue($conta_receber, 'valor_documento')." - Valor restante Mercado Pago ".$valor_total_mercado_pago.";".$response_order["body"]->total_amount.";".ArrayHelper::getValue($conta_receber, 'valor_documento'));
                            }
                            else{
                                echo "Recebido pela API";
                                
                                $tarifa_envio = $response_shipping["body"]->shipping_option->cost;
                                
                                $tarifas_produtos = 0;
                                
                                //print_r(ArrayHelper::getValue($response_order, 'body.order_items'));
                                foreach(ArrayHelper::getValue($response_order, 'body.order_items') as $k => $produto){
                                    $tarifas_produtos = $produto->sale_fee * $produto->quantity;
                                }
                                
                                echo "\n\nTarifa Envio: ".$tarifa_envio;
                                echo "\nTarifa Produto: ".$tarifas_produtos;
                                echo "\nValor:".ArrayHelper::getValue($conta_receber, 'valor_documento');
                                
                                $valor = ArrayHelper::getValue($conta_receber, 'valor_documento')+$tarifa_envio-$tarifas_produtos;
                                
                                //RECEBIMENTO AUTOMÁTICO
                                $body = [
                                    "call" => "LancarRecebimento",
                                    "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                                    "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                                    "param" => [
                                        "codigo_lancamento"     => ArrayHelper::getValue($conta_receber, 'codigo_lancamento_omie'),
                                        "valor"                 => $valor,
                                        "desconto"              => $tarifas_produtos,
                                        "juros"                 => $tarifa_envio,
                                        "data"                  => date("d/m/Y"),
                                        "observacao"            => "(Recebido pela API) Valor: ".ArrayHelper::getValue($conta_receber, 'valor_documento')." | Desconto: ".$tarifas_produtos." | Juros: ".$tarifa_envio,
                                        "codigo_conta_corrente" => $conta_receber["id_conta_corrente"]
                                    ]
                                ];
                                print_r($body);
                                
                                $response_recebimento = $omie->consulta("/api/v1/financas/contareceber/?JSON=",$body);
                                print_r($response_recebimento);
                                if($response_recebimento["httpCode"] < 300){
                                    fwrite($arquivo_log,";Recebido pela API");
                                }
                                else{
                                    fwrite($arquivo_log,";Não recebido pela API");
                                }
                                //if($y++ >= 2){ echo "\n\n3 Contas a receber recebidas"; die;}
                            }
                        }
                        else{
                            echo " - Pedido não encontrado no ML";
                            fwrite($arquivo_log,";;Pedido não encontrado no ML");
                        }
                    }
                    else{
                        
                        $body = [
                            "call" => "ConsultarPedido",
                            "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                            "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                            "param" => [
                                //"codigo_pedido"   => 1018996919,
                                "numero_pedido"     => ArrayHelper::getValue($conta_receber, 'numero_pedido')
                            ]
                        ];
                        $response_pedido = $omie->consulta("/api/v1/produtos/pedido/?JSON=",$body);
                        //print_r($response_pedido); die;
                        
                        if(array_key_exists("pedido_venda_produto",$response_pedido["body"])){
                            if(array_key_exists("cabecalho",$response_pedido["body"]["pedido_venda_produto"])){
                                if(array_key_exists("codigo_pedido_integracao",$response_pedido["body"]["pedido_venda_produto"]["cabecalho"])){
                                    echo "\n\nCódigo Integração: "; print_r($response_pedido["body"]["pedido_venda_produto"]["cabecalho"]["codigo_pedido_integracao"]); echo "\n\n";
                                    
                                    if($response_pedido["body"]["pedido_venda_produto"]["cabecalho"]["codigo_pedido_integracao"] <> ""){
                                        $response_order = $meli->get("/orders/".$response_pedido["body"]["pedido_venda_produto"]["cabecalho"]["codigo_pedido_integracao"]."?access_token=".$meliAccessToken);
                                        
                                        $response_shipping = $meli->get('https://api.mercadolibre.com/shipments/'.$response_order["body"]->shipping->id.'?access_token=' . $meliAccessToken);
                                        //print_r($response_shipping);
                                        
                                        if($response_order["httpCode"] == 200 && $response_shipping["httpCode"] == 200){
                                            
                                            fwrite($arquivo_log,";".$response_order["body"]->id);
                                            
                                            echo " - Pedido encontrado no ML";
                                            //continue;
                                            if(ArrayHelper::getValue($conta_receber, 'valor_documento') != $response_order["body"]->total_amount){
                                                echo "\nValores do pedido e nota diferentes";
                                                
                                                $valor_envio                = $response_shipping["body"]->shipping_option->cost - $response_shipping["body"]->shipping_option->list_cost;
                                                $valor_taxas_mercado_pago   = 0;
                                                $valor_despesa              = 0;
                                                
                                                
                                                foreach($response_order["body"]->payments as $pagamento){
                                                    //print_r($pagamento); print_r($meliAccessToken); die;
                                                    $response_payment = $meli->get("/payments/".$pagamento->id."?access_token=".$meliAccessToken);
                                                    //print_r($response_payment);
                                                    
                                                    /////////////////////////////////////////////////////////////////////////////////////////
                                                    $pagamento_id = $pagamento->id;
                                                    //$pagamento_id = 12063478334;
                                                    
                                                    $url = "https://api.mercadopago.com/v1/payments/".$pagamento_id."?access_token=".$meliAccessToken;
                                                    $ch = curl_init( $url );
                                                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                                                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                                                    $result = curl_exec($ch);
                                                    curl_close($ch);
                                                    $reposta_array = json_decode($result);
                                                    //print_r($reposta_array);
                                                    
                                                    foreach($reposta_array->charges_details as $detalhe_mercado_pago){
                                                        $valor_taxas_mercado_pago += $detalhe_mercado_pago->amounts->original;
                                                    }
                                                    
                                                    $valor_despesa += $pagamento->transaction_amount;
                                                    /////////////////////////////////////////////////////////////////////////////////////////
                                                    
                                                }
                                                
                                                $valor_total_mercado_pago = $valor_despesa + $valor_envio - $valor_taxas_mercado_pago;
                                                
                                                echo "\n\n";
                                                echo "Valor despesa: ".$valor_despesa." Valor envio: ".$valor_envio." Valor taxas mercado pago:".$valor_taxas_mercado_pago." Valor total mercado pago:".$valor_total_mercado_pago;
                                                echo "\n\n";
                                                
                                                $body = [
                                                    "call" => "AlterarContaReceber",
                                                    "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                                                    "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                                                    "param" => [
                                                        "codigo_lancamento_omie"    => ArrayHelper::getValue($conta_receber, 'codigo_lancamento_omie'),
                                                        "observacao"                => "Valores do pedido e nota diferentes. Pedido: ".$response_order["body"]->total_amount.", Nota: ".ArrayHelper::getValue($conta_receber, 'valor_documento'),
                                                    ]
                                                ];
                                                $response_recebimento = $omie->consulta("/api/v1/financas/contareceber/?JSON=",$body);
                                                //print_r($response_recebimento);
                                                
                                                fwrite($arquivo_log,";Valores do pedido e nota diferentes. Pedido: ".$response_order["body"]->total_amount.", Nota: ".ArrayHelper::getValue($conta_receber, 'valor_documento')." - Valor restante Mercado Pago ".$valor_total_mercado_pago.";".$response_order["body"]->total_amount.";".ArrayHelper::getValue($conta_receber, 'valor_documento'));
                                            }
                                            else{
                                                echo "Recebido pela API";
                                                
                                                $tarifa_envio = 0;//$response_shipping["body"]->shipping_option->cost;
                                                
                                                $tarifas_produtos = 0;
                                                
                                                //print_r(ArrayHelper::getValue($response_order, 'body.order_items'));
                                                foreach(ArrayHelper::getValue($response_order, 'body.order_items') as $k => $produto){
                                                    $tarifas_produtos = $produto->sale_fee * $produto->quantity;
                                                }
                                                
                                                echo "\n\nTarifa Envio: ".$tarifa_envio;
                                                echo "\nTarifa Produto: ".$tarifas_produtos;
                                                echo "\nValor:".ArrayHelper::getValue($conta_receber, 'valor_documento');
                                                
                                                $valor = ArrayHelper::getValue($conta_receber, 'valor_documento')+$tarifa_envio-$tarifas_produtos;
                                                
                                                //RECEBIMENTO AUTOMÁTICO
                                                $body = [
                                                    "call" => "LancarRecebimento",
                                                    "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                                                    "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                                                    "param" => [
                                                        "codigo_lancamento"     => ArrayHelper::getValue($conta_receber, 'codigo_lancamento_omie'),
                                                        "valor"                 => $valor,
                                                        "desconto"              => $tarifas_produtos,
                                                        "juros"                 => $tarifa_envio,
                                                        "data"                  => date("d/m/Y"),
                                                        "observacao"            => "(Recebido pela API) Valor: ".ArrayHelper::getValue($conta_receber, 'valor_documento')." | Desconto: ".$tarifas_produtos." | Juros: ".$tarifa_envio,
                                                        "codigo_conta_corrente" => $conta_receber["id_conta_corrente"]
                                                    ]
                                                ];
                                                print_r($body);
                                                
                                                $response_recebimento = $omie->consulta("/api/v1/financas/contareceber/?JSON=",$body);
                                                print_r($response_recebimento);
                                                if($response_recebimento["httpCode"] < 300){
                                                    fwrite($arquivo_log,";Recebido pela API");
                                                }
                                                else{
                                                    fwrite($arquivo_log,";Não recebido pela API");
                                                }
                                                //if($y++ >= 2){ echo "\n\n3 Contas a receber recebidas"; die;}
                                            }
                                        }
                                        else{
                                            echo " - Pedido não encontrado no ML";
                                            fwrite($arquivo_log,";;Pedido não encontrado no ML");
                                        }
                                    }
                                    else{
                                        echo " - Código integração vazio";
                                        fwrite($arquivo_log,";Código integração vazio");
                                    }
                                }
                            }
                        }
                        else{
                            echo " - Pedido não encontrado pela chave.";
                            fwrite($arquivo_log,";;Pedido não encontrado pela chave");
                        }
                    }
                }
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFim da rotina de aplicação do desconto";
    }
}

