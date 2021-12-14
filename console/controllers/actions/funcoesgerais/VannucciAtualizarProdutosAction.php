<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class VannucciAtualizarProdutosAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen("/var/tmp/vannucci_pellegrino_10-01-2020_v2.csv", 'r'); //Abre arquivo com preços para subir
        //$file = fopen("/var/tmp/vannucci_pellegrino_28-01-2020.csv", 'r'); //Abre arquivo com preços para subir
        //$file = fopen("/var/tmp/vannucci_pellegrino_10-02-2020.csv", 'r'); //Abre arquivo com preços para subir
        //$file = fopen("/var/tmp/vannucci_pellegrino_06-05-2020.csv", 'r'); //Abre arquivo com preços para subir
        $file = fopen("/var/tmp/vannucci_pellegrino_nomes_25-05-2020.csv", 'r'); //Abre arquivo com preços para subirwhile (($line = fgetcsv($file,null,';')) !== false)
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_vannucci_pellegrino_15-05-2020.csv")){
            unlink("/var/tmp/log_vannucci_pellegrino_15-05-2020.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_vannucci_pellegrino_15-05-2020.csv", "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir

            //Coloca os dados da planilha de preços no log
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8].';'.$linhaArray[9]);
            
            if ($i <= 0){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                fwrite($arquivo_log, ";encontrado;status");
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[5]." - ".$linhaArray[6]." - ".$linhaArray[7]; //Exibe no console(Terminal) as informações dos preços durante o processamento
            
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[0]])
                                        ->andWhere(['=','fabricante_id', 91])
                                        ->one(); //Procura produto pelo código do fabricante "VA"
            
            $nome_limpo = substr($linhaArray[2], 0, 150);
            
            if ($produto){ //Se encontrar o produto, processa o preço
                
                echo " - encontrado FABRICANTE"; //Escreva no termina l
                fwrite($arquivo_log, ';Produto encontrado FABRICANTE'); //Escreve no Log que encontrou o produto
                
                $produto->nome              = $nome_limpo;
                $produto->aplicacao         = $linhaArray[3];
                $produto->codigo_similar    = $linhaArray[4];
                //$produto->subcategoria_id   = $linhaArray[5];
                $produto->peso              = $linhaArray[5];
                $produto->altura            = $linhaArray[6];
                $produto->largura           = $linhaArray[7];
                $produto->profundidade      = $linhaArray[8];
                if($produto->save()){
                    echo " - alterado"; //Escreva no termina l
                    fwrite($arquivo_log, ';Dados alterados'); //Escreve no Log que encontrou o produto
                }
                else{
                    echo " - não alterado"; //Escreva no termina l
                    fwrite($arquivo_log, ';Dados não alterados'); //Escreve no Log que encontrou o produto
                }                
            }
            else{
                $produto = Produto::find()  ->andWhere(['=','codigo_global', $linhaArray[1]])
                                            ->andWhere(['=','fabricante_id', 91])
                                            ->one(); //Procura produto pelo código do fabricante "VA"
                
                if ($produto){ //Se encontrar o produto, processa o preço
                    
                    echo " - encontrado GLOBAL"; //Escreva no termina l
                    fwrite($arquivo_log, ';Produto encontrado GLOBAL'); //Escreve no Log que encontrou o produto
                    
                    $produto->nome              = $nome_limpo;
                    $produto->aplicacao         = $linhaArray[3];
                    $produto->codigo_similar    = $linhaArray[4];
                    //$produto->subcategoria_id   = $linhaArray[5];
                    $produto->peso              = $linhaArray[5];
                    $produto->altura            = $linhaArray[6];
                    $produto->largura           = $linhaArray[7];
                    $produto->profundidade      = $linhaArray[8];
                    if($produto->save()){
                        echo " - alterado"; //Escreva no termina l
                        fwrite($arquivo_log, ';Dados alterados'); //Escreve no Log que encontrou o produto
                    }
                    else{
                        echo " - não alterado"; //Escreva no termina l
                        fwrite($arquivo_log, ';Dados não alterados'); //Escreve no Log que encontrou o produto
                    }
                }
                else{
                    echo " - Não encontrado";
                    fwrite($arquivo_log, ';Produto Não encontrado;');
                }
            }
        }
        
        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}







