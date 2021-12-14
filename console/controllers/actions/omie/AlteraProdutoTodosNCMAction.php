<?php

namespace console\controllers\actions\omie;

use common\models\Produto;
use common\models\ValorProdutoFilial;
use console\controllers\actions\omie\Omie;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;


class AlteraProdutoTodosNCMAction extends Action
{
    public function run()
    {
       
        echo "Criando produtos...\n\n";
        $criar_omie = new Omie(1, 1);

        echo "\n entrou \n";
        
        $produtos = Produto::find()//->andWhere(['=','codigo_global',$codigo_global])
                                   ->all();
        
        if (file_exists("/var/tmp/log_omie_altera_produto_todos_ncm.csv")){
            unlink("/var/tmp/log_omie_altera_produto_todos_ncm.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_omie_altera_produto_todos_ncm.csv", "a");
        //Escreve no log
        fwrite($arquivo_log, "produto_id;http_code;status_omie\n");
 
        foreach ($produtos as $k => $produto) {
            
            echo $k." - "; print_r($produto->id);
                
            //echo "Alterando produtos...\n\n";
            $body = [
                "call" => "ConsultarProduto",
                "app_key" => '468080198586',
                "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                "param" => [
                    //"codigo_produto"            => "PA".$produto->id,
                    //"codigo_produto_integracao" => $produto->codigo_global,
                    "codigo"                    => "PA".$produto->id,
                ]
            ];
	    //print_r($body);
            $response = $criar_omie->consulta_produto("api/v1/geral/produtos/?JSON=",$body);
            //echo "\n"; print_r($response); echo "\n"; die;
            //var_dump(ArrayHelper::getValue($response, 'body.codigo_produto_integracao'));echo "\n";
            
            if (ArrayHelper::getValue($response, 'body.codigo_produto_integracao') != ""){
                $body = [
                    "call" => "AlterarProduto",
                    "app_key" => '468080198586',
                    "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                    "param" => [
                        "codigo_produto"            => ArrayHelper::getValue($response, 'body.codigo_produto'),
                        "codigo_produto_integracao" => ArrayHelper::getValue($response, 'body.codigo_produto_integracao'),
                        "codigo"                    => ArrayHelper::getValue($response, 'body.codigo'),
			"ncm"			    => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                    ]
                ];
                $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                //echo "\n"; print_r($response); echo "\n";die;
                
                if (ArrayHelper::getValue($response, 'httpCode') == 200){
                    fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";"."Produto Alterado com Sucesso!"."\n");
                    echo " - OK \n";
                } else {
                    fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";".ArrayHelper::getValue($response, 'body.faultstring')."\n");
                    echo " - ERROR \n";
                }             
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); 
    }
}




