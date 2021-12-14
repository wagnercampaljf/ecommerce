<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use common\models\Produto;

class ExcluirCaixasAction extends Action
{
    public function run()//$global_id)
    {
        
        echo "Excluir Lampadas\n\n";
        
        if (file_exists("/var/tmp/log_omie_excluir_lampadas_03-09-2020.csv")){
            unlink("/var/tmp/log_omie_excluir_lampadas_03-09-2020.csv");
        }
        
        $arquivo_nome = "/var/tmp/log_omie_excluir_lampadas_03-09-2020.csv";
        $arquivo_log = fopen($arquivo_nome, "a");
        //Escreve no log
        fwrite($arquivo_log, "produto_id;status_codigo_sp;http_code_sp;status_omie_sp;status_codigo_conta_duplicada;http_code_conta_duplicada;status_omie_conta_duplicada;status_codigo_mg;http_code_mg;status_omie_mg");
        
        $meli = new Omie(static::APP_ID, static::SECRET_KEY);
        
        $produtos = Produto::find() ->andWhere(["like", "codigo_global", "CX."])
                                    ->orderBy("id")
                                    ->all();
        
        foreach ($produtos as $k => $produto) {

            echo "\n\n".$k." - ".$produto->id." - PA".$produto->id." - ".$produto->codigo_global;
            //continue;
            fwrite($arquivo_log, "\n".$produto->id.";");
            
            ////////////////////////////////////////////////////////////////////////
            //SÃO PAULO - CONTA PRINCIPAL
            ////////////////////////////////////////////////////////////////////////
            echo "\n    SÃO PAULO: ";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_SP.'","app_secret":"'.static::APP_SECRET_OMIE_SP.'","param":[{"codigo":"'.$produto->codigo_global.'"}]}');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $produtos = curl_exec($ch);
            $produtos_codigo = json_decode($produtos);
            curl_close($ch);
            //print_r($produtos_codigo);
            
            $codigo_produto = '';
            
            if(isset($produtos_codigo->codigo_produto)){
                echo " - Produto encontrado pelo código_global";
                fwrite($arquivo_log, "Produto encontrado pelo código_global;");
                $codigo_produto = $produtos_codigo->codigo_produto;
            }
            else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_SP.'","app_secret":"'.static::APP_SECRET_OMIE_SP.'","param":[{"codigo":"PA'.$produto->id.'"}]}');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $produtos = curl_exec($ch);
                $produtos_codigo = json_decode($produtos);
                curl_close($ch);
                //print_r($produtos_codigo);
                
                if(isset($produtos_codigo->codigo_produto)){
                    echo " - Produto encontrado pelo código PA";
                    fwrite($arquivo_log, "Produto encontrado pelo código PA;");
                    $codigo_produto = $produtos_codigo->codigo_produto;
                }
                else{
                    echo " - Produto não encontrado";
                    fwrite($arquivo_log, "Produto não encontrado;");
                }
            }
            
            $body = [
                "call" => "ExcluirProduto",
                "app_key" => static::APP_KEY_OMIE_SP,
                "app_secret" => static::APP_SECRET_OMIE_SP,
                "param" => [
                    "codigo_produto"            => $codigo_produto,
                    //"codigo_produto_integracao" => "PA".$produto->id,
                    //"codigo"                    => "PA".$produto->id,
                ]
            ];
            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            fwrite($arquivo_log, ArrayHelper::getValue($response, 'httpCode').";");
            if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                 echo " - Erro";
                 fwrite($arquivo_log, "ERRO;");
            }
                 else{
                 echo " - OK";
                 fwrite($arquivo_log, "OK;");
            }
            
            ////////////////////////////////////////////////////////////////////////
            //SÃO PAULO - CONTA DUPLICADA
            ////////////////////////////////////////////////////////////////////////
            echo "\n    CONTA DUPLICADA: ";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_CONTA_DUPLICADA.'","app_secret":"'.static::APP_SECRET_OMIE_CONTA_DUPLICADA.'","param":[{"codigo":"'.$produto->codigo_global.'"}]}');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $produtos = curl_exec($ch);
            $produtos_codigo = json_decode($produtos);
            curl_close($ch);
            //print_r($produtos_codigo);
            
            $codigo_produto = '';
            
            if(isset($produtos_codigo->codigo_produto)){
                echo " - Produto encontrado pelo código_global";
                fwrite($arquivo_log, "Produto encontrado pelo código_global;");
                $codigo_produto = $produtos_codigo->codigo_produto;
            }
            else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_CONTA_DUPLICADA.'","app_secret":"'.static::APP_SECRET_OMIE_CONTA_DUPLICADA.'","param":[{"codigo":"PA'.$produto->id.'"}]}');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $produtos = curl_exec($ch);
                $produtos_codigo = json_decode($produtos);
                curl_close($ch);
                //print_r($produtos_codigo);
                
                if(isset($produtos_codigo->codigo_produto)){
                    echo " - Produto encontrado pelo código PA";
                    fwrite($arquivo_log, "Produto encontrado pelo código PA;");
                    $codigo_produto = $produtos_codigo->codigo_produto;
                }
                else{
                    echo " - Produto não encontrado";
                    fwrite($arquivo_log, "Produto não encontrado;");
                }
            }
            
            $body = [
                "call" => "ExcluirProduto",
                "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                "param" => [
                    "codigo_produto"            => $codigo_produto,
                    //"codigo_produto_integracao" => "PA".$produto->id,
                    //"codigo"                    => "PA".$produto->id,
                ]
            ];
            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            fwrite($arquivo_log, ArrayHelper::getValue($response, 'httpCode').";");
            if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                echo " - Erro";
                fwrite($arquivo_log, "ERRO;");
            }
            else{
                echo " - OK";
                fwrite($arquivo_log, "OK;");
            }
            
            ////////////////////////////////////////////////////////////////////////
            //MINAS GERAIS
            ////////////////////////////////////////////////////////////////////////
            echo "\n    MINAS GERAIS: ";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_MG.'","app_secret":"'.static::APP_SECRET_OMIE_MG.'","param":[{"codigo":"'.$produto->codigo_global.'"}]}');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $produtos = curl_exec($ch);
            $produtos_codigo = json_decode($produtos);
            curl_close($ch);
            //print_r($produtos_codigo);
            
            $codigo_produto = '';
            
            if(isset($produtos_codigo->codigo_produto)){
                echo " - Produto encontrado pelo código_global";
                fwrite($arquivo_log, "Produto encontrado pelo código_global;");
                $codigo_produto = $produtos_codigo->codigo_produto;
            }
            else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_MG.'","app_secret":"'.static::APP_SECRET_OMIE_MG.'","param":[{"codigo":"PA'.$produto->id.'"}]}');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $produtos = curl_exec($ch);
                $produtos_codigo = json_decode($produtos);
                curl_close($ch);
                //print_r($produtos_codigo);
                
                if(isset($produtos_codigo->codigo_produto)){
                    echo " - Produto encontrado pelo código PA";
                    fwrite($arquivo_log, "Produto encontrado pelo código PA;");
                    $codigo_produto = $produtos_codigo->codigo_produto;
                }
                else{
                    echo " - Produto não encontrado";
                    fwrite($arquivo_log, "Produto não encontrado;");
                }
            }
            
            $body = [
                "call" => "ExcluirProduto",
                "app_key" => static::APP_KEY_OMIE_MG,
                "app_secret" => static::APP_SECRET_OMIE_MG,
                "param" => [
                    "codigo_produto"            => $codigo_produto,
                    //"codigo_produto_integracao" => "PA".$produto->id,
                    //"codigo"                    => "PA".$produto->id,
                ]
            ];
            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            fwrite($arquivo_log, ArrayHelper::getValue($response, 'httpCode').";");
            if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                echo " - Erro";
                fwrite($arquivo_log, "ERRO;");
            }
            else{
                echo " - OK";
                fwrite($arquivo_log, "OK;");
            }
        }
    }
}



