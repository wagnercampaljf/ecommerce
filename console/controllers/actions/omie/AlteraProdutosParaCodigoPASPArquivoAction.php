<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use common\models\Produto;

class AlteraProdutosParaCodigoPASPArquivoAction extends Action
{
    public function run()//$global_id)
    {
       
        $LinhasArray = Array();
        $file = fopen('/var/tmp/omie_sem_pa_07-01-2020.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_omie_sem_pa_07-01-2020.csv")){
            unlink("/var/tmp/log_omie_sem_pa_07-01-2020.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_omie_sem_pa_07-01-2020.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "codigo_global;nome;status_pecaagora;;status_omie\n");
        
        echo "Alterando produtos... São Paulo\n\n";
        $meli = new Omie(static::APP_ID, static::SECRET_KEY);
        
        //Inicio teste Fred
            /*$ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_SP.'","app_secret":"'.static::APP_SECRET_OMIE_SP.'","param":[{"codigo":"FTS161008LL"}]}');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $produtos = curl_exec($ch);
            $produtos_codigo = json_decode($produtos);
            curl_close($ch);
            print_r($produtos_codigo); 
            die;*/
        //Fim teste Fred
        
        foreach ($LinhasArray as $k => $linhaArray) {

            echo "\n".$k." - ".$linhaArray[0];
            fwrite($arquivo_log, "\n".$linhaArray[0].";".$linhaArray[1]);
            
            $produto = Produto::find() ->andWhere(["=","codigo_global",$linhaArray[0]])
                                        ->one();
            
            if($produto){
                echo " - ".$produto->id." - ".$produto->codigo_global." - codigo_global";
                fwrite($arquivo_log, ";Produto encontrado no Peça Agora - codigo_global");
            }
            else{
                $produto = Produto::find()  ->andWhere(["=","codigo_fabricante",$linhaArray[0]])
                                            ->one();
                if($produto){
                    echo " - ".$produto->id." - ".$produto->codigo_fabricante." - codigo_fabricante";
                    fwrite($arquivo_log, ";Produto encontrado no Peça Agora - codigo_fabricante");
                }
                else{
                    $produto = Produto::find()  ->andWhere(["=","codigo_fabricante","D".$linhaArray[0]])
                                                ->one();
                    if($produto){
                        echo " - ".$produto->id." - ".$produto->codigo_fabricante." - codigo_fabricante D";
                        fwrite($arquivo_log, ";Produto encontrado no Peça Agora - codigo_fabricante D");
                    }
                    else{
                        echo " - Produto não encontrado no Peça Agora";
                        fwrite($arquivo_log, ";Produto não encontrado no Peça Agora;");
                        continue;
                    }
                }
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_SP.'","app_secret":"'.static::APP_SECRET_OMIE_SP.'","param":[{"codigo":"'.$linhaArray[0].'"}]}');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $produtos = curl_exec($ch);
            $produtos_codigo = json_decode($produtos);
            curl_close($ch);
            //print_r($produtos_codigo); 
            
            $codigo_produto = '';
            
            if(isset($produtos_codigo->codigo_produto)){
                echo " - Produto encontrado";
                fwrite($arquivo_log, ";Produto encontrado");
                $codigo_produto = $produtos_codigo->codigo_produto;
            }
            else{
                echo " - Produto não encontrado";
                fwrite($arquivo_log, ";Produto não encontrado");
                continue;
            }
            
            $body = [
                "call" => "AlterarProduto",
                "app_key" => static::APP_KEY_OMIE_SP,
                "app_secret" => static::APP_SECRET_OMIE_SP,
                "param" => [
                    "codigo_produto"            => $codigo_produto,
                    "codigo_produto_integracao" => "PA".$produto->id,
                    "codigo"                    => "PA".$produto->id,
		    "descricao"                 => "(".$produto->codigo_global.") ".$produto->nome,
                ]
            ];
            //print_r($body);
	    $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            //print_r($response);
            if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                echo " - Erro (SP)";
            }
            else{
                echo " - OK (SP)";
            }

	    //if($k >= 35){
            //    die;
            //}
        }
    }
}




