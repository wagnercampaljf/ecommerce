<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class LNGAtualizarProdutosAction extends Action
{
    public function run(){

        die;

        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/PRODUTOS_LNG_EDITADO_06-02-2020.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; 
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_PRODUTOS_LNG_EDITADO_06-02-2020.csv")){
            unlink("/var/tmp/log_PRODUTOS_LNG_EDITADO_06-02-2020.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_PRODUTOS_LNG_EDITADO_06-02-2020.csv", "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){ 

            //Coloca os dados da planilha de preços no log
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";".$linhaArray[14].";".$linhaArray[15].";".$linhaArray[16].";".$linhaArray[17].";".$linhaArray[18].";".$linhaArray[19].";");
            
            if ($i <= 0){ 
                fwrite($arquivo_log, "status");
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[1]." - ".$linhaArray[0]." - ".$linhaArray[7]." - ".$linhaArray[8]; //Exibe no console(Terminal) as informações dos preços durante o processamento
            
            $produto = Produto::find()  ->andWhere(['=','id', $linhaArray[0]])
                                        ->one();
            
            if ($produto){ //Se encontrar o produto, processa o preço
                
                echo " - encontrado"; //Escreva no termina l
                fwrite($arquivo_log, 'Produto encontrado'); //Escreve no Log que encontrou o produto
                
                $produto->nome                      = $linhaArray[1];
                $produto->subcategoria_id           = $linhaArray[2];
                $produto->codigo_fabricante         = $linhaArray[4];
                $produto->codigo_global             = $linhaArray[5];
                $produto->fabricante_id             = $linhaArray[6];
                $produto->codigo_montadora          = $linhaArray[8];
                $produto->peso                      = $linhaArray[9];
                $produto->altura                    = $linhaArray[10];
                $produto->largura                   = $linhaArray[11];
                $produto->profundidade              = $linhaArray[12];
                $produto->descricao                 = $linhaArray[13];
                $produto->aplicacao                 = $linhaArray[14];
                $produto->aplicacao_complementar    = $linhaArray[15];
                $produto->codigo_barras             = $linhaArray[17];
                $produto->cest                      = $linhaArray[18];
                $produto->ipi                       = $linhaArray[19];
                if($produto->save()){
                    echo " - produto atualizado";
                    fwrite($arquivo_log, " - produto atualizado");
                }
                else{
                    echo " - produto não atualizado";
                    fwrite($arquivo_log, " - produto não atualizado");
                }
            }
            else{
                echo " - Não encontrado";
                fwrite($arquivo_log, 'Produto Não encontrado');
            }
        }
        
        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}
