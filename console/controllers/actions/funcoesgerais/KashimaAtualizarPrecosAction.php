<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use common\models\MarcaProduto;

class KashimaAtualizarPrecosAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
    	//$file = fopen("/var/tmp/KITS_EMBREAGEM_KASHIMA_27-04-2020_precificado.csv", 'r');

        $file = fopen("/var/tmp/produtos_Pea_Agora_MA-19-05-2021.csv", 'r');

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; 
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_produtos_Pea_Agora_MA-19-05-2021.csv")){
            unlink("/var/tmp/log_produtos_Pea_Agora_MA-19-05-2021.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_produtos_Pea_Agora_MA-19-05-2021.csv", "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir

            //Coloca os dados da planilha de preços no log
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8].";");
            
            if ($i <= 1){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                fwrite($arquivo_log, "status");
                //continue;
            }
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[2]." - ".$linhaArray[6]; //Exibe no console(Terminal) as informações dos preços durante o processamento

            $produto = Produto::find()  ->andWhere(['=','codigo_global', $linhaArray[0]])
                                        //->andWhere(['=','fabricante_id', 52])
                                        ->one(); //Procura produto pelo código do fabricante "VA"
            
            if ($produto){ //Se encontrar o produto, processa o preço
                
                echo " - encontrado"; //Escreva no termina l
                fwrite($arquivo_log, 'Produto encontrado');
                //continue;
                
                $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',70])
                                                        ->andWhere(['=', 'produto_id', $produto->id])
                                                        ->one();
                if ($produtoFilial) {
                    echo " - ".$produtoFilial->id; 
                    fwrite($arquivo_log, ' - Estoque encontrado');
                    //continue;
                    
                    $preco_venda = $linhaArray[6];
                    $preco_compra = $linhaArray[7];
                    
       		    $quantidade = 0;
                    echo " - Quantidade: ".$quantidade;
                    $produtoFilial->quantidade = $quantidade;
                    if($produtoFilial->save()){
                        echo " - quantidade atualizada";
                        fwrite($arquivo_log, " - quantidade atualizada");
                    }
                    else{
                        echo " - quantidade não atualizada";
                        fwrite($arquivo_log, " - quantidade não atualizada");
                    }
                    
                    /*$valor_produto_filial = new ValorProdutoFilial;
                    $valor_produto_filial->produto_filial_id    = $produtoFilial->id;
                    $valor_produto_filial->valor                = $preco_venda;
                    $valor_produto_filial->valor_cnpj           = $preco_venda;
                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                    $valor_produto_filial->promocao             = false;
                    $valor_produto_filial->valor_compra         = $preco_compra;
                    if($valor_produto_filial->save()){
                        echo " - Preço atualizado";
                        fwrite($arquivo_log, ' - Preço atualizado');
                    }
                    else{
                        echo " - Preço não atualizado";
                        fwrite($arquivo_log, ' - Preço Não atualizado');
                    }*/
                }
                else{
                    echo " - Estoque não encontrado";
                    fwrite($arquivo_log, ' - Estoque Não encontrado');
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









