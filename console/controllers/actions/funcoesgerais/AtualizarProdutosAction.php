<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AtualizarProdutosAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do multiplicador: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/produtos_flexivel_escapamento_10-10-2019 ATUALIZADA.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_produtos_flexivel_escapamento_10-10-2019 ATUALIZADA.csv")){
            unlink("/var/tmp/log_produtos_flexivel_escapamento_10-10-2019 ATUALIZADA.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_produtos_flexivel_escapamento_10-10-2019 ATUALIZADA.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;multiplicador;status\n");
        
        foreach ($LinhasArray as $k => &$linhaArray){
            
            $id = $linhaArray[0];
            echo "\n".$k." - ".$id;
            
            if ($id == null or $id == "" or $id == "id"){
                // Escreve no log
                fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";Sem ID\n");
            }
            else {
                $produto = Produto::find()->andWhere(['=','id',$id])->one();

                if (isset($produto)){
                    echo "Produto encontrado para id - "; print_r($id);echo "\n";
                    
                    $produto->nome                      = $linhaArray[1];
                    $produto->peso                      = $linhaArray[2];
                    $produto->altura                    = $linhaArray[3];
                    $produto->largura                   = $linhaArray[4];
                    $produto->profundidade              = $linhaArray[5];
                    $produto->codigo_global             = $linhaArray[6];
                    $produto->codigo_similar            = $linhaArray[7];
                    $produto->codigo_montadora          = $linhaArray[8];
                    $produto->codigo_fabricante         = $linhaArray[9];
                    $produto->aplicacao                 = $linhaArray[10];
                    $produto->aplicacao_complementar    = $linhaArray[11];
                    if($produto->save()){
                        echo " - Produto Atualizado";
                        fwrite($arquivo_log, $produto->id.";".$linhaArray[1].";Produto Atualizado\n");
                    }
                    else{
                        echo " - Produto Não Atualizado";
                        fwrite($arquivo_log, $produto->id.";".$linhaArray[1].";Produto Não Atualizado\n");
                    }
                } 
                else {
                    echo " - Produto Não Encontrado";
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
