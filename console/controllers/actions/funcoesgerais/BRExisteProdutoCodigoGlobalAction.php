<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class BRExisteProdutoCodigoGlobalAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/br_produtos_ausentes_04-02-2020.csv", 'r'); 
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; 
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_br_produtos_ausentes_04-02-2020.csv")){
            unlink("/var/tmp/log_br_produtos_ausentes_04-02-2020.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_br_produtos_ausentes_04-02-2020.csv", "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){

            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8].";");
            
            if ($i <= 1){
                fwrite($arquivo_log, "status_codigo_global;codigo PA;codigo_fabricante");
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[1]." - ".$linhaArray[0]." - ".$linhaArray[7]." - ".$linhaArray[8]; 
            
            $produto = Produto::find()  ->andWhere(['=','codigo_global', $linhaArray[0]])
                                        //->andWhere(['=','fabricante_id', 52])
                                        ->one(); 
            
            if ($produto){ //Se encontrar o produto, processa o preço
                echo " - encontrado"; //Escreva no termina l
                fwrite($arquivo_log, 'Produto encontrado;'.$produto->id.";".$produto->codigo_fabricante); //Escreve no Log que encontrou o produto
            }
            else{
                echo " - Não encontrado";
                fwrite($arquivo_log, 'Produto Não encontrado;;');
            }
        }
        
        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








