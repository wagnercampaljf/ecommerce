<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;

class SincronizarEstoqueAutomaticoMG4Action extends Action
{
    public function run()
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
        $omie = new Omie(static::APP_ID, static::SECRET_KEY);
        
        $APP_KEY_OMIE_SP            = '468080198586';
        $APP_SECRET_OMIE_SP         = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_MG4     = '1758907907757';
        $APP_SECRET_OMIE_CONTA_MG4  = '0a69c9b49e5a188e5f43d5505f2752bc';

        //TESTE
        
            
        
        //TESTE
        
        
        //NOTA FISCAL OMIE CONTA DUPLICADA
        $data_filtro = date('d/m/Y', strtotime("-60 days",strtotime(date('Y-m-d'))));
                
        $produtos_dados = [];
                
        $body = [
            "call" => "ListarNF",
            "app_key" => $APP_KEY_OMIE_CONTA_MG4,
            "app_secret" => $APP_SECRET_OMIE_CONTA_MG4,
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 500,
                "apenas_importado_api" => "N",
                "ordenar_por" => "CODIGO",
                "dEmiInicial" => $data_filtro,
                //"tpNF" => 1, //Tipo 0 são as notas de entrada e a 1 são as notas de saída
            ]
        ];
        
        $response_nota_fiacal_compra = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
        //print_r($response_nota_fiacal_compra);die;
        
        //$produtos_dados = [];
        
        $total_de_paginas = ArrayHelper::getValue($response_nota_fiacal_compra, 'body.total_de_paginas');
        for($x = 1; $x <= $total_de_paginas; $x++){
            //break;
            echo "\nPágina: ".$x;
            $body = [
                "call" => "ListarNF",
                "app_key" => $APP_KEY_OMIE_CONTA_MG4,
                "app_secret" => $APP_SECRET_OMIE_CONTA_MG4,
                "param" => [
                    "pagina" => $x,
                    "registros_por_pagina" => 500,
                    "apenas_importado_api" => "N",
                    "ordenar_por" => "CODIGO",
                    "dEmiInicial" => $data_filtro,
                    //"tpNF" => 1, //Tipo 0 são as notas de entrada e a 1 são as notas de saída
                ]
            ];
            $response_nota_fiacal_compra = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
            
            foreach(ArrayHelper::getValue($response_nota_fiacal_compra, 'body.nfCadastro') as $k => $nota_fiscal){
                //echo "\n"; print_r($nota_fiscal); die;
                
                echo " - ".$k;
                
                if(!array_key_exists("det", $nota_fiscal)){
                    echo " - (FOR PRODUTOS NF k:".$k.")";
                    continue;
                }
                
                foreach(ArrayHelper::getValue($nota_fiscal, 'det') as $y => $produto){
                    //print_r($produto);
                    echo "\n".$y." - ".ArrayHelper::getValue($produto, 'prod.cProd'); //die;
                    
                    if(ArrayHelper::getValue($produto, 'prod.cProd') == "PA15160"){
                        echo "=============>>".$x."<<=============";
                        //print_r($nota_fiscal);die;
                        //print_r($response_nota_fiacal_compra); die;
                        //print_r($produto);die;
                        //die;
                    }
                    
                    //print_r($produto);
                    
                    $produto["prod"]["vProd"]   = $produto["prod"]["vProd"] / 2;
                    $produto["prod"]["vTotItem"]   = $produto["prod"]["vTotItem"] / 2;
                    $produto["prod"]["nCMCUnitario"]   = $produto["prod"]["nCMCUnitario"] / 2;
                    
                    //print_r($produto); die;
                    
                    $produtos_dados[ArrayHelper::getValue($produto, 'prod.cProd')] = $produto;
                    
                    $produtos_dados[ArrayHelper::getValue($produto, 'prod.cProd')]["tipo_nota_fiscal"] = ArrayHelper::getValue($nota_fiscal, 'ide.tpNF');
                }
                
            }
            
            //break;
            
            /*if($quantidade_nota_encontrada>=2){
             break;
             }*/
        }
        //NOTA FISCAL OMIE CONTA DUPLICADA
        //print_r($produtos_dados);die;

        //NOTA FISCAL OMIE PRINCIPAL
        $data_filtro = date('d/m/Y', strtotime("-60 days",strtotime(date('Y-m-d'))));
        
        $body = [
            "call" => "ListarNF",
            "app_key" => $APP_KEY_OMIE_SP,
            "app_secret" => $APP_SECRET_OMIE_SP,
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 500,
                "apenas_importado_api" => "N",
                "ordenar_por" => "CODIGO",
                "dEmiInicial" => $data_filtro,
                "tpNF" => 0, //Tipo 0 são as notas de entrada e a 1 são as notas de saída
            ]
        ];
        
        $response_nota_fiacal_compra = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
        //print_r($response_nota_fiacal_compra);die;
        
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
                    "registros_por_pagina" => 500,
                    "apenas_importado_api" => "N",
                    "ordenar_por" => "CODIGO",
                    "dEmiInicial" => $data_filtro,
                    "tpNF" => 0, //Tipo 0 são as notas de entrada e a 1 são as notas de saída
                ]
            ];
            $response_nota_fiacal_compra = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
            
            foreach(ArrayHelper::getValue($response_nota_fiacal_compra, 'body.nfCadastro') as $k => $nota_fiscal){
                //echo "\n"; print_r($nota_fiscal); die;
                
                echo " - ".$k;
                
                if(!array_key_exists("det", $nota_fiscal)){
                    echo " - (FOR PRODUTOS NF k:".$k.")";
                    continue;
                }
                
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
                    $produtos_dados[ArrayHelper::getValue($produto, 'prod.cProd')]["tipo_nota_fiscal"] = ArrayHelper::getValue($nota_fiscal, 'ide.tpNF');
                }
                
            }
            
            //break;
            
            /*if($quantidade_nota_encontrada>=2){
                break;
            }*/
        }
        //NOTA FISCAL OMIE PRINCIPAL
        
        print_r($produtos_dados);
        //die;
        
        $produtos_remessa               = "";
        $produtos_remessa_devolucao     = "";
        $quantidade_produtos_remessa    = 0;
        $quantidade_remessas_criadas    = 1;
        
        
        //Posição de estoque do Omie CONTA DUPLICADA
        $body = [
            "call" => "ListarPosEstoque",
            "app_key" => $APP_KEY_OMIE_CONTA_MG4,
            "app_secret" => $APP_SECRET_OMIE_CONTA_MG4,
            "param" => [
                "nPagina"       => 1,
                "nRegPorPagina" => 500,
                "dDataPosicao"  => date("d/m/Y"),
                "cExibeTodos"   => "N",
            ]
        ];
        $response_omie_posicao_estoque = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
        //print_r($response_omie_posicao_estoque); die;
        $total_de_paginas_posicao_estoque = ArrayHelper::getValue($response_omie_posicao_estoque, 'body.nTotPaginas');
        
        $contador = 0;
        
        for($i=1;$i<=$total_de_paginas_posicao_estoque;$i++){
            $body = [
                "call" => "ListarPosEstoque",
                "app_key" => $APP_KEY_OMIE_CONTA_MG4,
                "app_secret" => $APP_SECRET_OMIE_CONTA_MG4,
                "param" => [
                    "nPagina"       => $i,
                    "nRegPorPagina" => 500,
                    "dDataPosicao"  => date("d/m/Y"),
                    "cExibeTodos"   => "N",
                ]
            ];
            $response_omie_posicao_estoque = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
            
            if(!array_key_exists("body", $response_omie_posicao_estoque)){
                echo "\n\nSem BODY na lista de posição de estoque\n\n";
                break;
            }
            
            foreach($response_omie_posicao_estoque["body"]["produtos"] as $k => $produto_estoque){
                //print_r($produto_estoque); die;
                
                echo "\n".$contador++." - ".$k." - ".$produto_estoque["cCodigo"];
                
                $estoque    = (int) $produto_estoque["nSaldo"];
                
                if($estoque < 0){
                    echo " - Remessa padrão";
                    
                    if(array_key_exists($produto_estoque["cCodigo"], $produtos_dados)){
                        
                        $cfop                   = $produtos_dados[$produto_estoque["cCodigo"]]['prod']['CFOP'];
                        $csosn                  = "60";
                        $quantidade             = (-1)*$estoque;
                        
                        $codigo_produto         = "";
                        $codigo                 = "";
                        $body = [
                            "call" => "ConsultarProduto",
                            "app_key" => $APP_KEY_OMIE_SP,
                            "app_secret" => $APP_SECRET_OMIE_SP,
                            "param" => [
                                "codigo"            => $produto_estoque["cCodigo"],
                            ]
                        ];
                        $response = $omie->consulta_produto("api/v1/geral/produtos/?JSON=",$body);
                        //print_r($response);die;
                        if($response["httpCode"] > 300){
                            echo " - Produto não encontrado pelo PA no Omie principal";
                            continue;
                        }
                        else{
                            $codigo_produto = $response["body"]["codigo_produto"];
                            $codigo         = $response["body"]["codigo_produto"].date("Y-m-d");
                        }
                        
                        //print_r($produtos_dados);
                        $quantidade_nota        = $produtos_dados[$produto_estoque["cCodigo"]]['prod']['qCom'];
                        $valor_produto          = ArrayHelper::getValue($produtos_dados[$produto_estoque["cCodigo"]], 'prod.vProd');
                        $valor_total            = ArrayHelper::getValue($produtos_dados[$produto_estoque["cCodigo"]], 'prod.vTotItem');
                        $valor_st               = round($valor_total,2) - round($valor_produto,2);
                        $valor_unitario         = (($valor_produto==0) ? 1 : $valor_produto) / (($quantidade_nota==0) ? 1 : $quantidade_nota);
                        $valor_unitario_nota    = ArrayHelper::getValue($produtos_dados[$produto_estoque["cCodigo"]], 'prod.nCMCUnitario');
                        
                        $ean                    = $response["body"]["ean"];
                        $ncm                    = $response["body"]["ncm"];
                        $icms_nAliq = 0;
                        
                        switch ($cfop){
                            
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
                            case "1.652":   $cfop = "5.659";
                            break;
                            case "2.652":   $cfop = "5.659";
                            break;
                            
                            default: $cfop = "5.409";
                        }
                        
                        if($cfop == "5.152"){
                            $csosn  = "00";
                        }
                        elseif($cfop == "5.409"){
                            $csosn  = "60";
                        }
                        else{
                            $csosn  = "60";
                        }
                        
                        $valor_total_final = $quantidade * $valor_unitario;
                        
                        if($csosn == "00"){
                            $icms_nAliq = 18;
                        }
                        
                        $cfop   = "6.409";
                        $csosn  = "60";
                        
                        $produtos_remessa .= '{"COFINS":{"cSitTribCOFINS":"49","cTpCalcCOFINS":"B","nAliqCOFINS":0,"nBCCOFINS":0,"nQtdUTCOFINS":0,"nVaCOFINSSUT":0,
"nValCOFINS":0},"ICMS":{"cModBC":3,"cOrigem":0,"cSitTrib":"'.$csosn.'","nAliq":'.$icms_nAliq.',
"nBC":0,"nRedBC":0,"nValor":0},"ICMS_ST":{"cModBCST":"4","nAliqOP":0,"nAliqST":0,"nBCST":0,"nMargVrAd":0,"nRedBCST":0,"nValorST":0},
"PIS":{"cSitTribPIS":"49","cTpCalcPIS":"B","nAliqPIS":0,"nBCPIS":0,"nQtdUTPIS":0,"nValPIS":0,"nValPISUT":0},
"ICMS_SN":{"cOrigem":0,"cSitTribSN":"'.$csosn.'","nAliqSN":0,"nBCSN":0,
"nCredSN":0,"nValorSN":0},"cCFOP":"'.$cfop.'","cCest":"01.075.00","cCodItInt":"'.$codigo.'",
"cEAN":"'.$ean.'","cNCM":"'.$ncm.'","infAdicItem":{"cInfItemNF":"","cNaoMovEstoque":"N",
"cPedCompra":"","nItemPedCompra":0,"nPesoBruto":0.1,"nPesoLiq":0.1},"nCodProd":"'.$codigo_produto.'",
"nDesconto":0,"nQtde":'.$quantidade.',"nValUnit":'.$valor_unitario.',"rastreabilidade":[]},';
                       
                        //echo $produtos_remessa; die;
                        
                        fwrite($arquivo_log,"\n".$codigo_produto.";".$csosn.";".$cfop.";".$ean.";".$ncm.";".$quantidade.";".$valor_unitario);
                        
                        if($quantidade_produtos_remessa <= 300){
                            $quantidade_produtos_remessa++;
                        }
                        else{
                            break;
                        }
                    }
                }
            }
        }
        //Posição de estoque do Omie CONTA DUPLICADA
        //die;
        
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        $email_texto        = "Bom dia!\n\n\nSeguem remessas geradas:\n\n";
        $status_padrao      = false;
        
        $produtos_remessa = '"produtos":['.$produtos_remessa.']';
        $produtos_remessa = str_replace("[]},]", "[]}]",$produtos_remessa);
        
        $body = '{"call": "IncluirRemessa","app_key": "'.$APP_KEY_OMIE_SP.'","app_secret": "'.$APP_SECRET_OMIE_SP.'",
"param":[{"cabec":{"cCodIntRem": "'.date("Y-m-d")."_".$quantidade_remessas_criadas.'_MG4","dPrevisao": "'.date("d/m/Y").'","nCodCli": "3028165450","nCodRem": "","nCodVend": 0},
"email":{"cEmail": "felipe@optsolucoes.com"},"frete":{"cEspVol": "","cMarVol": "","cNumVol": "",
"cPlaca": "","cTpFrete": 0,"cUF": "","nCodTransp": 2648058022,"nPesoBruto": 10,"nPesoLiq": 10,
"nQtdVol": 1,"nValFrete": 0,"nValOutras": 0,"nValSeguro": 0},"infAdic":{"cCodCateg": "1.01.03",
"cConsFinal": "N","cContato": "","cDadosAdic": "",
"cNumCtr": "","cPedido": "","nCodProj": 0,"nfRelacionada": [{"InscrEstPR": "","cChaveRef": "",
"cSeriePR": "","cSerieRef": "","cUfPR": "","cUfRef": "","cnpjEmitRef": "","cnpjPR": "",
"dtEmissaoPR": "","dtEmissaoRef": "","indPresenca": "","nCOORef": "","nECFRef": "",
"nNFRef": "","nNfPR": ""}]},"obs":{"cObs": ""},'.$produtos_remessa.'}]}';
        
//"cTpFrete": "0",
//"nCodTransp": 2648058022,
//"nPesoBruto": 10,
//"nPesoLiq": 10,
//"nQtdVol": 1,
        
        $body = str_replace("\n","",$body);
        
        $url = "https://app.omie.com.br/api/v1/produtos/remessa/";
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $body );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        curl_close($ch);
        
        //echo "\n\n".$result."\n\n";
        
        $reposta_array = json_decode($result);
        print_r($reposta_array);
        
        if(array_key_exists("nCodRem",$reposta_array)){
            $email_texto .= "Remessa (SP2 => MG4)  =>  ".$reposta_array->cNumeroRemessa;
            $status_padrao      = true;
            fwrite($arquivo_log,"\n\n\ncodigo_remessa;codigo_remessa_integracao;codigo_status;descricao_status;numero_remessa");
            fwrite($arquivo_log,"\n".$reposta_array->nCodRem.";".$reposta_array->cCodIntRem.";".$reposta_array->cCodStatus.";".$reposta_array->cDesStatus.";".$reposta_array->cNumeroRemessa);
        }
        else{
            fwrite($arquivo_log,"\n\n\nRemessa não criada!");
        }
        
        echo "\n\nFinalizadas as notas de remessa!!!";
        
        if($status_padrao){
            $assunto = "Remessas criadas dia ".date("d/m/Y");
            $email_texto .= "\n\n\nAtte,\n\nPeça Agora";
            
            var_dump(\Yii::$app->mailer->compose()
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                ->setTo(["fiscal1pecaagora@gmail.com", "dev.pecaagora@gmail.com","financeiro.pecaagora@gmail.com", "nfe.pecaagora@gmail.com"])
                ->setSubject($assunto)
                ->setTextBody($email_texto)
                ->send());
        }
        
        fclose($arquivo_log);
 
    }
}

