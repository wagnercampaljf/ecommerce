<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AtualizarNCMBonfanteAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do multiplicador: \n\n";

        $LinhasArray = Array();
        $file = fopen('/var/tmp/bonfante_05-11-2020.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_bonfante_05-11-2020.csv")){
            unlink("/var/tmp/log_bonfante_05-11-2020.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_bonfante_05-11-2020.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;status\n");



        foreach ($LinhasArray  as  $k => &$linhaArray){
            echo "\n".$k." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2];



            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante',  $linhaArray[0]])->one();
            if ($produto){
                echo " - Produto encontrado";

            $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',86])
                ->andWhere(['=', 'produto_id', $produto->id])
                ->one();


            if ($produtoFilial) {

                $produto = Produto::find()->andWhere(['=','codigo_fabricante', $linhaArray[0]])->one();

                if ($produto){

                    if($produto->codigo_montadora == $linhaArray[6]  ){
                        echo " - Mesmo Codigo montadora ";
                        continue;
                    }



                    $produto->codigo_montadora = $linhaArray[6];
                    $produto->save();

                    fwrite($arquivo_log, $produto->id.";".$linhaArray[1].";codigo montadora Atualizado\n");
                    echo " - codigo montadora Atualizado";
                }
                else {
                    // Escreve no log
                    fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";codigo montadora não encontrado\n");
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