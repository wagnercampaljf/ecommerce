<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class ImportarIPIAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $linhas_dados = Array();
        $file = fopen('/var/tmp/morelate_ipi_16-09-2020.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $linhas_dados[] = $line;
        }
        fclose($file);
        
        $arquivo_log = fopen("/var/tmp/log_morelate_ipi_16-09-2020.csv", "a");
        
        foreach ($linhas_dados as $i => &$linha_dados ){
            echo "\n".$i." - ".$linha_dados[0];

            fwrite($arquivo_log, $linha_dados[0].";".$linha_dados[1].";".$linha_dados[2].";".$linha_dados[3].";".$linha_dados[4].";".$linha_dados[5].";".$linha_dados[6]);
            
            $produto = Produto::find()->andWhere(['=', 'codigo_fabricante', $linha_dados[0].".M"])->one();
            
            if($produto){
                
                $ipi = (float) $linha_dados[4];
                echo " - ".$linha_dados[4]." - ".$ipi;
                
                $produto->ipi = $ipi;
                if($produto->save()){
                    echo " - IPI alterado";
                    fwrite($arquivo_log, ";IPI alterado");
                }
                else{
                    echo " - IPI não alterado";
                    fwrite($arquivo_log, ";IPI não alterado");
                }
            }
            else{
                echo " - Produto não encontrado";
                fwrite($arquivo_log, ";Produto não encontrado");
            }
            
            fwrite($arquivo_log, "\n");
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}







