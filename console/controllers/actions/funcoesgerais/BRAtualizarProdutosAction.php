<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class BRAtualizarProdutosAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen("/var/tmp/br_produtos_ausentes_04-02-2020_completa -mari-NOMES_ATUALIZADOS.csv", 'r');
        $file = fopen("/var/tmp/br_ausente_completo_com_medidas_16-04-2020 ATUALIZADA.csv", 'r');
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_br_ausente_completo_com_medidas_16-04-2020 ATUALIZADA.csv")){
            unlink("/var/tmp/log_br_ausente_completo_com_medidas_16-04-2020 ATUALIZADA.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_br_ausente_completo_com_medidas_16-04-2020 ATUALIZADA.csv", "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir

            //Coloca os dados da planilha de preços no log
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8].'";"'.$linhaArray[9].'";"'.$linhaArray[10].'";"'.$linhaArray[11].'";"'.$linhaArray[12].'";"'.$linhaArray[13].'";"'.$linhaArray[14].'";"'.$linhaArray[15].'";"'.$linhaArray[16].'";"'.$linhaArray[17].'";"'.$linhaArray[18].'";"'.$linhaArray[19].'";"'.$linhaArray[20].'";"'.$linhaArray[21].'";"'.$linhaArray[22].'";"'.$linhaArray[23].'"');
            
            if ($i <= 0){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                fwrite($arquivo_log, ';"encontrado;status"');
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[1]." - ".$linhaArray[5]." - ".$linhaArray[6]." - ".$linhaArray[7]; //Exibe no console(Terminal) as informações dos preços durante o processamento
            
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[1].'.B'])
                                        ->andWhere(['=','fabricante_id', 52])
                                        ->one(); //Procura produto pelo código do fabricante "VA"
            
            $nome_limpo = substr($linhaArray[13], 0, 150);
            
            if ($produto){ //Se encontrar o produto, processa o preço
                
                echo " - encontrado FABRICANTE"; //Escreva no termina l
                fwrite($arquivo_log, ';Produto encontrado FABRICANTE'); //Escreve no Log que encontrou o produto
                
                $produto->nome                      = $nome_limpo;
                $produto->aplicacao                 = $linhaArray[14];
                //$produto->aplicacao_complementar    = $linhaArray[21];
                //$produto->codigo_similar            = $linhaArray[22];
                //$produto->multiplicador             = $linhaArray[22];
                //$produto->subcategoria_id           = $linhaArray[5];
                //$produto->peso                      = $linhaArray[6];
                //$produto->altura                    = $linhaArray[7];
                //$produto->largura                   = $linhaArray[8];
                //$produto->profundidade              = $linhaArray[9];
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
                /*$produto = Produto::find()  ->andWhere(['=','codigo_global', $linhaArray[1]])
                                            ->andWhere(['=','fabricante_id', 91])
                                            ->one(); //Procura produto pelo código do fabricante "VA"
                
                if ($produto){ //Se encontrar o produto, processa o preço
                    
                    echo " - encontrado GLOBAL"; //Escreva no termina l
                    fwrite($arquivo_log, ';Produto encontrado GLOBAL'); //Escreve no Log que encontrou o produto
                    
                    $produto->nome              = $nome_limpo;
                    $produto->aplicacao         = $linhaArray[3];
                    $produto->codigo_similar    = $linhaArray[4];
                    $produto->subcategoria_id   = $linhaArray[5];
                    $produto->peso              = $linhaArray[6];
                    $produto->altura            = $linhaArray[7];
                    $produto->largura           = $linhaArray[8];
                    $produto->profundidade      = $linhaArray[9];
                    if($produto->save()){
                        echo " - alterado"; //Escreva no termina l
                        fwrite($arquivo_log, ';Dados alterados'); //Escreve no Log que encontrou o produto
                    }
                    else{
                        echo " - não alterado"; //Escreva no termina l
                        fwrite($arquivo_log, ';Dados não alterados'); //Escreve no Log que encontrou o produto
                    }
                }
                else{*/
                echo " - Não encontrado";
                fwrite($arquivo_log, ';Produto Não encontrado;');
            }
        }
        
        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}







