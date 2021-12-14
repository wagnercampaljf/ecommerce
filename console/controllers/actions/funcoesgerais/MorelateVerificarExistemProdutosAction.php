<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class MorelateVerificarExistemProdutosAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/morelate_30-03-2020.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $log = "/var/tmp/log_morelate_30-03-2020.csv";
        if (file_exists($log)){
            unlink($log);
        }
        
        $arquivo_log = fopen($log, "a");
        // Escreve no log
        fwrite($arquivo_log,"Codigo_morelate;CODFABRICA;NCM;Original;Produto;Marca;Aplicação;Valor;Estoque;status;produto_id_encontrado");
        
        foreach ($LinhasArray as $i => &$linhaArray){
            
            if ($i == 0){
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[3]." - ".$linhaArray[4];
            //continue;
            
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8]);
            
            $produto = Produto::find()  ->andWhere(['like','codigo_global', $linhaArray[3]])
                                        ->one();
                                      
            if ($produto){
                echo " - Produto encontrado";
                fwrite($arquivo_log, '";"Produto encontrado";"'.$produto->id.'"');
            }
            else{
                echo " - Produto não encontrado";
                fwrite($arquivo_log, '";"Produto não encontrado";""');
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








