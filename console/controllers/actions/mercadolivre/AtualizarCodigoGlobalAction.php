<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AtualizarCodigoGlobalAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do multiplicador: \n\n";

        $LinhasArray = Array();
        $file = fopen('/var/tmp/produtos_caixas_lng.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);


        if (file_exists("/var/tmp/log_produtos_caixas_lng.csv")){
            unlink("/var/tmp/log_produtos_caixas_lng.csv");
        }


        $arquivo_log = fopen("/var/tmp/log_produtos_caixas_lng.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;status\n");



        foreach ($LinhasArray  as  $k => &$linhaArray){
            echo "\n".$k." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2];



            $produto = Produto::find()->andWhere(['=','codigo_fabricante', "CX.".$linhaArray[1]])->one();

            if ($produto){
                echo " - Produto encontrado";


            $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',60])
                ->andWhere(['=', 'produto_id', $produto->id])
                ->one();


            if ($produtoFilial) {

               // printf($produtoFilial);
               // die();

                $produto = Produto::find()->andWhere(['=','codigo_fabricante', "CX.".$linhaArray[1]])->one();

                if ($produto){

                    //$produto->nome = str_replace(" BR ","", $linhaArray[2]);
                    //$produto->largura              = 30;
                    //$produto->profundidade         = 30;
                    //$produto->peso             =  5;
                    //$produto->produto_condicao_id  = 5;
                    //$produto->subcategoria_id      = 446;
                    //$produto->codigo_global = $linhaArray[0];
                    //$produto->delete();
                   // print_r($produto);



                    $produto->subcategoria_id = 35;


                    $produto->save();

                    fwrite($arquivo_log, $produto->id.";".$linhaArray[1].";Codigo global  Atualizado\n");
                    echo " - codigo global  Atualizado";
                }
                else {
                    // Escreve no log
                    fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";nome não encontrado\n");
                }
            }else{
                echo " - Não encontrado";
                fwrite($arquivo_log, 'Produto Não encontrado');
            }

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