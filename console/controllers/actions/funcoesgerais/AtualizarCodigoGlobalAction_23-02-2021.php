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
        $file = fopen('/var/tmp/produtos_emblema-22-02-2021.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);


        if (file_exists("/var/tmp/log_produtos_emblema-22-02-2021.csv")){
            unlink("/var/tmp/log_produtos_emblema-22-02-2021.csv");
        }


        $arquivo_log = fopen("/var/tmp/log_produtos_emblema-22-02-2021.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;status\n");



        foreach ($LinhasArray  as  $k => &$linhaArray){
            echo "\n".$k." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2];


            $produto = Produto::find()->andWhere(['=','codigo_global', $linhaArray[0]])->one();

                if ($produto){

                    $produto->subcategoria_id      = 803;

                    //$produto->codigo_global = $linhaArray[0];

                    $produto->save();

                    fwrite($arquivo_log, $produto->id.";".$linhaArray[1].";Categoria Atualizado\n");
                    echo " - codigo global Atualizado";
                }
                else {
                    // Escreve no log
                    fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";Categoria não encontrado\n");
                }



        }

        // Fecha o arquivo
        fclose($arquivo_log);

        //print_r($LinhasArray);

        echo "\n\nFIM da rotina de criação preço!";
    }
}