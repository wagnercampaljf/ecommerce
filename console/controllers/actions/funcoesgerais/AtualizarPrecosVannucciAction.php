<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class AtualizarPrecosVannucciAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen("/var/tmp/lista_completa_vannucci_16-09-2019_precificado_venda.csv", 'r'); //Abre arquivo com preços para subir
        //$file = fopen("/var/tmp/vannucci_19-11-2019_precificado_venda.csv", 'r'); //Abre arquivo com preços para subir
	$file = fopen("/var/tmp/vannucci_preco_18-12-2019_precificado_venda.csv", 'r'); //Abre arquivo com preços para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
	if (file_exists("/var/tmp/log_vannucci_preco_18-12-2019_precificado_venda.csv")){
            unlink("/var/tmp/log_vannucci_preco_18-12-2019_precificado_venda.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_vannucci_preco_18-12-2019_precificado_venda.csv", "a");
        //Escreve no log
        //fwrite($arquivo_log, "coidgo_fabricante;NCM;codigo_global;valor;valor_compra;valor_venda;produto_filial_id;status_produto;status_estoque;status_preco\n");
        
        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir

            //Coloca os dados da planilha de preços no log
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7]);
            
            if ($i <= 32140){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[5]." - ".$linhaArray[6]." - ".$linhaArray[7]; //Exibe no console(Terminal) as informações dos preços durante o processamento
            
            $produto = Produto::find()->andWhere(['=','codigo_fabricante', $linhaArray[0]])->one(); //Procura produto pelo código do fabricante "VA"
            
            if ($produto){ //Se encontrar o produto, processa o preço
                
                echo " - encontrado"; //Escreva no termina l
                fwrite($arquivo_log, ';Produto encontrado'); //Escreve no Log que encontrou o produto
                
                $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',38])
                                                        ->andWhere(['=', 'produto_id', $produto->id])
                                                        ->one(); //Procura o estoque do produto na loja Vannucci
                if ($produtoFilial) {//Se encontrar estoque, processa
                    echo " - ".$produtoFilial->id; //Mostra o id do estoque no terminal
                    fwrite($arquivo_log, ';Estoque encontrado'); //Escreve no log que encontrou o estoque
                    
                    /*$quantidade = $linhaArray[6];
                    if($linhaArray[3] == "0,00"){
                        $quantidade = 0;
                    }
                    $produtoFilial->quantidade = $quantidade;
                    if($produtoFilial->save()){
                        echo " - quantidade atualizada";
                    }
                    else{
                        echo " - quantidade não atualizada";
                    }*/
                    
                    $preco_venda = $linhaArray[7];
                    $preco_compra = $linhaArray[6];
		    $valor_produto_filial = new ValorProdutoFilial;
                    $valor_produto_filial->produto_filial_id    = $produtoFilial->id;
                    $valor_produto_filial->valor                = $preco_venda;
                    $valor_produto_filial->valor_cnpj           = $preco_venda;
                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                    $valor_produto_filial->promocao             = false;
		    $valor_produto_filial->valor_compra         = $preco_compra;
                    if($valor_produto_filial->save()){
                        echo " - Preço atualizado";
                        fwrite($arquivo_log, ';Preço encontrado');
                    }
                    else{
                        echo " - Preço não atualizado";
                        fwrite($arquivo_log, ';Preço Não encontrado');
                    }
                }
                else{
                    echo " - Estoque não encontrado";
                    fwrite($arquivo_log, ';Estoque Não encontrado');
                }
            }
            else{
                echo " - Não encontrado";
                fwrite($arquivo_log, ';Produto Não encontrado');
            }
        }
        
        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}







