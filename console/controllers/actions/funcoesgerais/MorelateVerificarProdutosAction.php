<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;

class MorelateVerificarProdutosAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/morelate_comparacao_mudanca_03-08-2020.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $log = "/var/tmp/log_morelate_comparacao_mudanca_03-08-2020.csv";
        if (file_exists($log)){
            unlink($log);
        }
        $arquivo_log = fopen($log, "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){
            
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8].'";"'.$linhaArray[9].'";"'.$linhaArray[10].'";"'.$linhaArray[11].'";"'.$linhaArray[12].'";"'.$linhaArray[13].'";"'.$linhaArray[14].'";"'.$linhaArray[15].'";"'.$linhaArray[16].'";"'.$linhaArray[17].'";"'.$linhaArray[18].'";"'.$linhaArray[19].'";"'.$linhaArray[20].'";"'.$linhaArray[21].'"');
            
            if ($i == 0){
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[3]." - ".$linhaArray[4];
                        
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[0].".M"])->one();
            
            if ($produto){
                if(substr($produto->nome, 0, 150) != substr($linhaArray[4],0,150) || $produto->aplicacao != $linhaArray[6]){
                    fwrite($arquivo_log, ';"Produto com alteração"');
                }
                else{
                    fwrite($arquivo_log, ';"Produto sem alteração"');
                }
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}







