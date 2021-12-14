<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AtualizarMultiplicadorAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do multiplicador: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/home/pecaagoradev/produto_br_cx_multiplicador.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_alterar_multiplicador.csv")){
            unlink("/var/tmp/log_alterar_multiplicador.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_alterar_multiplicador.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;multiplicador;status\n");
        
        foreach ($LinhasArray as &$linhaArray){
            $id = $linhaArray[0];
            
            if ($id == null or $id == "" or $id == "id"){
                // Escreve no log
                fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";Sem ID\n");
            }
            else {
                $produto = Produto::find()->andWhere(['=','id',$id])->one();

                if (isset($produto)){
                    echo "Produto encontrado para id - "; print_r($id);echo "\n";
                    
                    $produto->multiplicador = $linhaArray[1];
                    $produto->save();
                    
                    fwrite($arquivo_log, $produto->id.";".$linhaArray[1].";Multiplicador Atualizado\n");
                } 
                else {
                    // Escreve no log
                    fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";Produto não encontrado\n");
                }
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); 
        
        //print_r($LinhasArray);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}
