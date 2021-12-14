<?php

namespace console\controllers\actions\omie;

use common\models\Produto;

class CorrigirPesosTabelaAction extends Action
{
    public function run($conta_omie)
    {
        
        //Função deve rodar de segunda a sexta
       
        $arquivo_log = fopen("/var/tmp/log_omie_correcao_peso_tabela_".date("Y-m-d_H-i-s").".csv", "a");
        
        echo "Corrigir pesos no Omie...\n\n";
        
        $omie = new Omie(static::APP_ID, static::SECRET_KEY);
        
        $app_key    = "";
        $app_secret = "";
        $arquivo    = "/var/tmp/PRODUTOS_SP2_PESO.csv";
        
        switch ($conta_omie){
            case 'sp':
                $app_key    = "468080198586";
                $app_secret = "7b3fb2b3bae35eca3b051b825b6d9f43";
                $arquivo    = "produtos_SP_pa_peso_acima.csv";
                break;
            case 'filial':
                $app_key    = "1017311982687";
                $app_secret = "78ba33370fac6178da52d42240591291";
                $arquivo    = "produtos_FILIAL_pa_peso_acima.csv";
                break;
            case 'mg':
                $app_key    = "469728530271";
                $app_secret = "6b63421c9bb3a124e012a6bb75ef4ace";
                $arquivo    = "produtos_MG_pa_peso_acima.csv";
                break;
        }

        //TESTE
        
            
        
        //TESTE
        
        //Dados dos produtos da conta principal do Omie
        $x = 0;

        $produtos = Produto::find()->orderBy(["id" => SORT_ASC])->all();
        
        foreach($produtos as $k => $produto){

            echo "\n".$x++." - PA".$produto->id;
            //continue;
            
            if($x < 38454){
                echo " - Pular";
                continue;
            }
            
            $body = [
                "call" => "ConsultarProduto",
                "app_key" => $app_key,
                "app_secret" => $app_secret,
                "param" => [
                    "codigo"    => "PA".$produto->id
                ]
            ];
            
            $response_produto_busca = $omie->consulta("/api/v1/geral/produtos//?JSON=",$body);
            //print_r($response_produto_busca);die;
            
            if($response_produto_busca["httpCode"] < 300){
                $body = [
                    "call" => "AlterarProduto",
                    "app_key" => $app_key,
                    "app_secret" => $app_secret,
                    "param" => [
                        "codigo_produto"    => $response_produto_busca["body"]["codigo_produto"],
                        //"codigo"            => $line[0],
                        "peso_liq"          => 0.001,
                        "peso_bruto"        => 0.001
                    ]
                ];
                
                $response_produto_alteracao = $omie->consulta("/api/v1/geral/produtos//?JSON=",$body);
                //print_r($response_produto_alteracao);
                if($response_produto_alteracao["httpCode"] < 300){
                    echo " - OK";
                }
                else{
                    echo " - ERRO";
                    print_r($response_produto_alteracao);
                }
            }
            else{
                $body = [
                    "call" => "ConsultarProduto",
                    "app_key" => $app_key,
                    "app_secret" => $app_secret,
                    "param" => [
                        "codigo"    => $produto->codigo_global
                    ]
                ];
                
                $response_produto_busca = $omie->consulta("/api/v1/geral/produtos//?JSON=",$body);
                //print_r($response_produto_busca);die;
                
                if($response_produto_busca["httpCode"] < 300){
                    $body = [
                        "call" => "AlterarProduto",
                        "app_key" => $app_key,
                        "app_secret" => $app_secret,
                        "param" => [
                            "codigo_produto"    => $response_produto_busca["body"]["codigo_produto"],
                            //"codigo"            => $line[0],
                            "peso_liq"          => 0.001,
                            "peso_bruto"        => 0.001
                        ]
                    ];
                    
                    $response_produto_alteracao = $omie->consulta("/api/v1/geral/produtos//?JSON=",$body);
                    //print_r($response_produto_alteracao);
                    if($response_produto_alteracao["httpCode"] < 300){
                        echo " - OK";
                    }
                    else{
                        echo " - ERRO";
                        print_r($response_produto_alteracao);
                    }
                }
            }
        }
        
        echo "\n\nFinalizadas correção de pesos no Omie!!!";
        
        fclose($arquivo_log);
 
    }
}

