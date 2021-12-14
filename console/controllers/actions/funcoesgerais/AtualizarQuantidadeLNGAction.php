<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AtualizarQuantidadeLNGAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao da Quantidade LNG: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen('/var/tmp/LNG_quantidade_09-08-2019.csv', 'r');
	$file = fopen('/var/tmp/lng_atualizado_28-08-19.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        //print_r($LinhasArray);die;
        $arquivo_log = fopen("/var/tmp/log_alterar_quantidade_lng".date("Y-m-d_H-i-s").".csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;sku;quantidade;status\n");
        
        foreach ($LinhasArray as $k => &$linhaArray){
            
            if($k <= 1){
                continue;
            }
            
            echo "\n".$k." - ".$linhaArray[0];
            //continue;
            
            $id = $linhaArray[0];
            
            if ($id == null or $id == "" or $id == "id"){
                // Escreve no log
                fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";Sem ID\n");
            }
            else {
                //$codigo_fabricante  = "L".str_replace(" ","",str_replace("-","",$id));
		$codigo_fabricante  = $id;
                $produto_filial     = ProdutoFilial::find() ->joinWith('produto')
                                                            ->andWhere(['like','codigo_fabricante',$codigo_fabricante])
                                                            ->andWhere(['=','filial_id',60])
                                                            ->one();
                if ($produto_filial){
                    echo " - Produto encontrado para codigo_fabricante - "; print_r($codigo_fabricante);echo "\n";
                    
		    //if($produto_filial->filial_id == 60){
		 	$produto_filial->quantidade = $linhaArray[3];
			var_dump($produto_filial->save());
		    //}
		    //else{
		    //	$produto_filial->quantidade = $linhaArray[3];
		    //	$produto_filial->filial_id = 60;
                    //    var_dump($produto_filial->save());
		    //}

                    fwrite($arquivo_log, $produto_filial->id.";".$linhaArray[0].";".$linhaArray[3].";Quantidade Atualizada\n");
                }
                else {
                    echo " - Produto NÃO encontrado para codigo_fabricnate - "; 
		    print_r($codigo_fabricante);
		    echo "\n";
                    // Escreve no log
                    fwrite($arquivo_log, ";".$linhaArray[0].";".$linhaArray[3].";Produto não encontrado\n");
                }
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); 
        
        //print_r($LinhasArray);
        
        echo "\n\nFIM da rotina de atualizacao da Quantidade LNG!";
    }
}
