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

        echo "INÍCIO da rotina de atualizacao do Codigo Global:CX. \n\n";        

        $LinhasArray = Array();
        $file = fopen('/var/tmp/produtos_caixa_sem_padrao_2021-12-07.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        $arquivo_log = fopen("/var/tmp/log_caixas_sem_padrao_.".date("Y-m-d_H-i-s").".csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;status\n");

        // echo "<prev>";
        // print_r($LinhasArray);
        // echo "</prev>";
       // // die;

        foreach ($LinhasArray  as  $k => &$linhaArray){
            
            // if($k < 147){continue;}

            $codigo_global = $linhaArray[0];
            $nome          = $linhaArray[1];
            $multiplicador = $linhaArray[2];

            //Multiplicador sempre tem que ser par
            // if(!$multiplicador % 2 == 0){

            //     $multiplicador+= 1;
            // }

            echo "\n".$k." - ".$codigo_global." - ".$nome." - ".$multiplicador;

          

            $produto = Produto::find()->andWhere(['=','codigo_global', $codigo_global])->one();

            if ($produto){
                echo " - Produto encontrado ";


               $produto = Produto::find()->andWhere(['=','codigo_global', $codigo_global])->one();

                if ($produto){

                    $codigo_global = str_replace("P.","",$codigo_global);

                    if(strpos("CX",$codigo_global) === true){
                        $codigo   =   str_replace("CX","",$codigo_global); 
                    }else{
                        $codigo   =   str_replace("CX-","",$codigo_global);
                    }
                    $codigoNovo = "CX.".$codigo; 
                    
                    echo " - ".$codigoNovo;

                    $produto->codigo_global = $codigoNovo;                     
                    $produto->save();                 
                    

                    fwrite($arquivo_log, $produto->id.";".$nome.";Codigo global  Atualizado\n");
                    echo " - codigo global  Atualizado";
                }
                else {
                    // Escreve no log
                    fwrite($arquivo_log, $codigo_global.";".$nome.";nome não encontrado\n");
                }
            

            }else{
                echo " - Produto não encontrado";
                fwrite($arquivo_log, ";Produto não encontrado");
            }


        }

        // Fecha o arquivo
        fclose($arquivo_log);

        //print_r($LinhasArray);

        echo "\n\nFIM da rotina de atualização de codigo global CX! \n";
    }
}