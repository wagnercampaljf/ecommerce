<?php

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
        echo "Sincronizar estoque omie...\n\n";
        $arquivo_log = fopen("/var/tmp/log_puxar_estoque_omie/log_puxar_estoque_omie_".date("Y-m-d_H-i-s").".csv", "a");
        
        fwrite($arquivo_log,date("Y-m-d_H-i-s")."\n\n");
        
        $omie = new Omie(static::APP_ID, static::SECRET_KEY);
                
        $APP_KEY_OMIE_MG                    = '469728530271';
        $APP_SECRET_OMIE_MG                 = '6b63421c9bb3a124e012a6bb75ef4ace';
        
        $APP_KEY_OMIE_SP                    = '468080198586';
        $APP_SECRET_OMIE_SP                 = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_DUPLICADA       = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA    = '78ba33370fac6178da52d42240591291';

        $APP_KEY_OMIE_MG4       	= '1758907907757';
        $APP_SECRET_OMIE_MG4    	= '0a69c9b49e5a188e5f43d5505f2752bc';
        
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
        
        $codigo_produtos                = [];
        $codigo_produtos_filtro_pa      = "0";
        $codigo_produtos_filtro_global  = "'0'";
        
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
                fwrite($arquivo_log,"\n".$produto["cCodigo"].";".$produto["nSaldo"]);
                
                $codigo_produtos[]          = $produto["cCodigo"];
                
                
                $pos = strpos($produto["cCodigo"], "PA");
                echo "(".$pos.")";
                
                if($pos === false || $pos != 0){
                    echo " - Código Global";
                    
                    $codigo_global      = $produto["cCodigo"];
                    $produto_alteracao  = Produto::find()->andWhere(['=', 'codigo_global', $codigo_global])->one();
                    if($produto_alteracao){
                        echo " - produto encontrado";
                        
                        $codigo_produtos_filtro_pa .= ",".$produto_alteracao->id;
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=', 'filial_id', 96])
                        ->andWhere(['=', 'produto_id', $produto_alteracao->id])
                        ->one();
                        
                        if($produto_filial){
                            echo " - estoque encontrado";

			    if($produto_filial->quantidade != $produto["nSaldo"]){
	                            $produto_filial->quantidade = $produto["nSaldo"];
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
                            $produto_filial_novo->filial_id     = 96;
                            $produto_filial_novo->quantidade         = $produto["nSaldo"];
                            $produto_filial_novo->envio              = 1;
                            
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
                else{
                    echo " - Código PA";    
                    
                    $id                 = str_replace("PA", "", $produto["cCodigo"]);
		    $id                 = str_replace("-", "", str_replace(".", "", str_replace(",", "", str_replace("_", "", str_replace("+", "", str_replace("|", "", str_replace("*", "", $id)))))));
                    $produto_alteracao  = Produto::find()->andWhere(['=', 'id', $id])->one();
                    if($produto_alteracao){
                        echo " - produto encontrado";
                        
                        $codigo_produtos_filtro_pa .= ",".$produto_alteracao->id;
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=', 'filial_id', 96])
                                                                ->andWhere(['=', 'produto_id', $produto_alteracao->id])
                                                                ->one();
                    
                        if($produto_filial){
                            echo " - estoque encontrado";

			    if($produto_filial->quantidade != $produto["nSaldo"]){
	                            $produto_filial->quantidade = $produto["nSaldo"];
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
                            $produto_filial_novo->filial_id     = 96;
                            $produto_filial_novo->quantidade         = $produto["nSaldo"];
                            $produto_filial_novo->envio              = 1;
                            
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
            }
        }
        
        $produtos_filial_zerar = ProdutoFilial::find() ->where(" filial_id = 96 and produto_id not in (".$codigo_produtos_filtro_pa.") ")  
                                                ->orderBy("id")
                                                ->all();
        foreach($produtos_filial_zerar as $j => $produto_filial_zerar){
            echo "\n".$j." - ".$produto_filial_zerar->id." - zerar";
//break;
            /*$produto_filial_casada = ProdutoFilial::find()  ->andWhere(['=', 'produto_id', $produto_filial_zerar->produto_id])
                                                            ->andWhere(['=', 'filial_id', 8])
                                                            ->one();
            if($produto_filial_casada){
                
                echo " - Filial Casada encontrada";
                
                $produto_filial_casada->quantidade = 9999;
                if($produto_filial_casada->save()){
                    echo " - Venda Casada Alterada";
                }
                else{
                    echo " - Venda Casada Não Alterada";
                }
            }
            else{
                
                echo " - Filial Casada não encontrada";
                
                $produto_filial_casada_novo                 = new ProdutoFilial;
                $produto_filial_casada_novo->produto_id     = $produto_filial_zerar->produto_id;
                $produto_filial_casada_novo->filial_id      = 8;
                $produto_filial_casada_novo->quantidade     = 9999;
                $produto_filial_casada_novo->envio          = 1;
                if($produto_filial_casada_novo->save()){
                    echo " - Venda Casada Criado";
                }
                else{
                    echo " - Venda Casada Não Criado";
                }
            }*/
            
	    if($produto_filial_zerar->quantidade != 0){
	            $produto_filial_zerar->quantidade = 0;
	            if($produto_filial_zerar->save()){
	                echo " -  Zerado na PECA AGORA FISICA";
	            }
	            else{
	                echo " -  Não Zerado na PECA AGORA FISICA";
	            }
	    }
	    else{
		echo " - Mesmo estoque";
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
        
        $codigo_produtos                = [];
        $codigo_produtos_filtro_pa      = "0";
        $codigo_produtos_filtro_global  = "'0'";
        
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
                echo "\n".$k." - ".$produto["cCodigo"];
                $codigo_produtos[]          = $produto["cCodigo"];
                
                fwrite($arquivo_log,"\n".$produto["cCodigo"].";".$produto["nSaldo"]);
                
                $pos = strpos($produto["cCodigo"], "PA");
                echo "(".$pos.")";
                
                if($pos === false || $pos != 0){
                    echo " - Código Global";
                    
                    $codigo_global      = $produto["cCodigo"];
                    $produto_alteracao  = Produto::find()->andWhere(['=', 'codigo_global', $codigo_global])->one();
                    if($produto_alteracao){
                        echo " - produto encontrado";
                        
                        $codigo_produtos_filtro_pa .= ",".$produto_alteracao->id;
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=', 'filial_id', 95])
                        ->andWhere(['=', 'produto_id', $produto_alteracao->id])
                        ->one();
                        
                        if($produto_filial){
                            echo " - estoque encontrado";
                            $produto_filial->quantidade = $produto["nSaldo"];
                            if($produto_filial->save()){
                                echo " - Estoque atualizado";
                            }
                            else{
                                echo " - Estoque não atualizado";
                            }
                        }
                        else{
                            
                            echo " - estoque não encontrado";
                            
                            $produto_filial_novo                = new ProdutoFilial;
                            $produto_filial_novo->produto_id    = $produto_alteracao->id;
                            $produto_filial_novo->filial_id     = 95;
                            $produto_filial_novo->quantidade         = $produto["nSaldo"];
                            $produto_filial_novo->envio              = 0;
                            
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
                else{
                    echo " - Código PA";
                    
                    $id                 = str_replace("PA", "", $produto["cCodigo"]);
                    $id                 = str_replace("-", "", str_replace(".", "", str_replace(",", "", str_replace("_", "", str_replace("*", "", str_replace("|", "", str_replace("+", "", $id)))))));
		    $produto_alteracao  = Produto::find()->andWhere(['=', 'id', $id])->one();
                    if($produto_alteracao){
                        echo " - produto encontrado";
                        
                        $codigo_produtos_filtro_pa .= ",".$produto_alteracao->id;
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=', 'filial_id', 95])
                        ->andWhere(['=', 'produto_id', $produto_alteracao->id])
                        ->one();
                        
                        if($produto_filial){
                            echo " - estoque encontrado";
                            $produto_filial->quantidade = $produto["nSaldo"];
                            if($produto_filial->save()){
                                echo " - Estoque atualizado";
                            }
                            else{
                                echo " - Estoque não atualizado";
                            }
                        }
                        else{
                            
                            echo " - estoque não encontrado";
                            
                            $produto_filial_novo                = new ProdutoFilial;
                            $produto_filial_novo->produto_id    = $produto_alteracao->id;
                            $produto_filial_novo->filial_id     = 95;
                            $produto_filial_novo->quantidade         = $produto["nSaldo"];
                            $produto_filial_novo->envio              = 0;
                            
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
            }
        }
        
        $produtos_filial_zerar = ProdutoFilial::find()  ->where(" filial_id = 95 and produto_id not in (".$codigo_produtos_filtro_pa.") ")
                                                        ->orderBy("id")
                                                        ->all();
        
        foreach($produtos_filial_zerar as $j => $produto_filial_zerar){
            echo "\n".$j." - ".$produto_filial_zerar->id." - zerar";
//break;            
            $produto_filial_zerar->quantidade = 0;
            if($produto_filial_zerar->save()){
                echo " -  Zerado na PECA AGORA SP FILIAL";
            }
            else{
                echo " -  Não Zerado na PECA AGORA SP FILIAL";
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
        
        $codigo_produtos                = [];
        $codigo_produtos_filtro_pa      = "0";
        $codigo_produtos_filtro_global  = "'0'";
        
        fwrite($arquivo_log, "\n\nMG\n\ncodigo;quantidade");
        
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
                echo "\n".$k." - ".$produto["cCodigo"];
                $codigo_produtos[]          = $produto["cCodigo"];
                
                fwrite($arquivo_log,"\n".$produto["cCodigo"].";".$produto["nSaldo"]);
                
                $pos = strpos($produto["cCodigo"], "PA");
                echo "(".$pos.")";
                
                if($pos === false || $pos != 0){
                    echo " - Código Global";
                    
                    $codigo_global      = $produto["cCodigo"];
                    $produto_alteracao  = Produto::find()->andWhere(['=', 'codigo_global', $codigo_global])->one();
                    if($produto_alteracao){
                        echo " - produto encontrado";
                        
                        $codigo_produtos_filtro_pa .= ",".$produto_alteracao->id;
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=', 'filial_id', 94])
                        ->andWhere(['=', 'produto_id', $produto_alteracao->id])
                        ->one();
                        
                        if($produto_filial){
                            echo " - estoque encontrado";
                            $produto_filial->quantidade = $produto["nSaldo"];
                            if($produto_filial->save()){
                                echo " - Estoque atualizado";
                            }
                            else{
                                echo " - Estoque não atualizado";
                            }
                        }
                        else{
                            
                            echo " - estoque não encontrado";
                            
                            $produto_filial_novo                = new ProdutoFilial;
                            $produto_filial_novo->produto_id    = $produto_alteracao->id;
                            $produto_filial_novo->filial_id     = 94;
                            $produto_filial_novo->quantidade         = $produto["nSaldo"];
                            $produto_filial_novo->envio              = 0;
                            
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
                else{
                    echo " - Código PA";
                    
                    $id                 = str_replace("PA", "", $produto["cCodigo"]);
                    $id                 = str_replace("-", "", str_replace(".", "", str_replace(",", "", str_replace("_", "", str_replace("*", "", str_replace("|", "", str_replace("+", "", $id)))))));
                    $produto_alteracao  = Produto::find()->andWhere(['=', 'id', $id])->one();
                    if($produto_alteracao){
                        echo " - produto encontrado";
                        
                        $codigo_produtos_filtro_pa .= ",".$produto_alteracao->id;
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=', 'filial_id', 94])
                        ->andWhere(['=', 'produto_id', $produto_alteracao->id])
                        ->one();
                        
                        if($produto_filial){
                            echo " - estoque encontrado";
                            $produto_filial->quantidade = $produto["nSaldo"];
                            if($produto_filial->save()){
                                echo " - Estoque atualizado";
                            }
                            else{
                                echo " - Estoque não atualizado";
                            }
                        }
                        else{
                            
                            echo " - estoque não encontrado";
                            
                            $produto_filial_novo                = new ProdutoFilial;
                            $produto_filial_novo->produto_id    = $produto_alteracao->id;
                            $produto_filial_novo->filial_id     = 94;
                            $produto_filial_novo->quantidade         = $produto["nSaldo"];
                            $produto_filial_novo->envio              = 0;
                            
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
            }
        }
        
        $produtos_filial_zerar = ProdutoFilial::find()  ->where(" filial_id = 94 and produto_id not in (".$codigo_produtos_filtro_pa.") ")
                                                        ->orderBy("id")
                                                        ->all();
        
        foreach($produtos_filial_zerar as $j => $produto_filial_zerar){
            echo "\n".$j." - ".$produto_filial_zerar->id." - zerar";
            
            $produto_filial_zerar->quantidade = 0;
            if($produto_filial_zerar->save()){
                echo " -  Zerado na PECA AGORA MG";
            }
            else{
                echo " -  Não Zerado na PECA AGORA MG";
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
        
        $codigo_produtos                = [];
        $codigo_produtos_filtro_pa      = "0";
        $codigo_produtos_filtro_global  = "'0'";
        
        fwrite($arquivo_log, "\n\nMG4\n\ncodigo;quantidade");
        
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
                echo "\n".$k." - ".$produto["cCodigo"];
                $codigo_produtos[]          = $produto["cCodigo"];
                
                fwrite($arquivo_log,"\n".$produto["cCodigo"].";".$produto["nSaldo"]);
                
                $pos = strpos($produto["cCodigo"], "PA");
                echo "(".$pos.")";
                
                if($pos === false || $pos != 0){
                    echo " - Código Global";
                    
                    $codigo_global      = $produto["cCodigo"];
                    $produto_alteracao  = Produto::find()->andWhere(['=', 'codigo_global', $codigo_global])->one();
                    if($produto_alteracao){
                        echo " - produto encontrado";
                        
                        $codigo_produtos_filtro_pa .= ",".$produto_alteracao->id;
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=', 'filial_id', 93])
                        ->andWhere(['=', 'produto_id', $produto_alteracao->id])
                        ->one();
                        
                        if($produto_filial){
                            echo " - estoque encontrado";
                            $produto_filial->quantidade = $produto["nSaldo"];
                            if($produto_filial->save()){
                                echo " - Estoque atualizado";
                            }
                            else{
                                echo " - Estoque não atualizado";
                            }
                        }
                        else{
                            
                            echo " - estoque não encontrado";
                            
                            $produto_filial_novo                = new ProdutoFilial;
                            $produto_filial_novo->produto_id    = $produto_alteracao->id;
                            $produto_filial_novo->filial_id     = 93;
                            $produto_filial_novo->quantidade         = $produto["nSaldo"];
                            $produto_filial_novo->envio              = 0;
                            
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
                else{
                    echo " - Código PA";
                    
                    $id                 = str_replace("PA", "", $produto["cCodigo"]);
                    $id                 = str_replace("-", "", str_replace(".", "", str_replace(",", "", str_replace("_", "", str_replace("*", "", str_replace("|", "", str_replace("+", "", $id)))))));
                    $produto_alteracao  = Produto::find()->andWhere(['=', 'id', $id])->one();
                    if($produto_alteracao){
                        echo " - produto encontrado";
                        
                        $codigo_produtos_filtro_pa .= ",".$produto_alteracao->id;
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=', 'filial_id', 93])
                        ->andWhere(['=', 'produto_id', $produto_alteracao->id])
                        ->one();
                        
                        if($produto_filial){
                            echo " - estoque encontrado";
                            $produto_filial->quantidade = $produto["nSaldo"];
                            if($produto_filial->save()){
                                echo " - Estoque atualizado";
                            }
                            else{
                                echo " - Estoque não atualizado";
                            }
                        }
                        else{
                            
                            echo " - estoque não encontrado";
                            
                            $produto_filial_novo                = new ProdutoFilial;
                            $produto_filial_novo->produto_id    = $produto_alteracao->id;
                            $produto_filial_novo->filial_id     = 93;
                            $produto_filial_novo->quantidade         = $produto["nSaldo"];
                            $produto_filial_novo->envio              = 0;
                            
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
            }
        }
        
        $produtos_filial_zerar = ProdutoFilial::find()  ->where(" filial_id = 93 and produto_id not in (".$codigo_produtos_filtro_pa.") ")
                                                        ->orderBy("id")
                                                        ->all();
        
        foreach($produtos_filial_zerar as $j => $produto_filial_zerar){
            echo "\n".$j." - ".$produto_filial_zerar->id." - zerar";
            
            $produto_filial_zerar->quantidade = 0;
            if($produto_filial_zerar->save()){
                echo " -  Zerado na PECA AGORA MG4";
            }
            else{
                echo " -  Não Zerado na PECA AGORA MG4";
            }
        }
        
        fwrite($arquivo_log, "\n\n".date("Y-m-d_H-i-s"));
        fclose($arquivo_log);
        echo "\n\nFIM DA ROTINA DE IMPORTAÇÂO DO ESTOQUE DO OMIE";
    }
}

