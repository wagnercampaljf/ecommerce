<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;

class SincronizarEstoqueAction extends Action
{
    public function run($global_id)
    {
        
        //Função deve rodar de segunda a sexta
        
        $arquivo_log = fopen("/var/tmp/log_omie_remessa_".date("Y-m-d_H-i-s").".csv", "a");
        
        $dia_semana = date("l");
        
        if($dia_semana == "Sunday" || $dia_semana == "Saturday"){
            fwrite($arquivo_log,"Final de Semana (".$dia_semana.")");
            fclose($arquivo_log);
            die;
        }
        else{
            fwrite($arquivo_log,"Dia de Semana (".$dia_semana.") \n\n\ncodigo_produto;csosn;cfop;ean;ncm;quantidade;valor_unitario");
        }
      
        
        echo "Sincronizar estoque omie...\n\n";
        $meli = new Omie(static::APP_ID, static::SECRET_KEY);
        
        $APP_KEY_OMIE_SP              = '468080198586';
        $APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';
        
        //TESTE

        /*$body = [
            "call" => "ConsultarPedido",
            "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
            "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
            "param" => [
                //"codigo_pedido"       => 1022514564
		"codigo_pedido"       => 1022512631
		//"nPagina"       => 1,
                //"nRegistrosPorPagina" => 50,
                //"dDataPosicao"  => "",
                //"cExibeTodos"   => "S",
            ]
        ];
        echo "\n\n";
        $response_pedido = $meli->consulta("/api/v1/produtos/pedido/?JSON=",$body);
        print_r($response_pedido);

	if(!array_key_exists("det" , ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto'))){
                echo " - SEM PRODUTOS";
        }
	else{
		echo " - COM PRODUTOS";
	}

	die;*/

        //TESTE
        
                
        //NOTA FISCAL OMIE PRINCIPAL
        $body = [
            "call" => "ListarNF",
            "app_key" => $APP_KEY_OMIE_SP,
            "app_secret" => $APP_SECRET_OMIE_SP,
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 100,
                "apenas_importado_api" => "N",
                "ordenar_por" => "CODIGO",
                "tpNF" => 0,
            ]
        ];
        $response_nota_fiacal_compra = $meli->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
        //print_r($response_nota_fiacal_compra);die;
        
        $produtos_dados = [];
        
        $total_de_paginas = ArrayHelper::getValue($response_nota_fiacal_compra, 'body.total_de_paginas');
        for($x = 1; $x <= $total_de_paginas; $x++){
            //break;
            echo "\nPágina: ".$x;
            $body = [
                "call" => "ListarNF",
                "app_key" => $APP_KEY_OMIE_SP,
                "app_secret" => $APP_SECRET_OMIE_SP,
                "param" => [
                    "pagina" => $x,
                    "registros_por_pagina" => 100,
                    "apenas_importado_api" => "N",
                    "ordenar_por" => "CODIGO",
                    "tpNF" => 0,
                ]
            ];
            $response_nota_fiacal_compra = $meli->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
            
            foreach(ArrayHelper::getValue($response_nota_fiacal_compra, 'body.nfCadastro') as $k => $nota_fiscal){
                //print_r($nota_fiscal);
                echo " - ".$k;
                
                foreach(ArrayHelper::getValue($nota_fiscal, 'det') as $y => $produto){
                    //print_r($produto);
                    echo "\n".$y." - ".ArrayHelper::getValue($produto, 'prod.cProd'); //die;
                    
                    if(ArrayHelper::getValue($produto, 'prod.cProd') == "PA38272"){
                        echo "=============>>".$x."<<=============";   
                        //print_r($nota_fiscal);die;
                        //print_r($response_nota_fiacal_compra); die;
                        //print_r($produto);die;
                        //die;
                    }
                    
                    $produtos_dados[ArrayHelper::getValue($produto, 'prod.cProd')] = $produto;
                    //print_r($produtos_dados); die;
                }
               
            }
            
            //break;
            //print_r($produtos_dados); die;
        }
        //print_r($produtos_dados); die;
        
        //PEDIDOS VENDA OMIE NOVO'
        $body = [
            "call" => "ListarPedidos",
            //"app_key" => static::APP_KEY_OMIE,
            //"app_secret" => static::APP_SECRET_OMIE,
            "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
            "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 100,
                "apenas_importado_api" => "N"
            ]
        ];
        $response_omie = $meli->consulta_pedido("api/v1/geral/pedido/?JSON=",$body);
        //print_r($response_omie);die;
        
        $total_de_paginas_pedidos = ArrayHelper::getValue($response_omie, 'body.total_de_paginas');
        
        $produtos_remessa               = "";
        $quantidade_produtos_remessa    = 0;
        $quantidade_remessas_criadas    = 1;
        
        
        $produtos_ja_adicionados = array();
        
        for($i=1;$i<=$total_de_paginas_pedidos;$i++){
            $body = [
                "call" => "ListarPedidos",
                //"app_key" => static::APP_KEY_OMIE,
                //"app_secret" => static::APP_SECRET_OMIE,
                "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                "param" => [
                    "pagina" => $i,
                    "registros_por_pagina" => 100,
                    "apenas_importado_api" => "N"
                ]
            ];
            $response_omie = $meli->consulta_pedido("api/v1/geral/pedido/?JSON=",$body);
            
            $pedidos_omie_novo = ArrayHelper::getValue($response_omie, 'body.pedido_venda_produto');
            foreach($pedidos_omie_novo as $pedido_omie_novo){
                //print_r(ArrayHelper::getValue($pedido_omie_novo, 'cabecalho'));
                echo "\n\nPedido: ".ArrayHelper::getValue($pedido_omie_novo, 'cabecalho.codigo_pedido');

		//if(ArrayHelper::getValue($pedido_omie_novo, 'cabecalho.codigo_pedido') != "1022514564"){continue;}

                $body = [
                    "call" => "ConsultarPedido",
                    "app_key" => '1017311982687',
                    "app_secret" => '78ba33370fac6178da52d42240591291',
                    "param" => [
                        "codigo_pedido"       => ArrayHelper::getValue($pedido_omie_novo, 'cabecalho.codigo_pedido'),
                    ]
                ];
                $response_pedido = $meli->consulta("/api/v1/produtos/pedido/?JSON=",$body);
                //print_r($response_pedido);

		if(!array_key_exists("det" , ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto'))){
			echo " - SEM PRODUTOS";
			continue;
		}

                foreach(ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto.det') as $produto){
                    //print_r($produto); die;
                    echo "\nProduto: ".ArrayHelper::getValue($produto, 'produto.codigo');
                    
                    //if(ArrayHelper::getValue($produto, 'produto.codigo') != "PA28833"){continue;}
                    
                    $body = [
                        "call" => "MovimentoEstoque",
                        //"app_key" => static::APP_KEY_OMIE,
                        //"app_secret" => static::APP_SECRET_OMIE,
                        "app_key" => '1017311982687',
                        "app_secret" => '78ba33370fac6178da52d42240591291',
                        "param" => [
                            //"nPagina"       => 1,
                            "cod_int"       => ArrayHelper::getValue($produto, 'produto.codigo'),
                            "datainicial"   => "01/01/2010",
                            "dataFinal"     => "31/12/2020",
                        ]
                    ];
                    $response_omie = $meli->consulta("/api/v1/estoque/consulta/?JSON=",$body);
                    //print_r($response_omie);
                    
                    $movimentacoes = ArrayHelper::getValue($response_omie, 'body.movProduto');
                    
                    if($movimentacoes){
                        foreach(ArrayHelper::getValue($movimentacoes[count($movimentacoes)-1], 'movPeriodo') as $movimentacao){
                            $pos = strpos(ArrayHelper::getValue($movimentacao, 'tipo'), "Atual");
                            if (!($pos === false)) {
                                
                                $salto_atual = ArrayHelper::getValue($movimentacao, 'qtde');
                                echo "\nSaldo atual: ".$salto_atual;
                                
                                //if(ArrayHelper::getValue($produto, 'produto.codigo') == "PA231813"){ die; }
                                
                                if($salto_atual < 0){
                                    if(array_key_exists(ArrayHelper::getValue($produto, 'produto.codigo'), $produtos_dados)){
                                        //print_r($produtos_dados[ArrayHelper::getValue($produto, 'produto.codigo')]);
                                        
                                        $quantidade             = (-1)*$salto_atual;
                                        $valor_produto          = ArrayHelper::getValue($produtos_dados[ArrayHelper::getValue($produto, 'produto.codigo')], 'prod.vProd');
                                        $valor_total            = ArrayHelper::getValue($produtos_dados[ArrayHelper::getValue($produto, 'produto.codigo')], 'prod.vTotItem');
                                        $valor_st               = round($valor_total,2) - round($valor_produto,2);
                                        $valor_unitario         = $valor_produto / ArrayHelper::getValue($produtos_dados[ArrayHelper::getValue($produto, 'produto.codigo')], 'prod.qCom');
                                        $valor_unitario_nota    = ArrayHelper::getValue($produtos_dados[ArrayHelper::getValue($produto, 'produto.codigo')], 'prod.nCMCUnitario');
                                        $cfop                   = "5.409";
                                        $csosn                  = "500";
                                        
                                        switch (ArrayHelper::getValue($produtos_dados[ArrayHelper::getValue($produto, 'produto.codigo')], 'prod.CFOP')){
                                            /*case "5.401":   $cfop = "5.409";
                                                            break;
                                            case "5.402":   $cfop = "5.409";
                                                            break;
                                            case "5.403":   $cfop = "5.409";
                                                            break;
                                            case "5.405":   $cfop = "5.409";
                                                            break;
                                            
                                            case "6.401":   $cfop = "5.409";
                                                            break;
                                            case "6.402":   $cfop = "5.409";
                                                            break;
                                            case "6.403":   $cfop = "5.409";
                                                            break;
                                            case "6.405":   $cfop = "5.409";
                                                            break;
                                                            
                                            case "5.101":   $cfop = "5.152";
                                                            break;
                                            case "5.102":   $cfop = "5.152";
                                                            break;
                                            case "5.103":   $cfop = "5.152";
                                                            break;
                                            case "5.104":   $cfop = "5.152";
                                                            break;
                                            case "5.105":   $cfop = "5.152";
                                                            break;
                                            case "5.106":   $cfop = "5.152";
                                                            break; 
                                            
                                            case "6.101":   $cfop = "5.152";
                                                            break;
                                            case "6.102":   $cfop = "5.152";
                                                            break;
                                            case "6.103":   $cfop = "5.152";
                                                            break;
                                            case "6.104":   $cfop = "5.152";
                                                            break;
                                            case "6.105":   $cfop = "5.152";
                                                            break;
                                            case "6.106":   $cfop = "5.152";
                                                            break;*/
                                                            
                                            case "1.401":   $cfop = "5.409";
                                                            break;
                                            case "1.402":   $cfop = "5.409";
                                                            break;
                                            case "1.403":   $cfop = "5.409";
                                                            break;
                                            case "1.405":   $cfop = "5.409";
                                                            break;
                                            
                                            case "2.401":   $cfop = "5.409";
                                                            break;
                                            case "2.402":   $cfop = "5.409";
                                                            break;
                                            case "2.403":   $cfop = "5.409";
                                                            break;
                                            case "2.405":   $cfop = "5.409";
                                                            break;
                                            
                                            case "1.101":   $cfop = "5.152";
                                                            break;
                                            case "1.102":   $cfop = "5.152";
                                                            break;
                                            case "1.103":   $cfop = "5.152";
                                                            break;
                                            case "1.104":   $cfop = "5.152";
                                                            break;
                                            case "1.105":   $cfop = "5.152";
                                                            break;
                                            case "1.106":   $cfop = "5.152";
                                                            break;
                                            
                                            case "2.101":   $cfop = "5.152";
                                                            break;
                                            case "2.102":   $cfop = "5.152";
                                                            break;
                                            case "2.103":   $cfop = "5.152";
                                                            break;
                                            case "2.104":   $cfop = "5.152";
                                                            break;
                                            case "2.105":   $cfop = "5.152";
                                                            break;
                                            case "2.106":   $cfop = "5.152";
                                                            break;
                                            
                                            default: $cfop = "5.409";
                                        }
                                        
                                        if($cfop == "5.152"){
                                            $csosn  = "900";
                                        }
                                        
                                        $valor_total_final = $quantidade * $valor_unitario;
                                        
                                        
                                        $codigo = ArrayHelper::getValue($produto, 'produto.codigo');
                                        $codigo_produto = ArrayHelper::getValue($produtos_dados[ArrayHelper::getValue($produto, 'produto.codigo')], 'nfProdInt.nCodProd');
                                        $ean = ArrayHelper::getValue($produto, 'produto.ean');
                                        $ncm = ArrayHelper::getValue($produto, 'produto.ncm');
                                        
                                        
                                        if(array_search($codigo, $produtos_ja_adicionados)){
                                            continue;
                                        }
                                        else{
                                            $produtos_ja_adicionados[] = $codigo;
                                        }
                                        
                                        $produtos_remessa .= '{"COFINS":{"cSitTribCOFINS":"01","cTpCalcCOFINS":"B","nAliqCOFINS":0,"nBCCOFINS":0,"nQtdUTCOFINS":0,"nVaCOFINSSUT":0,
"nValCOFINS":0},"ICMS":{"cModBC":3,"cOrigem":0,"cSitTrib":"","nAliq":0,
"nBC":0,"nRedBC":0,"nValor":0},"ICMS_ST":{"cModBCST":"4","nAliqOP":0,"nAliqST":0,"nBCST":0,"nMargVrAd":0,"nRedBCST":0,"nValorST":0},
"PIS":{"cSitTribPIS":"01","cTpCalcPIS":"B","nAliqPIS":0,"nBCPIS":0,"nQtdUTPIS":0,"nValPIS":0,"nValPISUT":0},
"ICMS_SN":{"cOrigem":0,"cSitTribSN":"'.$csosn.'","nAliqSN":0,"nBCSN":0,
"nCredSN":0,"nValorSN":0},"cCFOP":"'.$cfop.'","cCest":"01.075.00","cCodItInt":"'.$codigo.'",
"cEAN":"'.$ean.'","cNCM":"'.$ncm.'","infAdicItem":{"cInfItemNF":"","cNaoMovEstoque":"N",
"cPedCompra":"","nItemPedCompra":0,"nPesoBruto": 2,"nPesoLiq":2},"nCodProd":"'.$codigo_produto.'",
"nDesconto":0,"nQtde":'.$quantidade.',"nValUnit":'.$valor_unitario.',"rastreabilidade":[]},';
                                        
                                        fwrite($arquivo_log,"\n".$codigo_produto.";".$csosn.";".$cfop.";".$ean.";".$ncm.";".$quantidade.";".$valor_unitario);

                                    }
                                }
                                
                                break;
                            }
                        }
                    }
                }
            }
        }
        
        $produtos_remessa = '"produtos":['.$produtos_remessa.']';
        $produtos_remessa = str_replace("[]},]", "[]}]",$produtos_remessa);
        
        $body = '{"call": "IncluirRemessa","app_key": "468080198586","app_secret": "7b3fb2b3bae35eca3b051b825b6d9f43",
"param":[{"cabec":{"cCodIntRem": "'.date("Y-m-d")."_".$quantidade_remessas_criadas.'","dPrevisao": "'.date("d/m/Y").'","nCodCli": "2641483458","nCodRem": "","nCodVend": 0},
"email":{"cEmail": "felipe@optsolucoes.com"},"frete":{"cEspVol": "","cMarVol": "","cNumVol": "",
"cPlaca": "","cTpFrete": 9,"cUF": "","nCodTransp": 0,"nPesoBruto": 2,"nPesoLiq": 2,
"nQtdVol": 0,"nValFrete": 0,"nValOutras": 0,"nValSeguro": 0},"infAdic":{"cCodCateg": "1.01.03",
"cConsFinal": "N","cContato": "","cDadosAdic": "",
"cNumCtr": "","cPedido": "","nCodProj": 0,"nfRelacionada": [{"InscrEstPR": "","cChaveRef": "",
"cSeriePR": "","cSerieRef": "","cUfPR": "","cUfRef": "","cnpjEmitRef": "","cnpjPR": "",
"dtEmissaoPR": "","dtEmissaoRef": "","indPresenca": "","nCOORef": "","nECFRef": "",
"nNFRef": "","nNfPR": ""}]},"obs":{"cObs": ""},'.$produtos_remessa.'}]}';
        
        $body = str_replace("\n","",$body);
        
        $url = "https://app.omie.com.br/api/v1/produtos/remessa/";
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $body );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        curl_close($ch);
        
        echo "\n\n".$result."\n\n";
        
        $reposta_array = json_decode($result);
        print_r($reposta_array);
        
        if(array_key_exists("nCodRem",$reposta_array)){
            fwrite($arquivo_log,"\n\n\ncodigo_remessa;codigo_remessa_integracao;codigo_status;descricao_status;numero_remessa");
            fwrite($arquivo_log,"\n".$reposta_array->nCodRem.";".$reposta_array->cCodIntRem.";".$reposta_array->cCodStatus.";".$reposta_array->cDesStatus.";".$reposta_array->cNumeroRemessa);
        }
        else{
            fwrite($arquivo_log,"\n\n\nRemessa não criada!");
        }
        
        echo "\n\nFinalizadas as notas de remessa!!!";
        
        fclose($file);
                
    }
}

