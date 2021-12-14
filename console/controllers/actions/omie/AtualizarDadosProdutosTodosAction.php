<?php

namespace console\controllers\actions\omie;

use common\models\Produto;
use common\models\ValorProdutoFilial;
use console\controllers\actions\omie\Omie;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_decode;
use common\models\ProdutoFilial;


class AtualizarDadosProdutosTodosAction extends Action
{
    public function run($codigo_global)
    {
       
        echo "Criando produtos...\n\n";
        $criar_omie = new Omie(1, 1);
        
        $APP_KEY_OMIE_MG                    = '469728530271';
        $APP_SECRET_OMIE_MG                 = '6b63421c9bb3a124e012a6bb75ef4ace';
        
        $APP_KEY_OMIE_SP                    = '468080198586';
        $APP_SECRET_OMIE_SP                 = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_DUPLICADA       = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA    = '78ba33370fac6178da52d42240591291';

        echo "\n entrou \n";
        
        $produtos = Produto::find()//->andWhere(['=','id',334923])
                                   //->andWhere(['codigo_global' => $produtos_corrigir])
                                   //->andWhere(['codigo_fabricante' => $produtos_corrigir])
                                   ->orderBy(["id" => SORT_ASC])
                                   ->all();
        
        if (file_exists("/var/tmp/log_omie_altera_produto_todos.csv")){
            unlink("/var/tmp/log_omie_altera_produto_todos.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_omie_altera_produto_todos".date("Y-m-d_H-i-s").".csv", "a");
        //Escreve no log
        fwrite($arquivo_log, "produto_id;http_code;status_omie\n");
 
        foreach ($produtos as $k => $produto) {
            
            echo "\n".$k." - ".$produto->id;
            //continue;
            
            if($k <= 57040){
                echo " - pular";
                continue;
            }
            
            $descricao = str_replace('"',"''",substr("".$produto->codigo_global." ".$produto->nome,0,100));

            $produto_filial = ProdutoFilial::find() ->andWhere(["=", "produto_id", $produto->id])
                                                    ->andWhere(["<>", "filial_id", 98])
                                                    ->one();

            $valor = 0;                                                    
            if($produto_filial){
                
                $valor_produto_filial = ValorProdutoFilial::find()  ->andWhere(["=", "produto_filial_id", $produto_filial->id])
                                                                    ->orderBy(["dt_inicio" => SORT_DESC])
                                                                    ->one();
                if($valor_produto_filial){
                    $valor = $valor_produto_filial->valor;    
                }
            }
            
            echo " - ".$descricao." - ".$valor_produto_filial->valor;
            //continue;
               
            $body = [
                "call" => "AlterarProduto",
                "app_key" => $APP_KEY_OMIE_SP,
                "app_secret" => $APP_SECRET_OMIE_SP,
                "param" => [
                    "codigo_produto_integracao" => "PA".$produto->id,
                    "codigo"                    => "PA".$produto->id,
                    "descricao"                 => str_replace(" ","%20",$descricao),
                    "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                    "valor_unitario"            => round($valor,2),
                    "unidade"                   => "PC",
                    "tipoItem"                  => "99",
                    "peso_liq"                  => round($produto->peso,2),
                    "peso_bruto"                => round($produto->peso,2),
                    "altura"                    => round($produto->altura,2),
                    "largura"                   => round($produto->largura,2),
                    "profundidade"              => round($produto->profundidade,2),
                    "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
                    "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                ]
            ];
            //echo "\n"; print_r($body); echo "\n";
            $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            //echo "\n"; print_r($response); echo "\n";die;
            
            if (ArrayHelper::getValue($response, 'httpCode') == 200){
                echo  " - Produto alterado(Omie Principal)";
                fwrite($arquivo_log, "(Omie Principal)".$produto->id.";".ArrayHelper::getValue($response, 'httpCode').";"."Produto Alterado com Sucesso!"."\n");
            } else {
                echo  " - Produto não alterado(Omie Principal) - ";
                echo ArrayHelper::getValue($response, 'body.faultstring');
                fwrite($arquivo_log, "(Omie Principal)".$produto->id.";".ArrayHelper::getValue($response, 'httpCode').";".ArrayHelper::getValue($response, 'body.faultstring')."\n");
            }       
            
            $body = [
                "call" => "AlterarProduto",
                "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                "param" => [
                    "codigo_produto_integracao" => "PA".$produto->id,
                    "codigo"                    => "PA".$produto->id,
                    "descricao"                 => str_replace(" ","%20",$descricao),
                    "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                    "valor_unitario"            => round($valor,2),
                    "unidade"                   => "PC",
                    "tipoItem"                  => "99",
                    "peso_liq"                  => round($produto->peso,2),
                    "peso_bruto"                => round($produto->peso,2),
                    "altura"                    => round($produto->altura,2),
                    "largura"                   => round($produto->largura,2),
                    "profundidade"              => round($produto->profundidade,2),
                    "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
                    "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                ]
            ];
            //echo "\n"; print_r($body); echo "\n";
            $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            //echo "\n"; print_r($response); echo "\n";die;
            
            if (ArrayHelper::getValue($response, 'httpCode') == 200){
                echo  " - Produto alterado(Omie Conta Duplicada)";
                fwrite($arquivo_log, "(Omie Conta Duplicada)".$produto->id.";".ArrayHelper::getValue($response, 'httpCode').";"."Produto Alterado com Sucesso!"."\n");
            } else {
                echo  " - Produto não alterado(Omie Conta Duplicada) - ";
                echo ArrayHelper::getValue($response, 'body.faultstring');
                fwrite($arquivo_log, "(Omie Conta Duplicada)".$produto->id.";".ArrayHelper::getValue($response, 'httpCode').";".ArrayHelper::getValue($response, 'body.faultstring')."\n");
            }    
            
            $body = [
                "call" => "AlterarProduto",
                "app_key" => $APP_KEY_OMIE_MG,
                "app_secret" => $APP_SECRET_OMIE_MG,
                "param" => [
                    "codigo_produto_integracao" => "PA".$produto->id,
                    "codigo"                    => "PA".$produto->id,
                    "descricao"                 => str_replace(" ","%20",$descricao),
                    "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                    "valor_unitario"            => round($valor,2),
                    "unidade"                   => "PC",
                    "tipoItem"                  => "99",
                    "peso_liq"                  => round($produto->peso,2),
                    "peso_bruto"                => round($produto->peso,2),
                    "altura"                    => round($produto->altura,2),
                    "largura"                   => round($produto->largura,2),
                    "profundidade"              => round($produto->profundidade,2),
                    "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
                    "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                ]
            ];
            //echo "\n"; print_r($body); echo "\n";
            $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            //echo "\n"; print_r($response); echo "\n";die;
            
            if (ArrayHelper::getValue($response, 'httpCode') == 200){
                echo  " - Produto alterado(Omie MG)";
                fwrite($arquivo_log, "(Omie MG)".$produto->id.";".ArrayHelper::getValue($response, 'httpCode').";"."Produto Alterado com Sucesso!"."\n");
            } else {
                echo  " - Produto não alterado(Omie MG) - ";
                echo ArrayHelper::getValue($response, 'body.faultstring');
                fwrite($arquivo_log, "(Omie MG)".$produto->id.";".ArrayHelper::getValue($response, 'httpCode').";".ArrayHelper::getValue($response, 'body.faultstring')."\n");
            }    
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); 
    }
}



