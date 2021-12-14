<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AtualizarCodigoFabricanteBonfanteAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do multiplicador: \n\n";

        $LinhasArray = Array();
        $file = fopen('/var/tmp/atualizar_codigos_globais_bonfante.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_atualizar_codigos_globais_bonfante.csv")){
            unlink("/var/tmp/log_atualizar_codigos_globais_bonfante.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_atualizar_codigos_globais_bonfante.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;status\n");



        foreach ($LinhasArray  as  $k => &$linhaArray){
            echo "\n".$k." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2];


            $produto = Produto::find()->andWhere(['=','codigo_fabricante', $linhaArray[1]])->one();
            if ($produto){
                echo ' - Encontrado';

                if($produto->codigo_fabricante == $linhaArray[1]){
                    echo " - CODIGO ENCONTRADO";

                    // ATUALIZAÇÃO CODIGO GLOBAL


                    $produto->codigo_global   =  str_replace("A","", str_replace("B","",str_replace("a","",str_replace("b","",$linhaArray[1]))));

                    // ATUALIZAÇÃO CODIGO FABRICANTE

                    // $produto->codigo_fabricante = $linhaArray[0]."-".$linhaArray[1];

                    //$produto->codigo_fabricante  =  str_replace("A","", str_replace("B","",str_replace("a","",str_replace("b","",$linhaArray[0]."-".$linhaArray[1]))));

                    $produto->save();

                    echo " - atualizado ";
                }

                else {

                    echo " - Não atualizado";

                }

                // ALTERAR NO NOME

                /*if ($linhaArray[1]==7800){
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." PRETO FOSCO PU";
                    $produto->save();
                }
                elseif ($linhaArray[1]==7354){
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." CINZA ORIGINAL";
                    $produto->save();

                }
                elseif ($linhaArray[1]==6500)
                {
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." PRIMER";
                    $produto->save();
                }
                elseif ($linhaArray[1]==9004)
                {
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." BRANCO MODELO ANTIGO";
                    $produto->save();
                }
                elseif ($linhaArray[1]==9147)
                {
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." BRANCO MODERNO";
                    $produto->save();
                }
                elseif ($linhaArray[1]==4925)
                {
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." BRANCO DIAMANTE PU";
                    $produto->save();
                }
                elseif ($linhaArray[1]==7400)
                {
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." CINZA FOSCO 2629 PU";
                    $produto->save();
                }
                elseif ($linhaArray[1]==7660)
                {
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." CINZA SATIN CROSS";
                    $produto->save();
                }
                elseif ($linhaArray[1]==7700)
                {
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." CINZA FOSCO ESCURO PU";
                    $produto->save();
                }
                elseif ($linhaArray[1]==7810)
                {
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." PRETO ECOAT";
                    $produto->save();
                }
                elseif ($linhaArray[1]==7830)
                {
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." PRETO PO";
                    $produto->save();
                }
                elseif ($linhaArray[1]==7850)
                {
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." PRETO PLÁSTICO SEM PINTURA";
                    $produto->save();
                }
                elseif ($linhaArray[1]==7665)
                {
                    echo " - nome atualizado ";
                    $produto->nome =  $produto->nome." CROMADO";
                    $produto->save();
                }*/

                fwrite($arquivo_log, $produto->id.";".$linhaArray[1].";codigo fabricante Atualizado\n");
                echo " - codigo fabricante Atualizado";
            }
            else {
                echo ' - não  Encontrado';


                fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";codigo fabricante não encontrado\n");
            }




        }





        // Fecha o arquivo
        fclose($arquivo_log);

        //print_r($LinhasArray);

        echo "\n\nFIM da rotina de criação preço!";
    }
}