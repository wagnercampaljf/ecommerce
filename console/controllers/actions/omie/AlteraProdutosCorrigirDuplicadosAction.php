<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use common\models\Produto;

class AlteraProdutosCorrigirDuplicadosAction extends Action
{
    public function run()//$global_id)
    {
        
        echo "Alterando produtos... São Paulo\n\n";
        $meli = new Omie(static::APP_ID, static::SECRET_KEY);
     
        unlink("/var/tmp/log_omie_todos_produtos.csv");
        $arquivo_log = fopen("/var/tmp/log_omie_todos_produtos.csv", "a");
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/omie_todos_produtos.csv", 'r'); //Abre arquivo com preços para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);
        
        foreach ($LinhasArray as $i => &$linhaArray){
            
            if($i < 950){
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[0];
            
            fwrite($arquivo_log, "\n".$linhaArray[0].";".$linhaArray[1]);
            
            if($i == 0){
                fwrite($arquivo_log, ";status");
                continue;
            }

            $codigo = $linhaArray[0];
            $pos = strpos($codigo, "PA");
            
            if ($pos === false) {
                echo " - Produto SEM código PA no OMIE";
                fwrite($arquivo_log, ";Produto SEM código PA no OMIE");
                
                $produto = Produto::find()->andWhere(['=','codigo_global',$linhaArray[0]])->one();
                
                if($produto){
                    //echo 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_SP.'","app_secret":"'.static::APP_SECRET_OMIE_SP.'","param":[{"codigo":"PA'.$produto->id.'"}]}';
                    echo " - Produto encontrado NO PEÇA, pelo CODIGO_GLOBAL";
                    fwrite($arquivo_log, " - Produto encontrado NO PEÇA, pelo CODIGO_GLOBAL");
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_SP.'","app_secret":"'.static::APP_SECRET_OMIE_SP.'","param":[{"codigo":"PA'.$produto->id.'"}]}');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $produtos = curl_exec($ch);
                    $produtos_codigo = json_decode($produtos);
                    curl_close($ch);
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_SP.'","app_secret":"'.static::APP_SECRET_OMIE_SP.'","param":[{"codigo":"'.$linhaArray[0].'"}]}');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $produto_global = curl_exec($ch);
                    $produto_global_codigo = json_decode($produto_global);
                    curl_close($ch);
                    
                    if(!isset($produto_global_codigo->codigo_produto)){
                        echo " - Produto JÁ EXCLUÍDO no OMIE";
                        fwrite($arquivo_log, " - Produto JÁ EXCLUÍDO no OMIE");
			continue;
                    }
                    
                    if(isset($produtos_codigo->codigo_produto)){
                        echo " - Produto encontrado pelo código PA no Omie";
                        fwrite($arquivo_log, " - Produto encontrado pelo código PA");
                        
                        $body = [
                            "call" => "AlterarProduto",
                            "app_key" => static::APP_KEY_OMIE_SP,
                            "app_secret" => static::APP_SECRET_OMIE_SP,
                            "param" => [
                                "codigo_produto_integracao"     => "PA".$produto->id,
                                "quantidade_estoque"        	=> ($produto_global_codigo->quantidade_estoque + $produtos_codigo->quantidade_estoque), 
                            ]
                        ];
                        $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                        if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                            echo " - Erro Alterar (SP)";
			    print_r($response);
                            fwrite($arquivo_log, " - Produto PA NÃO ALTERADO OMIE");
                        }
                        else{
                            echo " - OK Alterar (SP)";
                            fwrite($arquivo_log, " - Produto PA ALTERADO OMIE");
                        }
                        
                        $body = [
                            "call" => "ExcluirProduto",
                            "app_key" => static::APP_KEY_OMIE_SP,
                            "app_secret" => static::APP_SECRET_OMIE_SP,
                            "param" => [
                                "codigo_produto"	=> $produto_global_codigo->codigo_produto,
                            ]
                         ];
                        $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                        
                        if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                            echo " - Erro Exluir (SP)";
			    print_r($response);
                            fwrite($arquivo_log, " - Produto NÃO EXCLUIDO OMIE");
                        }
                        else{
                            echo " - OK Excluir (SP)";
                            fwrite($arquivo_log, " - Produto EXCLUIDO OMIE");
                        }
                    }
                    else{
                        echo " - Produto NÃO encontrado pelo código PA no OMIE";
                        fwrite($arquivo_log, " - Produto NÃO encontrado pelo código PA no OMIE");
                        
                        $body = [
                            "call" => "AlterarProduto",
                            "app_key" => static::APP_KEY_OMIE_SP,
                            "app_secret" => static::APP_SECRET_OMIE_SP,
                            "param" => [
                                "codigo_produto"            => $produto_global_codigo->codigo_produto,
                                "codigo_produto_integracao" => "PA".$produto->id,
                                "codigo"                    => "PA".$produto->id,
                                
                            ]
                        ];
                        $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                        if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                            echo " - Erro Alterar (SP)";
			    print_r($response);
                            fwrite($arquivo_log, " - Produto NÃO ALTERADO para o CODIGO_PA no OMIE");
                        }
                        else{
                            echo " - OK Alterar (SP)";
                            fwrite($arquivo_log, " - Produto ALTERADO para o CODIGO_PA no OMIE");
                        }
                    }
                }
                else{
                    $produto = Produto::find()->andWhere(['=','codigo_fabricante',$linhaArray[0]])->one();
                    
                    if($produto){
                        //echo 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_SP.'","app_secret":"'.static::APP_SECRET_OMIE_SP.'","param":[{"codigo":"PA'.$produto->id.'"}]}';
                        echo " - Produto encontrado NO PEÇA, pelo CODIGO_FABRICANTE";
                        fwrite($arquivo_log, " - Produto encontrado NO PEÇA, pelo CODIGO_FABRICANTE");
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_SP.'","app_secret":"'.static::APP_SECRET_OMIE_SP.'","param":[{"codigo":"PA'.$produto->id.'"}]}');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $produtos = curl_exec($ch);
                        $produtos_codigo = json_decode($produtos);
                        curl_close($ch);
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_SP.'","app_secret":"'.static::APP_SECRET_OMIE_SP.'","param":[{"codigo":"'.$linhaArray[0].'"}]}');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $produto_global = curl_exec($ch);
                        $produto_global_codigo = json_decode($produto_global);
                        curl_close($ch);
                     
                        if(!isset($produto_global_codigo->codigo_produto)){
                            echo " - Produto JÁ EXCLUÍDO no OMIE";
                            fwrite($arquivo_log, " - Produto JÁ EXCLUÍDO no OMIE");
			    continue;
                        }
                        
                        if(isset($produtos_codigo->codigo_produto)){
                            
                            echo " - Produto encontrado pelo código PA no Omie";
                            fwrite($arquivo_log, " - Produto encontrado pelo código PA");
                            
                            $body = [
                                "call" => "AlterarProduto",
                                "app_key" => static::APP_KEY_OMIE_SP,
                                "app_secret" => static::APP_SECRET_OMIE_SP,
                                "param" => [
                                    "codigo_produto_integracao" => "PA".$produto->id,
                                    "quantidade_estoque"       	=> ($produto_global_codigo->quantidade_estoque + $produtos_codigo->quantidade_estoque),
                                ]
                            ];
                            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                            if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                                echo " - Erro Alterar (SP)";
				print_r($response);
                                fwrite($arquivo_log, " - Produto NÃO ALTERADO OMIE");
                            }
                            else{
                                echo " - OK Alterar (SP)";
                                fwrite($arquivo_log, " - Produto ALTERADO OMIE");
                            }
                            
                            $body = [
                                "call" => "ExcluirProduto",
                                "app_key" => static::APP_KEY_OMIE_SP,
                                "app_secret" => static::APP_SECRET_OMIE_SP,
                                "param" => [
				    "codigo_produto"        => $produto_global_codigo->codigo_produto,
                                ]
                            ];
                            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                            if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                                echo " - Erro Exluir (SP)";
				print_r($response);
                                fwrite($arquivo_log, " - Produto NÃO EXCLUIDO OMIE");
                            }
                            else{
                                echo " - OK Excluir (SP)";
                                fwrite($arquivo_log, " - Produto EXCLUIDO OMIE");
                            }
                        }
                        else{
                            echo " - Produto NÃO encontrado pelo código PA no OMIE";
                            fwrite($arquivo_log, " - Produto não encontrado");
                            
                            $body = [
                                "call" => "AlterarProduto",
                                "app_key" => static::APP_KEY_OMIE_SP,
                                "app_secret" => static::APP_SECRET_OMIE_SP,
                                "param" => [
                                    "codigo_produto"            => $produto_global_codigo->codigo_produto,
                                    "codigo_produto_integracao" => "PA".$produto->id,
                                    "codigo"                    => "PA".$produto->id,
                                    
                                ]
                            ];
                            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                            if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                                echo " - Erro Alterado, CODIGO_FABRICANTE (SP)";
                                fwrite($arquivo_log, " - Produto NÃO EXCLUIDO OMIE");
                            }
                            else{
                                echo " - OK Alterado, CODIGO_FABRICANTE  (SP)";
                                fwrite($arquivo_log, " - Produto EXCLUIDO OMIE");
                            }
                        }
                    }
                    else{
                        echo " - Produto não encontrado NO PEÇA, por CODIGO_FABRICANTE";
                        fwrite($arquivo_log, ";Produto não encontrado NO PEÇA, por CODIGO_FABRICANTE");
                    }
                }
            } else {
                echo " - Produto COM código PA no OMIE";
                fwrite($arquivo_log, ";Produto com código PA no OMIE");
            }
        }
        
        fclose($arquivo_log);
    }
}





