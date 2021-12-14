<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class BonfanteAtualizarPrecosAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();

        //$file = fopen("/var/tmp/bonfante_06-01-2021_precificado.csv", 'r');
        //  $file = fopen("/var/tmp/bonfante_14-01-2021_precificado.csv", 'r');
        //$file = fopen("/var/tmp/bonfante_01-02-2021-importados_precificado.csv", 'r');
        //$file = fopen("/var/tmp/bonfante_estoque_preco_01-02-2021_precificado.csv", 'r');
        //$file = fopen("/var/tmp/bonfante_estoque_preco_importados_01-03-2021_precificado.csv", 'r');
        //$file = fopen("/var/tmp/bonfante_estoque_preco_01-04-2021.2.csv", 'r');

        $file = fopen("/var/tmp/bonfante_estoque_preco_03-05-2021.1_precificado.csv", 'r');





        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_bonfante_estoque_preco_03-05-2021.1_precificado.csv")){
            unlink("/var/tmp/log_bonfante_estoque_preco_03-05-2021.1_precificado.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_bonfante_estoque_preco_03-05-2021_precificado.csv", "a");
       
        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir
            if ($i >= 0){

            //Coloca os dados da planilha de preços no log
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8]);
            
            if ($i <= 1){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                fwrite($arquivo_log, ";STATUS");
                continue;
            }
            
            echo "\n".$i." - " .$linhaArray[0]." - ".$linhaArray[5]." - ".$linhaArray[6]." - ".$linhaArray[7]; //Exibe no console(Terminal) as informações dos preços durante o processamento
            
            $produto = Produto::find()->andWhere(['=','codigo_fabricante', $linhaArray[0]])->one(); //Procura produto pelo código do fabricante "VA"
            
            if ($produto){ //Se encontrar o produto, processa o preço
                
                echo " - encontrado"; //Escreva no termina l
                fwrite($arquivo_log, ';Produto encontrado'); //Escreve no Log que encontrou o produto

                $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',86])
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


                    $preco_venda = $linhaArray[8];
                    $preco_compra = $linhaArray[7];

                   //$preco_venda = $linhaArray[9];  //produtos importados
                    //$preco_compra = $linhaArray[8]; //produtoss importados

                    /**/

                    //Verifica se o valor a ser adicionado É igual ao anterior, se for, nÃo adiciona o registro novo;
                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produtoFilial->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();
                    if ($preco_venda > $valor_produto_filial->valor * 3){
                        echo " - Preco mais alto que o normal";
                        fwrite($arquivo_log, ';Preço mais alto que o normal');
                        continue;

                    }elseif ($preco_venda < $valor_produto_filial->valor * 0.70){
                        echo " - Preco mais baixo que o normal";
                        fwrite($arquivo_log, ';Preço mais baixo que o normal');
                        continue;

                    }elseif ($preco_venda == $valor_produto_filial->valor){
                        echo " - mesmo valor";
                        fwrite($arquivo_log, ';mesmo valor');
                        continue;
                    }
                    else
                    {
                        echo " - Preco normal";

                    }



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
        }
        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}

