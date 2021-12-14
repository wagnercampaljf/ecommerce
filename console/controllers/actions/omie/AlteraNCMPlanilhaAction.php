<?php

namespace console\controllers\actions\omie;

use common\models\Produto;
use console\controllers\actions\omie\Omie;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_decode;


class AlteraNCMPlanilhaAction extends Action
{
    public function run($id)
    {
       
        $APP_KEY_OMIE_SP              = '468080198586';
        $APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';
        
        echo "Criando produtos...\n\n";
        $criar_omie = new Omie(1, 1);

        echo "\n entrou \n";
        
        if (file_exists("/var/tmp/log_omie_thairine_ncm_17-08-2020.csv")){
            unlink("/var/tmp/log_omie_thairine_ncm_17-08-2020.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_omie_thairine_ncm_17-08-2020.csv", "a");
        
        $LinhasArray = Array();
        //$file = fopen("/var/tmp/omie_thairine_ncm_17-07-2020.csv", 'r');
	$file = fopen("/var/tmp/omie_thairine_ncm_17-08-2020.csv", 'r');
        
        $x=0;
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            //var_dump($line);
            echo "\n".$x." - ".$line[9];

	    if($x<=1682){
		$x++;
		continue;
	    }
            
            $produto_id = (int) str_replace("PA","",$line[9]);
            
            /*var_dump($produto_id);
            if($x <= 0){
                continue;
            }
            var_dump($produto_id);*/
            
            $produto = Produto::find()->andWhere(['=','id',$produto_id])->one();
            
            if($produto){
                echo " - produto encontrado";
                $produto->codigo_montadora = $line[12];
                if($produto->save()){
                    echo " - produto alterado PECA";
                }
                else{
                    echo " - produto não alterado, PECA";
                }
            }
            
            //PRODUTO OMIE PRINCIPAL";
            $body = [
                "call" => "ConsultarProduto",
                "app_key" => $APP_KEY_OMIE_SP,
                "app_secret" => $APP_SECRET_OMIE_SP,
                "param" => [
                    "codigo_produto_integracao" => $line[9],
                ]
            ];
            $response = $criar_omie->consulta_produto("api/v1/geral/produtos/?JSON=",$body);
            //echo "\n"; print_r($response); echo "\n"; die;
            
            $ncm = substr($line[12], 0, 4).".".substr($line[12], 4, 2).".".substr($line[12], 6, 2);
            
            if (ArrayHelper::getValue($response, 'httpCode') == 200){
                echo "- produto encontrado no Omie(Principal)";
                $body = [
                    "call" => "AlterarProduto",
                    "app_key" => '468080198586',
                    "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                    "param" => [
                        "codigo_produto"            => ArrayHelper::getValue($response, 'body.codigo_produto'),
                        //"descricao"                 => "(".$produto->codigo_global.") ".$produto->nome,
                        "ncm"                       => $ncm,
                    ]
                ];
                //echo "\n"; print_r($body); echo "\n";
                //echo "\n\n111\n\n";
                $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                //echo "\n\n333\n\n";
                //echo "\n"; print_r($response); echo "\n";
                
                if (ArrayHelper::getValue($response, 'httpCode') == 200){
                    echo " - produto alterado(Principal)";
                    fwrite($arquivo_log, $line[9].";".$line[12].";".ArrayHelper::getValue($response, 'httpCode').";"."Produto Alterado com Sucesso!"."\n");
                } else {
                    echo " - produto NÃO alterado(Principal)";
                    fwrite($arquivo_log, $line[9].";".$line[12].";".ArrayHelper::getValue($response, 'httpCode').";".ArrayHelper::getValue($response, 'body.faultstring')."\n");
                }
            } else{
                echo " - produto NÃO encontrado(Principal)";
            }
            
            //PRODUTO OMIE SECUNDARIO";
            $body = [
                "call" => "ConsultarProduto",
                "app_key" => '1017311982687',
                "app_secret" => '78ba33370fac6178da52d42240591291',
                "param" => [
                    "codigo_produto_integracao" => $line[9],
                ]
            ];
            $response = $criar_omie->consulta_produto("api/v1/geral/produtos/?JSON=",$body);
            //echo "\n"; print_r($response); echo "\n"; die;
            
            $ncm = substr($line[12], 0, 4).".".substr($line[12], 4, 2).".".substr($line[12], 6, 2);
            
            if (ArrayHelper::getValue($response, 'httpCode') == 200){
                echo " - produto encontrado(Conta Duplicada)";
                $body = [
                    "call" => "AlterarProduto",
                    "app_key" => '1017311982687',
                    "app_secret" => '78ba33370fac6178da52d42240591291',
                    "param" => [
                        "codigo_produto"            => ArrayHelper::getValue($response, 'body.codigo_produto'),
                        //"descricao"                 => "(".$produto->codigo_global.") ".$produto->nome,
                        "ncm"                       => $ncm,
                    ]
                ];
                //echo "\n"; print_r($body); echo "\n";
                //echo "\n\n111\n\n";
                $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                //echo "\n\n333\n\n";
                //echo "\n"; print_r($response); echo "\n";
                
                if (ArrayHelper::getValue($response, 'httpCode') == 200){
                    echo " - produto alterado(Conta Duplicada)";
                    fwrite($arquivo_log, $line[9].";".$line[12].";".ArrayHelper::getValue($response, 'httpCode').";"."Produto Alterado com Sucesso!"."\n");
                } else {
                    echo " - produto NÃO alterado(Conta Duplicada)";
                    fwrite($arquivo_log, $line[9].";".$line[12].";".ArrayHelper::getValue($response, 'httpCode').";".ArrayHelper::getValue($response, 'body.faultstring')."\n");
                }
            } else{
                echo " - produto NÃO encontrado(Conta Duplicada)";
            }
            
            $x++;
            //if($x>=2){
            //    die;
            //}
        }
        fclose($file);
                
        fclose($arquivo_log); 
    }
}



