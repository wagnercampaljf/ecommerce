<?php
//4444
namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use common\models\Filial;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreProduto;
use common\models\ProdutoFilial;
use common\models\Produto;

class PuxarEstoqueDoOmieAction extends Action
{
    public function run()
    {

        //die;

        echo "Sincronizar estoque omie...\n\n";
        $arquivo_log = fopen("/var/tmp/log_puxar_estoque_omie/log_puxar_estoque_omie_".date("Y-m-d_H-i-s").".csv", "a");
        
        fwrite($arquivo_log,date("Y-m-d_H-i-s")."\n\n");
        
        $omie = new Omie(static::APP_ID, static::SECRET_KEY);
                
        $APP_KEY_OMIE_MG                   = '469728530271';
        $APP_SECRET_OMIE_MG                = '6b63421c9bb3a124e012a6bb75ef4ace';
        
        $APP_KEY_OMIE_SP                   = '468080198586';
        $APP_SECRET_OMIE_SP                = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA   = '78ba33370fac6178da52d42240591291';

        $APP_KEY_OMIE_MG4       	       = '1758907907757';
        $APP_SECRET_OMIE_MG4    	       = '0a69c9b49e5a188e5f43d5505f2752bc';
        
        $dados = [];        
        
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //CONTA PRINCIPAL SP
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        echo "\n\nOMIE SP\n\n";
        
        $body = [
            "call" => "ListarPosEstoque",
            "app_key" => $APP_KEY_OMIE_SP,
            "app_secret" => $APP_SECRET_OMIE_SP,
            "param" => [
                "nPagina"               => 1,
                "nRegPorPagina"         => 500,
                "dDataPosicao"          => date("d/m/Y"),
                "cExibeTodos"           => "N"
                //"codigo_local_estoque": 0
            ]
        ];
        $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
        //print_r($response_omie); die;        
        $quantidade_paginas = $response_omie["body"]["nTotPaginas"];
        
        fwrite($arquivo_log, "SP\n\ncodigo;quantidade");
        
        for($i = 1;$i <= $quantidade_paginas;$i++){
        //break;            
            echo "\nPágina: ".$i;
            $body = [
                "call" => "ListarPosEstoque",
                "app_key" => $APP_KEY_OMIE_SP,
                "app_secret" => $APP_SECRET_OMIE_SP,
                "param" => [
                    "nPagina"               => $i,
                    "nRegPorPagina"         => 500,
                    "dDataPosicao"          => date("d/m/Y"),
                    "cExibeTodos"           => "N"
                    //"codigo_local_estoque": 0
                ]
            ];
            $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
            foreach($response_omie["body"]["produtos"] as $k => $produto){
                
                //print_r($produto); die;
                echo "\n".$k." - ".$produto["cCodigo"]." - Quantidade: ".$produto["nSaldo"];
                
                $dados[96][$produto["cCodigo"]] = $produto["nSaldo"];
                
            }
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //CONTA PRINCIPAL SP FILIAL
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        echo "\n\nOMIE SP FILIAL\n\n";
        
        $body = [
            "call" => "ListarPosEstoque",
            "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
            "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
            "param" => [
                "nPagina"               => 1,
                "nRegPorPagina"         => 500,
                "dDataPosicao"          => date("d/m/Y"),
                "cExibeTodos"           => "N"
                //"codigo_local_estoque": 0
            ]
        ];
        $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
        
        $quantidade_paginas = $response_omie["body"]["nTotPaginas"];

        fwrite($arquivo_log, "\n\nFilial SP\n\ncodigo;quantidade");
        
        for($i = 1;$i <= $quantidade_paginas;$i++){
            //break;            
            echo "\nPágina: ".$i;
            $body = [
                "call" => "ListarPosEstoque",
                "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                "param" => [
                    "nPagina"               => $i,
                    "nRegPorPagina"         => 500,
                    "dDataPosicao"          => date("d/m/Y"),
                    "cExibeTodos"           => "N"
                    //"codigo_local_estoque": 0
                ]
            ];
            $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
            foreach($response_omie["body"]["produtos"] as $k => $produto){
                
                //print_r($produto); die;
                echo "\n".$k." - ".$produto["cCodigo"]." - Quantidade: ".$produto["nSaldo"];
                
                $dados[95][$produto["cCodigo"]] = $produto["nSaldo"];
                
                if(array_key_exists($produto["cCodigo"], $dados[96])){
                    $dados[96][$produto["cCodigo"]] += $produto["nSaldo"];
                }
            }
        }
        
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //CONTA PRINCIPAL MG
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        echo "\n\nOMIE MG\n\n";
        
        $body = [
            "call" => "ListarPosEstoque",
            "app_key" => $APP_KEY_OMIE_MG,
            "app_secret" => $APP_SECRET_OMIE_MG,
            "param" => [
                "nPagina"               => 1,
                "nRegPorPagina"         => 500,
                "dDataPosicao"          => date("d/m/Y"),
                "cExibeTodos"           => "N"
                //"codigo_local_estoque": 0
            ]
        ];
        $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
        
        $quantidade_paginas = $response_omie["body"]["nTotPaginas"];
        
        for($i = 1;$i <= $quantidade_paginas;$i++){
            //break;            
            echo "\nPágina: ".$i;
            $body = [
                "call" => "ListarPosEstoque",
                "app_key" => $APP_KEY_OMIE_MG,
                "app_secret" => $APP_SECRET_OMIE_MG,
                "param" => [
                    "nPagina"               => $i,
                    "nRegPorPagina"         => 500,
                    "dDataPosicao"          => date("d/m/Y"),
                    "cExibeTodos"           => "N"
                    //"codigo_local_estoque": 0
                ]
            ];
            $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
            foreach($response_omie["body"]["produtos"] as $k => $produto){
                
                //print_r($produto); die;
                echo "\n".$k." - ".$produto["cCodigo"]." - Quantidade: ".$produto["nSaldo"];
                
                $dados[94][$produto["cCodigo"]] = $produto["nSaldo"];

            }
        }

         ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //CONTA PRINCIPAL MG4
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        echo "\n\nOMIE MG4\n\n";
        
        $body = [
            "call" => "ListarPosEstoque",
            "app_key" => $APP_KEY_OMIE_MG4,
            "app_secret" => $APP_SECRET_OMIE_MG4,
            "param" => [
                "nPagina"               => 1,
                "nRegPorPagina"         => 500,
                "dDataPosicao"          => date("d/m/Y"),
                "cExibeTodos"           => "N"
                //"codigo_local_estoque": 0
            ]
        ];
        $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
        
        $quantidade_paginas = $response_omie["body"]["nTotPaginas"];
        
        for($i = 1;$i <= $quantidade_paginas;$i++){
            
            echo "\nPágina: ".$i;
            $body = [
                "call" => "ListarPosEstoque",
                "app_key" => $APP_KEY_OMIE_MG4,
                "app_secret" => $APP_SECRET_OMIE_MG4,
                "param" => [
                    "nPagina"               => $i,
                    "nRegPorPagina"         => 500,
                    "dDataPosicao"          => date("d/m/Y"),
                    "cExibeTodos"           => "N"
                    //"codigo_local_estoque": 0
                ]
            ];
            $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
            foreach($response_omie["body"]["produtos"] as $k => $produto){
                
                //print_r($produto); die;
                echo "\n".$k." - ".$produto["cCodigo"]." - Quantidade: ".$produto["nSaldo"];
                
                $dados[93][$produto["cCodigo"]] = $produto["nSaldo"];
                
                if(array_key_exists($produto["cCodigo"], $dados[96])){
                    $dados[96][$produto["cCodigo"]] += $produto["nSaldo"];
                }
            }
        }
        
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        //NOVA FUNÇÃO
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        
        foreach($dados as $filial_id => $produtos_dados){
            echo "((".$filial_id."))";
            print_r($produtos_dados);
            
            $codigo_produtos_filtro_pa = "0";
            
            $x = 0;
            
            foreach($produtos_dados as $codigo => $produto_quantidade){
                
                echo "\n".$x++." - ".$codigo;
                
                $pos = strpos($codigo, "PA");

                $produto_alteracao = new Produto;
                if($pos === false || $pos != 0){
                    echo " - Código Global";
                    
                    $codigo_global      = $codigo;
                    $produto_alteracao  = Produto::find()->andWhere(['=', 'codigo_global', $codigo_global])->one();
                }
                else{
                    echo " - Código PA";
                    
                    $id                 = str_replace("PA", "", $codigo);
                    $id                 = str_replace("-", "", str_replace(".", "", str_replace(",", "", str_replace("_", "", str_replace("*", "", str_replace("|", "", str_replace("+", "", $id)))))));
                    $produto_alteracao  = Produto::find()->andWhere(['=', 'id', $id])->one();
                }
                
                if($produto_alteracao){
                    echo " - produto encontrado";
                    
                    $codigo_produtos_filtro_pa .= ",".$produto_alteracao->id;
                    
                    $produto_filial = ProdutoFilial::find() ->andWhere(['=', 'filial_id', $filial_id])
                                                            ->andWhere(['=', 'produto_id', $produto_alteracao->id])
                                                            ->one();
                    
                    if($produto_filial){
                        echo " - estoque encontrado";
                        if($produto_filial->quantidade != $produto_quantidade && $produto_filial->e_atualizar_quantidade_planilha){
                            $produto_filial->quantidade = $produto_quantidade;
                            if($produto_filial->save()){
                                echo " - Estoque atualizado";
                            }
                            else{
                                echo " - Estoque não atualizado";
                            }
                        }
                        else{
                            echo " - Mesmo estoque";
                        }
                    }
                    else{
                        
                        echo " - estoque não encontrado";
                        
                        $produto_filial_novo                = new ProdutoFilial;
                        $produto_filial_novo->produto_id    = $produto_alteracao->id;
                        $produto_filial_novo->filial_id     = $filial_id;
                        $produto_filial_novo->quantidade    = $produto_quantidade;
                        $produto_filial_novo->envio         = 0;
                        
                        if($produto_filial_novo->save()){
                            echo " - Estoque criado";
                        }
                        else{
                            echo " - Estoque não criado";
                        }
                    }
                }
                else{
                    echo " - produto não encontrado";
                }
            }
            
            $produtos_filial_zerar = ProdutoFilial::find()  ->where(" filial_id = ".$filial_id." and produto_id not in (".$codigo_produtos_filtro_pa.") ")
                                                            ->orderBy("id")
                                                            ->all();
            
            foreach($produtos_filial_zerar as $j => $produto_filial_zerar){
                echo "\n".$j." - ".$produto_filial_zerar->id." - zerar";
                //break;
                if($produto_filial_zerar->quantidade != 0 && $produto_filial->e_atualizar_quantidade_planilha){
                    $produto_filial_zerar->quantidade = 0;
                    if($produto_filial_zerar->save()){
                        echo " -  Zerado na PECA AGORA - ".$filial_id;
                    }
                    else{
                        echo " -  Não Zerado na PECA AGORA - ".$filial_id;
                    }
                }
                else{
                    echo " - Mesmo estoque";
                }
            }
        }
    }
}

