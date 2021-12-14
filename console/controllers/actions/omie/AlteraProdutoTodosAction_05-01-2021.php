<?php

namespace console\controllers\actions\omie;

use common\models\Produto;
use common\models\ValorProdutoFilial;
use console\controllers\actions\omie\Omie;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_decode;


class AlteraProdutoTodosAction extends Action
{
    public function run($codigo_global)
    {
       
        echo "Criando produtos...\n\n";
        $criar_omie = new Omie(1, 1);

        echo "\n entrou \n";
        
        $produtos = Produto::find()//->andWhere(['=','id',334923])
                                   ->all();
        
        if (file_exists("/var/tmp/log_omie_altera_produto_todos.csv")){
            unlink("/var/tmp/log_omie_altera_produto_todos.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_omie_altera_produto_todos".date("Y-m-d_H-i-s").".csv", "a");
        //Escreve no log
        fwrite($arquivo_log, "produto_id;http_code;status_omie\n");
 
        foreach ($produtos as $k => $produto) {
            
            echo "\n".$k." - ".$produto->id;
            
            if($k <= 4409){
                echo " - pular";continue;
            }
            
            //echo "Alterando produtos...\n\n";
            $body = [
                "call" => "ConsultarProduto",
                "app_key" => '468080198586',
                "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                "param" => [
                    //"codigo_produto"            => $produto->codigo_global,
                    "codigo_produto_integracao" => $produto->codigo_global,
                    //"codigo"                    => $produto->codigo_global,
                ]
            ];
            $response = $criar_omie->consulta_produto("api/v1/geral/produtos/?JSON=",$body);
            //echo "\n"; print_r($response); echo "\n"; die; 
            //var_dump(ArrayHelper::getValue($response, 'body.codigo_produto_integracao'));echo "\n";
            
            $descricao = str_replace('"',"''",substr("".$produto->codigo_global." ".$produto->nome,0,100));
            
            //if (ArrayHelper::getValue($response, 'body.codigo_produto_integracao') == ""){
            if (ArrayHelper::getValue($response, 'httpCode') == 200){
                
                echo " - Produto encontrado pelo Codigo Global, integração";
                
                $body = [
                    "call" => "AlterarProduto",
                    "app_key" => '468080198586',
                    "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                    "param" => [
                        "codigo_produto"            => ArrayHelper::getValue($response, 'body.codigo_produto'),
                        "codigo_produto_integracao" => "PA".$produto->id,
                        "codigo"                    => "PA".$produto->id,
                        "descricao"                 => str_replace(" ","%20",$descricao),
                        "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                    ]
                ];
                //echo "\n"; print_r($body); echo "\n";
                $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                //echo "\n"; print_r($response); echo "\n";die;
                
                if (ArrayHelper::getValue($response, 'httpCode') == 200){
                    echo  " - Produto alterado";
                    fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";"."Produto Alterado com Sucesso!"."\n");
                } else {
                    echo  " - Produto não alterado - ";
                    echo ArrayHelper::getValue($response, 'body.faultstring');
                    fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";".ArrayHelper::getValue($response, 'body.faultstring')."\n");
                }             
            } else{
                $body = [
                    "call" => "ConsultarProduto",
                    "app_key" => '468080198586',
                    "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                    "param" => [
                        //"codigo_produto"            => $produto->codigo_global,
                        //"codigo_produto_integracao" => $produto->codigo_global,
                        "codigo"                    => $produto->codigo_global,
                    ]
                ];
                $response = $criar_omie->consulta_produto("api/v1/geral/produtos/?JSON=",$body);
                //echo "\n"; print_r($response); echo "\n"; die;
                //var_dump(ArrayHelper::getValue($response, 'body.codigo_produto_integracao'));echo "\n";
                
                $descricao = str_replace('"',"''",substr("".$produto->codigo_global." ".$produto->nome,0,100));
                
                //if (ArrayHelper::getValue($response, 'body.codigo_produto_integracao') == ""){
                if (ArrayHelper::getValue($response, 'httpCode') == 200){
                    
                    echo " - Produto encontrado pelo Codigo Global, codigo";
                    
                    $body = [
                        "call" => "AlterarProduto",
                        "app_key" => '468080198586',
                        "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                        "param" => [
                            "codigo_produto"            => ArrayHelper::getValue($response, 'body.codigo_produto'),
                            "codigo_produto_integracao" => "PA".$produto->id,
                            "codigo"                    => "PA".$produto->id,
                            "descricao"                 => str_replace(" ","%20",$descricao),
                            "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                        ]
                    ];
                    //echo "\n"; print_r($body); echo "\n";
                    $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                    //echo "\n"; print_r($response); echo "\n";die;
                    
                    if (ArrayHelper::getValue($response, 'httpCode') == 200){
                        echo  " - Produto alterado";
                        fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";"."Produto Alterado com Sucesso!"."\n");
                    } else {
                        echo  " - Produto não alterado - ";
                        echo ArrayHelper::getValue($response, 'body.faultstring');
                        fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";".ArrayHelper::getValue($response, 'body.faultstring')."\n");
                    }
                } 
                else{
                    echo " - Produto pelo PA";
                    
                    $body = [
                        "call" => "AlterarProduto",
                        "app_key" => '468080198586',
                        "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                        "param" => [
                            "codigo_produto"            => ArrayHelper::getValue($response, 'body.codigo_produto'),
                            "codigo_produto_integracao" => "PA".$produto->id,
                            "codigo"                    => "PA".$produto->id,
                            "descricao"                 => str_replace(" ","%20",$descricao),
                            "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                        ]
                    ];
                    //echo "\n"; print_r($body); echo "\n";
                    $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                    
                    if (ArrayHelper::getValue($response, 'httpCode') == 200){
                        echo  " - Produto alterado";
                        fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";"."Produto Alterado com Sucesso!"."\n");
                    } else {
                        echo  " - Produto não alterado - ";
                        echo ArrayHelper::getValue($response, 'body.faultstring');
                        fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";".ArrayHelper::getValue($response, 'body.faultstring')."\n");
                    }
                    
                    //print_r($response);
                }
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); 
    }
}



