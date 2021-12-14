<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class ForrosAtualizaPrecoVendaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do preço: \n\n";


        $LinhasArray = Array();


        $file = fopen("/var/tmp/emblemas_avulsos_2021-08-03.csv", 'r');


        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_emblemas_avulsos_2021-08-03.csv")){
            unlink("/var/tmp/log_emblemas_avulsos_2021-08-03.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_emblemas_avulsos_2021-08-03.csv", "a");

        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as informações de preços a subir

            if ($i <= 1){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                fwrite($arquivo_log, "status");
                continue;
            }

            //fwrite($arquivo_log, "\n".$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12]);


            /*if ($i >= 50){
                    die;
                }*/

            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[7]." - ".$linhaArray[5]; //Exibe no console(Terminal) as informações dos preços durante o processamento

            $produto = Produto::find()  ->andWhere(['=','codigo_global', $linhaArray[0]])
                                        ->one(); //Procura produto pelo código "






            if ($produto){ //Se encontrar o produto, processa o preço

                echo " - encontrado"; //Escreva no termina l
                fwrite($arquivo_log, ';Produto encontrado'); //Escreve no Log que encontrou o produto

                $produto_filial = ProdutoFilial::find()->andWhere(['=','produto_id',$produto->id])->andWhere(['=','filial_id',8])->one();

                if($produto_filial){
                    echo " - estoque encontrado"; //Escreva no termina l
                    fwrite($arquivo_log, ';Estoque encontrado'); //Escreve no Log que encontrou o produto

                    $preco_venda = $linhaArray[5];
                    //$preco_compra   = $linhaArray[10];

                    /*if ($preco_compra <=200){
                        $preco_venda = $linhaArray[11] +30;
                    }else{
                        $preco_venda = $linhaArray[11];
                    }*/




                    $valor_produto_filial = new ValorProdutoFilial;
                    $valor_produto_filial->produto_filial_id    = $produto_filial->id;
                    $valor_produto_filial->valor                = $preco_venda;
                    $valor_produto_filial->valor_cnpj           = $preco_venda;
                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                    $valor_produto_filial->promocao             = false;
                    $valor_produto_filial->valor_compra         =$valor_produto_filial->valor_compra ;


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







