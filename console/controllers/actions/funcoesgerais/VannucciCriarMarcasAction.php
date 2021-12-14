<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\MarcaProduto;

class VannucciCriarMarcasAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/MARCAS_VANNUCCI.csv", 'r'); //Abre arquivo com preços para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_MARCAS_VANNUCCI.csv")){
            unlink("/var/tmp/log_MARCAS_VANNUCCI.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_MARCAS_VANNUCCI.csv", "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir

            //Coloca os dados da planilha de preços no log
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'"');
            
            /*if ($i <= 0){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                fwrite($arquivo_log, "status");
                continue;
            }*/
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[1]; //Exibe no console(Terminal) as informações dos preços durante o processamento
            
            $marca_produto = MarcaProduto::find()   ->andWhere(['=','nome', $linhaArray[1]])
                                                    ->one(); //Procura produto pelo código do fabricante "VA"
            
            if (!$marca_produto){ //Se encontrar o produto, processa o preço
                
                echo " - Criar MarcaProduto"; //Escreva no termina l
                
                $marca_produto = new MarcaProduto;
                $marca_produto->nome = $linhaArray[1];
                if($marca_produto->save()){
                    echo " - CRIADO"; //Escreva no termina l
                    fwrite($arquivo_log, ';Marca Criada'); //Escreve no Log que encontrou o produto
                }
                else{
                    echo " - NÃO CRIADO"; //Escreva no termina l
                    fwrite($arquivo_log, ';Marca não criada'); //Escreve no Log que encontrou o produto
                }                
            }
            else{
                echo " - Marca já criada";
                fwrite($arquivo_log, ';Marca já criada;');
            }
        }
        
        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








