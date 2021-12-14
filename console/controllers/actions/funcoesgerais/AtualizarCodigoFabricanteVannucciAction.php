<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AtualizarCodigoFabricanteVannucciAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do multiplicador: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/vannucci_corrigir_codigo_fabricante.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $LinhasArrayVannucciAntiga = Array();
        $file = fopen('/var/tmp/Lista_Completa_Vannucci_2018.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArrayVannucciAntiga[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_vannucci_corrigir_codigo_fabricante.csv")){
            unlink("/var/tmp/log_vannucci_corrigir_codigo_fabricante.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_vannucci_corrigir_codigo_fabricante.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;codigo_fabricante_antigo;codigo_fabricante_novo;status_codigo_fabricante;codigo_global_antigo;codigo_global_novo;status_codigo_global\n");
        
        foreach ($LinhasArray as $k => &$linhaArray){
            echo "\n".$k." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2];
            
            $status_codigo_fabricante   = ";;produto não encontrado";
            $status_codigo_global       = ";;produto não encontrado";
            $codigo_fabricante_antiga   = "";
            $codigo_global_antiga       = "";
            
            foreach ($LinhasArrayVannucciAntiga as $k => &$LinhaArrayVannucciAntiga){
                $codigo_fabricante = explode("-", $LinhaArrayVannucciAntiga[0]);
                if($linhaArray[1] == $codigo_fabricante[0]){
                    echo " - produto encontrado";
                    $status_codigo_fabricante   = "Produto encontrado";
                    $codigo_fabricante_antiga   = $LinhaArrayVannucciAntiga[0];
                    $codigo_global_antiga       = $LinhaArrayVannucciAntiga[1];
                    
                    if(($linhaArray[2] == $LinhaArrayVannucciAntiga[1]) || (str_replace(",","",str_replace(".","",$linhaArray[2])) == str_replace(",","",str_replace(".","",str_replace("-","",$LinhaArrayVannucciAntiga[1]))))){
                        echo "Mesmo código global";
                        $status_codigo_global = "Mesmo codigo_global";
                        
                        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id',$linhaArray[0]])->one();
                        if($produto_filial){
                            echo " - Produto Filial encontrado";
                            
                            $produto    = Produto::find()->andWhere(['=', 'id', $produto_filial->produto_id])->one();
                            if($produto){
                                echo " - Produto encontrado";

                                if($produto->codigo_fabricante == $linhaArray[1]){
					echo " - PRODUTO IGUAL DO PRODUTO FILIAL";
					$produto->codigo_fabricante = $LinhaArrayVannucciAntiga[0];
					var_dump($produto->save());
				}
				else{
					echo " - PRODUTO DIFERENTE DO PRODUTO FILIAL";
				}
                            }
                            else{
                                echo " - Produto não encontrado";
                            }
                        }
                        else{
                            echo " - Produto Filial não encontrado";
                        }
                    }
                    else{
                        $status_codigo_global = "Diferentes codigo_global";
                    }
                    
                    break;
                }
                else{
                    $status_codigo_fabricante = "Produto não encontrado";
                }
            }
            
            fwrite($arquivo_log, "produto_id;".$linhaArray[1].";".$codigo_fabricante_antiga.";".$status_codigo_fabricante.";".$linhaArray[2].";".$codigo_global_antiga.";".$status_codigo_global."\n");
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); 
        
        //print_r($LinhasArray);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}
