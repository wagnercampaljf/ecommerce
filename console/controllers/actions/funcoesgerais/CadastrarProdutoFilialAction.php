<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class CadastrarProdutoFilialAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do multiplicador: \n\n";

        $LinhasArray = Array();
        //$file = fopen('/var/tmp/dib_produtos_codigo_global.csv', 'r');

        $file = fopen('/var/tmp/produtos_12-02-2021.1.csv', 'r');



        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);


        if (file_exists("/var/tmp/log_produtos_12-02-2021.1.csv")){
            unlink("/var/tmp/log_produtos_12-02-2021.1.csv");
        }


        $arquivo_log = fopen("/var/tmp/log_produtos_12-02-2021.1.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;status\n");



        foreach ($LinhasArray  as  $k => &$linhaArray){
            echo "\n".$k." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2];



            $produto = Produto::find()  ->andWhere(['=','codigo_global', $linhaArray[0]])->one();


            if ($produto){
                echo " - Produto encontrado";


                $produto_filial_novo    = new ProdutoFilial;
                $produto_filial_novo->produto_id                    = $produto->id;
                $produto_filial_novo->filial_id                     = 8;
                $produto_filial_novo->quantidade                    = 9999;
                $produto_filial_novo->envio                         = 1;
                $produto_filial_novo->atualizar_preco_mercado_livre = true;
                if($produto_filial_novo->save()){
                    echo " - estoque_criado";
                    fwrite($arquivo_log, " - estoque_criado");
                }
                else{
                    print_r($produto_filial_novo);
                    echo " - estoque_nao_criado";
                    fwrite($arquivo_log, " - estoque_nao_criado");
                }

                $preco_compra = $linhaArray[6];

                $preco_venda = $linhaArray[5];

                $valor_produto_filial = new ValorProdutoFilial;
                $valor_produto_filial->produto_filial_id    = $produto_filial_novo->id;
                $valor_produto_filial->valor                = $preco_venda;
                $valor_produto_filial->valor_cnpj           = $preco_venda;
                $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                $valor_produto_filial->promocao             = false;
                $valor_produto_filial->valor_compra         = $preco_compra;
                if($valor_produto_filial->save()){
                    echo " - Preço criado";
                    fwrite($arquivo_log, ' - Preço criado');
                }
                else{
                    echo " - preco_nao_criado";
                    fwrite($arquivo_log, ' - preco_nao_criado');
                }

            /*if ($produtoFilial) {

                $produto = Produto::find()  ->andWhere(['=','codigo_global', $linhaArray[0]])->one();

                if ($produto){


                    $produto->codigo_global = $linhaArray[0];
                    $produto->save();

                    fwrite($arquivo_log, $produto->id.";".$linhaArray[1].";codigo global Atualizado\n");
                    echo " - codigo global Atualizado";
                }
                else {
                    // Escreve no log
                    fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";codigo global não encontrado\n");
                }
            }else{
                echo " - Não encontrado";
                fwrite($arquivo_log, 'Produto Não encontrado');
            }*/

            }else{
                echo " - Produto não encontrado";
                fwrite($arquivo_log, ";Produto não encontrado");
            }


        }

        // Fecha o arquivo
        fclose($arquivo_log);

        //print_r($LinhasArray);

        echo "\n\nFIM da rotina de criação preço!";
    }
}