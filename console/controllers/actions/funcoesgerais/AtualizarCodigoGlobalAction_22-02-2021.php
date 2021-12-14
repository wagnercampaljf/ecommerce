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
        $file = fopen('/var/tmp/dib_produtos_codigo_global.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);


        if (file_exists("/var/tmp/log_dib_produtos_codigo_global.csv")){
            unlink("/var/tmp/log_dib_produtos_codigo_global.csv");
        }


        $arquivo_log = fopen("/var/tmp/log_dib_produtos_codigo_global..csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;status\n");



        foreach ($LinhasArray  as  $k => &$linhaArray){
            echo "\n".$k." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2];



            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', 'D'.$linhaArray[3]])->one();
            if ($produto){
                echo " - Produto encontrado";

            $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',97])
                ->andWhere(['=', 'produto_id', $produto->id])
                ->one();


            if ($produtoFilial) {

                $produto = Produto::find()->andWhere(['=','codigo_fabricante', 'D'.$linhaArray[3]])->one();

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