<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class GaussAtualizaPrecoVendaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do preço: \n\n";


        $LinhasArray = Array();


        //$file = fopen("/var/tmp/gauss_preco_estoque_11-05-2021_precificado.csv", 'r');
        //$file = fopen("/var/tmp/produtos_gauss_2021-06-01_precificado.csv", 'r');

        $file = fopen("/var/tmp/produtos_gauss_2021-06-09_precificado.csv", 'r');





        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_produtos_gauss_2021-06-09_precificado.csv")){
            unlink("/var/tmp/log_produtos_gauss_2021-06-09_precificado.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_produtos_gauss_2021-06-09_precificado.csv", "a");

        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as informações de preços a subir

            if ($i <= 1){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                fwrite($arquivo_log, "status");
                continue;
            }

            fwrite($arquivo_log, "\n".$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";".$linhaArray[14].";".$linhaArray[15].";".$linhaArray[16].";".$linhaArray[17].";".$linhaArray[18].";".$linhaArray[19]);


            /*if ($i >= 50){
                    die;
                }*/

            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[18]." - ".$linhaArray[3]." - ".$linhaArray[18]; //Exibe no console(Terminal) as informações dos preços durante o processamento

            $produto = Produto::find()  ->andWhere(['=','codigo_global', $linhaArray[0]])
                                        ->one(); //Procura produto pelo código "


            /*$produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',00])
                                                        ->andWhere(['=', 'produto_id', $produto->id])
                                                        ->one(); */


            $preco_compra   = (float) str_replace(",",".",str_replace(".","",$linhaArray[17]));




            if ($produto){ //Se encontrar o produto, processa o preço

                echo " - encontrado"; //Escreva no termina l
                fwrite($arquivo_log, ';Produto encontrado'); //Escreve no Log que encontrou o produto

                $produto_filial = ProdutoFilial::find()->andWhere(['=','produto_id',$produto->id])->andWhere(['=','filial_id',8])->one();

                if($produto_filial){
                    echo " - estoque encontrado"; //Escreva no termina l
                    fwrite($arquivo_log, ';Estoque encontrado'); //Escreve no Log que encontrou o produto

                    $preco_venda = $linhaArray[19];

                    //Verifica se o valor a ser adicionado É igual ao anterior, se for, nÃo adiciona o registro novo;
                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produto_filial->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();
                    if($preco_venda == $valor_produto_filial->valor){
                        echo " - mesmo valor";
                        continue;
                    }


                    //Produtos com caixar zerar estoque
                    /*$pos = strpos($linhaArray[0], "CX.");

                    if ($pos === false) {
                        $produto_caixa_conferencia = Produto::find()->andWhere(['like', 'codigo_fabricante', 'CX.'.$linhaArray[0]])
                                                                    ->one();

                        if($produto_caixa_conferencia){
                            echo " - Produto com caixa";
                            fwrite($arquivo_log, ';Produto com caixa');
                            $produto_filial->quantidade = 0;
                        }
                        else{
                            echo " - Produto sem caixa";
                            fwrite($arquivo_log, ';Produto sem caixa');
                            $produto_filial->quantidade = 947;
                    }
                    } else {
                        echo " - Produto caixa";
                        fwrite($arquivo_log, ';Produto caixa');
                        $produto_filial->quantidade = 947;
                    }*/

                    $produto_filial->quantidade = 947;

                    echo " - ".$produto_filial->quantidade;
                    if($produto_filial->save()){
                        echo " - Estoque atualizado";
                        fwrite($arquivo_log, ';Estoque atualizado');
                    }
                    else{
                        echo " - Estoque não atualizado";
                        fwrite($arquivo_log, ';Estoque Não atualizado');
                    }

                    $valor_produto_filial = new ValorProdutoFilial;
                    $valor_produto_filial->produto_filial_id    = $produto_filial->id;
                    $valor_produto_filial->valor                = $preco_venda;
                    $valor_produto_filial->valor_cnpj           = $preco_venda;
                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                    $valor_produto_filial->promocao             = false;
                    $valor_produto_filial->valor_compra         = $preco_compra;


                    //print_r($valor_produto_filial);


                    if($valor_produto_filial->save()){
                        echo " - Preço atualizado";
                        fwrite($arquivo_log, ';Preço atualizado');
                    }
                    else{
                        echo " - Preço não atualizado";
                        fwrite($arquivo_log, ';Preço Não atualizado');
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







