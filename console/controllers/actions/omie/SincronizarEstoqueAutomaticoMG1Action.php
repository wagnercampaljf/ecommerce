<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use common\models\MovimentacaoEstoqueMestre;
use common\models\MovimentacaoEstoqueDetalhe;
use common\models\Produto;
use Yii;

class SincronizarEstoqueAutomaticoMG1Action extends Action
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
        
        $APP_KEY_OMIE_MG            = '469728530271';
        $APP_SECRET_OMIE_MG         = '6b63421c9bb3a124e012a6bb75ef4ace';

        //TESTE
        
            
        
        //TESTE
        
        
        //PRODUTOS COMPRADOS POR MG1
        $data_filtro = date('d/m/Y', strtotime("-100 days",strtotime(date('Y-m-d'))));
        $produtos_vendidos_mg1 = [];
                
        $body = [
            "call" => "ListarNF",
            "app_key" => $APP_KEY_OMIE_MG,
            "app_secret" => $APP_SECRET_OMIE_MG,
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 500,
                "apenas_importado_api" => "N",
                "ordenar_por" => "CODIGO",
                "dEmiInicial" => $data_filtro,
                "tpNF" => 1, //Tipo 0 são as notas de entrada e a 1 são as notas de saída
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
                "app_key" => $APP_KEY_OMIE_MG,
                "app_secret" => $APP_SECRET_OMIE_MG,
                "param" => [
                    "pagina" => $x,
                    "registros_por_pagina" => 500,
                    "apenas_importado_api" => "N",
                    "ordenar_por" => "CODIGO",
                    "dEmiInicial" => $data_filtro,
                    "tpNF" => 1, //Tipo 0 são as notas de entrada e a 1 são as notas de saída
                ]
            ];
            $response_nota_fiacal_compra = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
            
            foreach(ArrayHelper::getValue($response_nota_fiacal_compra, 'body.nfCadastro') as $k => $nota_fiscal){
                //echo "\n"; print_r($nota_fiscal); die;
                
                echo " - ".$k." - ".$nota_fiscal["ide"]["hEmi"]." ".$nota_fiscal["ide"]["dEmi"];
                
                if(!array_key_exists("det", $nota_fiscal)){
                    echo " - (FOR PRODUTOS NF k:".$k.")";
                    continue;
                }
                
                foreach(ArrayHelper::getValue($nota_fiscal, 'det') as $y => $produto){
                    //print_r($produto); die;
                    echo "\n".$y." - ".ArrayHelper::getValue($produto, 'prod.cProd'); //die;
                    
                    if(ArrayHelper::getValue($produto, 'prod.cProd') == "PA15160"){
                        echo "=============>>".$x."<<=============";
                        //print_r($nota_fiscal);die;
                        //print_r($response_nota_fiacal_compra); die;
                        //print_r($produto);die;
                        //die;
                    }
                    
                    $produtos_vendidos_mg1[ArrayHelper::getValue($produto, 'prod.cProd')] = [
                                                                                                $produto,
                                                                                                "data_hora_emissao" => $nota_fiscal["ide"]["hEmi"]." ".$nota_fiscal["ide"]["dEmi"]
                                                                                            ];
                    
                    //$produtos_dados[ArrayHelper::getValue($produto, 'prod.cProd')]["tipo_nota_fiscal"] = ArrayHelper::getValue($nota_fiscal, 'ide.tpNF');
                }
            }
        }
        
        //print_r($produtos_vendidos_mg1); die;
        
        //PRODUTOS VENDIDOS POR MG1
        $data_filtro_comprados_mg1 = date('d/m/Y', strtotime("-100 days",strtotime(date('Y-m-d'))));
        $produtos_comprados_mg1 = [];
        
        $body = [
            "call" => "ListarNF",
            "app_key" => $APP_KEY_OMIE_MG,
            "app_secret" => $APP_SECRET_OMIE_MG,
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 500,
                "apenas_importado_api" => "N",
                "ordenar_por" => "CODIGO",
                "dEmiInicial" => $data_filtro_comprados_mg1,
                "tpNF" => 0, //Tipo 0 são as notas de entrada e a 1 são as notas de saída
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
                "app_key" => $APP_KEY_OMIE_MG,
                "app_secret" => $APP_SECRET_OMIE_MG,
                "param" => [
                    "pagina" => $x,
                    "registros_por_pagina" => 500,
                    "apenas_importado_api" => "N",
                    "ordenar_por" => "CODIGO",
                    "dEmiInicial" => $data_filtro_comprados_mg1,
                    "tpNF" => 0, //Tipo 0 são as notas de entrada e a 1 são as notas de saída
                ]
            ];
            $response_nota_fiacal_compra = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
            
            foreach(ArrayHelper::getValue($response_nota_fiacal_compra, 'body.nfCadastro') as $k => $nota_fiscal){
                //echo "\n"; print_r($nota_fiscal); die;

                echo " - ".$k." - ".$nota_fiscal["ide"]["hEmi"]." ".$nota_fiscal["ide"]["dEmi"];
                
                if(!array_key_exists("det", $nota_fiscal)){
                    echo " - (FOR PRODUTOS NF k:".$k.")";
                    continue;
                }
                
                foreach(ArrayHelper::getValue($nota_fiscal, 'det') as $y => $produto){
                    //print_r($produto); die;
                    echo "\n".$y." - ".ArrayHelper::getValue($produto, 'prod.cProd'); //die;
                    
                    if(ArrayHelper::getValue($produto, 'prod.cProd') == "PA15160"){
                        echo "=============>>".$x."<<=============";
                        //print_r($nota_fiscal);die;
                        //print_r($response_nota_fiacal_compra); die;
                        //print_r($produto);die;
                        //die;
                    }
                    
                    $produtos_comprados_mg1[ArrayHelper::getValue($produto, 'prod.cProd')] = [
                                                                                                $produto,
                                                                                                "data_hora_emissao" => $nota_fiscal["ide"]["hEmi"]." ".$nota_fiscal["ide"]["dEmi"]
                                                                                            ];
                    
                }
            }
        }
        
        //PRODUTOS COMPRADOS POR SP2
        $data_filtro_comprados_sp2 = date('d/m/Y', strtotime("-100 days",strtotime(date('Y-m-d'))));
        $produtos_comprados_sp2 = [];
        
        $body = [
            "call" => "ListarNF",
            "app_key" => $APP_KEY_OMIE_SP,
            "app_secret" => $APP_SECRET_OMIE_SP,
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 500,
                "apenas_importado_api" => "N",
                "ordenar_por" => "CODIGO",
                "dEmiInicial" => $data_filtro_comprados_sp2,
                "tpNF" => 0, //Tipo 0 são as notas de entrada e a 1 são as notas de saída
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
                "app_key" => $APP_KEY_OMIE_SP,
                "app_secret" => $APP_SECRET_OMIE_SP,
                "param" => [
                    "pagina" => $x,
                    "registros_por_pagina" => 500,
                    "apenas_importado_api" => "N",
                    "ordenar_por" => "CODIGO",
                    "dEmiInicial" => $data_filtro_comprados_sp2,
                    "tpNF" => 0, //Tipo 0 são as notas de entrada e a 1 são as notas de saída
                ]
            ];
            $response_nota_fiacal_compra = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
            
            foreach(ArrayHelper::getValue($response_nota_fiacal_compra, 'body.nfCadastro') as $k => $nota_fiscal){
                //echo "\n"; print_r($nota_fiscal); die;
                
                echo " - ".$k." - ".$nota_fiscal["ide"]["hEmi"]." ".$nota_fiscal["ide"]["dEmi"];
                
                if(!array_key_exists("det", $nota_fiscal)){
                    echo " - (FOR PRODUTOS NF k:".$k.")";
                    continue;
                }
                
                foreach(ArrayHelper::getValue($nota_fiscal, 'det') as $y => $produto){
                    //print_r($produto); die;
                    echo "\n".$y." - ".ArrayHelper::getValue($produto, 'prod.cProd'); //die;
                    
                    if(ArrayHelper::getValue($produto, 'prod.cProd') == "PA15160"){
                        echo "=============>>".$x."<<=============";
                        //print_r($nota_fiscal);die;
                        //print_r($response_nota_fiacal_compra); die;
                        //print_r($produto);die;
                        //die;
                    }
                    
                    $produtos_comprados_sp2[ArrayHelper::getValue($produto, 'prod.cProd')] = [
                                                                                                $produto,
                                                                                                "data_hora_emissao" => $nota_fiscal["ide"]["hEmi"]." ".$nota_fiscal["ide"]["dEmi"]
                                                                                            ];
                    
                }
            }
        }
        
        //print_r($produtos_vendidos_mg1);
        //print_r($produtos_comprados_mg1); 
        //print_r($produtos_comprados_sp2);        
        
        $movimentacao_estoque_mestre                        = new MovimentacaoEstoqueMestre;
        $movimentacao_estoque_mestre->descricao             = "Movimentação de Estoque (Sincronização de estoque - SP2 => MG1) - ".date("Y-m-d H-i-s");
        $movimentacao_estoque_mestre->salvo_em              = date("Y-m-d H:i:s");
        //$movimentacao_estoque_mestre->salvo_por             = Yii::$app->user->identity->id;
        $movimentacao_estoque_mestre->e_autorizado          = true;
        $movimentacao_estoque_mestre->filial_origem_id      = 96;
        $movimentacao_estoque_mestre->filial_destino_id     = 94;
        $movimentacao_estoque_mestre->codigo_remessa_omie   = null;
        if($movimentacao_estoque_mestre->save()){
        
            foreach($produtos_vendidos_mg1 as $codigo => $produto_vnedido_mg1){
                echo "\n".$codigo." - ".$produto_vnedido_mg1["data_hora_emissao"];
                //print_r($produto_vnedido_mg1);
                
                if(!array_key_exists($codigo, $produtos_comprados_mg1)){
                    
                    echo " - Produto não comprado por MG1";
                    
                    if(array_key_exists($codigo, $produtos_comprados_sp2)){
                        
                        $data_hora_venda_mg1    = substr($produto_vnedido_mg1["data_hora_emissao"],15,4)."-".substr($produto_vnedido_mg1["data_hora_emissao"],12,2)."-".substr($produto_vnedido_mg1["data_hora_emissao"],9,2)." ".substr($produto_vnedido_mg1["data_hora_emissao"],0,8);
                        $data_hora_compra_sp2   = substr($produtos_comprados_sp2[$codigo]["data_hora_emissao"],15,4)."-".substr($produtos_comprados_sp2[$codigo]["data_hora_emissao"],12,2)."-".substr($produtos_comprados_sp2[$codigo]["data_hora_emissao"],9,2)." ".substr($produtos_comprados_sp2[$codigo]["data_hora_emissao"],0,8);
                        
                        $data_venda_mg1         = substr($produto_vnedido_mg1["data_hora_emissao"],15,4)."-".substr($produto_vnedido_mg1["data_hora_emissao"],12,2)."-".substr($produto_vnedido_mg1["data_hora_emissao"],9,2);
                        $data_compra_sp2        = substr($produtos_comprados_sp2[$codigo]["data_hora_emissao"],15,4)."-".substr($produtos_comprados_sp2[$codigo]["data_hora_emissao"],12,2)."-".substr($produtos_comprados_sp2[$codigo]["data_hora_emissao"],9,2);
                        
                        $data_filtro_compra_sp2 = date('Y-m-d', strtotime("-5 days",strtotime($data_venda_mg1)));
                        
                        echo " - Produto comprado por SP2 - ".$produtos_comprados_sp2[$codigo]["data_hora_emissao"];
                        
                        echo "\n    Data Compra SP2: ".$data_compra_sp2." Data Filtro Compra SP2: ".$data_filtro_compra_sp2;
                        
                        //continue;
                        if($data_filtro_compra_sp2 <= $data_compra_sp2){
                            $body = [
                                "call" => "PosicaoEstoque",
                                "app_key" => $APP_KEY_OMIE_MG,
                                "app_secret" => $APP_SECRET_OMIE_MG,
                                "param" => [
                                    "codigo_local_estoque"  => 0,
                                    //"id_prod": 0,
                                    "cod_int"               => $codigo,
                                    "data"                  => date("d/m/Y")
                                ]
                            ];
                            $response_posicao_estoque = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
                            //print_r($response_posicao_estoque); 
                            //print_r($produto_vnedido_mg1);
                            //print_r($produtos_comprados_sp2[$codigo]); die;
                            
                            if(!array_key_exists("saldo", $response_posicao_estoque["body"])){
                                continue;
                            }
                            
                            $quantidade_a_transferir = (-1)*$response_posicao_estoque["body"]["saldo"];
                            
                            if($quantidade_a_transferir == 0){
                                continue;
                            }
                            
                            $movimentacao_estoque_detalhe                                   = new MovimentacaoEstoqueDetalhe;
                            $movimentacao_estoque_detalhe->movimentacao_estoque_mestre_id   = $movimentacao_estoque_mestre->id;
                            $movimentacao_estoque_detalhe->descricao                        = $codigo." | ".$produto_vnedido_mg1[0]["prod"]["xProd"];
                            $movimentacao_estoque_detalhe->salvo_em                         = date("Y-m-d H:i:s");
                            $movimentacao_estoque_detalhe->salvo_por                        = null;
                            $movimentacao_estoque_detalhe->quantidade                       = $quantidade_a_transferir;
                            
                            $codigo_produto_omie = $codigo;
                            $pos = strpos($codigo_produto_omie, "PA");
                            if($pos === false || $pos != 0){
                                echo " - Global";
                                $produto_tabela = Produto::find()->andWhere(["=", "codigo_global", $codigo_produto_omie])->one();
                                if($produto_tabela){
                                    echo " - Produto encontrado";
                                    $movimentacao_estoque_detalhe->produto_id   = $produto_tabela->id;
                                }
                                else{
                                    echo " - Produto não encontrado";
                                }
                            }
                            else{
                                echo " - PA";
                                $pa             = (int) str_replace("PA", "", $codigo_produto_omie);
                                //echo " - ".$pa; die;
                                $produto_tabela = Produto::find()->andWhere(["=", "id", $pa])->one();
                                if($produto_tabela){
                                    echo " - Produto encontrado";
                                    $movimentacao_estoque_detalhe->produto_id   = $produto_tabela->id;
                                }
                                else{
                                    echo " - Produto não encontrado";
                                }
                            }
                            
                            $movimentacao_estoque_detalhe->save();
                            
                            ///////////////////////////////////////////////////////
                            //Saída
                            ///////////////////////////////////////////////////////
                            $body = [
                                "call" => "IncluirAjusteEstoque",
                                "app_key" => $APP_KEY_OMIE_SP,
                                "app_secret" => $APP_SECRET_OMIE_SP,
                                "param" => [
                                    "codigo_local_estoque"  => 2643278369,
                                    "id_prod"               => $produtos_comprados_sp2[$codigo][0]["nfProdInt"]["nCodProd"],
                                    "data"                  => date("d/m/Y"),//"29/10/2021",
                                    "quan"                  => $quantidade_a_transferir,
                                    "obs"                   => "Ajuste feito pela API (ID movimentacao_estoque_mestre: ".$movimentacao_estoque_mestre->id.")",
                                    "origem"                => "AJU",
                                    "tipo"                  => "SAI",
                                    "motivo"                => "INV",
                                    "valor"                 => 1
                                ]
                            ];
                            
                            $response_omie_ajuste_estoque_saida = $omie->consulta("/api/v1/estoque/ajuste/?JSON=",$body);
                            //print_r($response_omie_ajuste_estoque_saida); //die;
                            
                            if($response_omie_ajuste_estoque_saida["httpCode"] == 200){
                                echo " - Ajuste criado Omie";
                                
                                $movimentacao_estoque_detalhe->id_ajuste_omie_saida = (string) $response_omie_ajuste_estoque_saida["body"]["id_ajuste"];
                                //print_r($movimentacao_estoque_detalhe);
                                if($movimentacao_estoque_detalhe->save()){
                                    echo " - Movimentação detalhe salva";
                                }
                                else{
                                    echo " - Movimentação detalhe não salva";
                                }
                            }
                            else{
                                echo " - Ajuste não criado Omie";
                            }
                            
                            ///////////////////////////////////////////////////////
                            //Entrada
                            ///////////////////////////////////////////////////////
                           
                            $body = [
                                "call" => "IncluirAjusteEstoque",
                                "app_key" => $APP_KEY_OMIE_MG,
                                "app_secret" => $APP_SECRET_OMIE_MG,
                                "param" => [
                                    "codigo_local_estoque"  => 562764799,
                                    "id_prod"               => $produto_vnedido_mg1[0]["nfProdInt"]["nCodProd"],
                                    "data"                  => date("d/m/Y"),//"29/10/2021",
                                    "quan"                  => $quantidade_a_transferir,
                                    "obs"                   => "Ajuste feito pela API (ID movimentacao_estoque_mestre: ".$movimentacao_estoque_mestre->id.")",
                                    "origem"                => "AJU",
                                    "tipo"                  => "ENT",
                                    "motivo"                => "INV",
                                    "valor"                 => 1
                                ]
                            ];
                            
                            $response_omie_ajuste_estoque_entrada = $omie->consulta("/api/v1/estoque/ajuste/?JSON=",$body);
                            //print_r($response_omie_ajuste_estoque_entrada); //die;
                            
                            if($response_omie_ajuste_estoque_entrada["httpCode"] == 200){
                                echo " - Ajuste criado Omie";
                                
                                $movimentacao_estoque_detalhe->id_ajuste_omie_entrada = (string) $response_omie_ajuste_estoque_entrada["body"]["id_ajuste"];
                                //print_r($movimentacao_estoque_detalhe);
                                if($movimentacao_estoque_detalhe->save()){
                                    echo " - Movimentação detalhe salva";
                                }
                                else{
                                    echo " - Movimentação detalhe não salva";
                                }
                            }
                            else{
                                echo " - Ajuste não criado Omie";
                            }
                            //die;
                        }
                        else{
                            echo " - DATA DIVERGENTE";
                        }                            
                    }
                    else{
                        echo " - Produto não comprado por SP2";
                    }
                }
                else{
                    echo " - Produto comprado por MG1";
                }
            }
        }
        
        fclose($arquivo_log);
        
    }
}

