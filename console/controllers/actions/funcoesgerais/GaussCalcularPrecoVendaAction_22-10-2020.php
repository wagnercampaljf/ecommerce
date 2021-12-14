<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class GaussCalcularPrecoVendaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";

        $faixas_lampadas_unidade = [
            1 => array(0 , 1.99 , 5, true),
            2 => array(2 , 3.99 , 8, true),
            3 => array(4 , 100000 , 2.0, false),
        ];

        $LinhasArray = Array();
        $arquivo_origem = '/var/tmp/gauss_preco_estoque_24-09-2020';
        $file = fopen($arquivo_origem.".csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        $destino = $arquivo_origem."_precificado.csv";
        if (file_exists($destino)){
            unlink($destino);
        }

        $arquivo_destino = fopen($destino, "a");
        // Escreve no log

        foreach ($LinhasArray as $i => &$linhaArray){

            $codigo_global = $linhaArray[1];
            echo "\n".$i." - ".$codigo_global;

            fwrite($arquivo_destino, "\n".$codigo_global.';'.$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";".$linhaArray[14].";".$linhaArray[15].";".$linhaArray[16].";");

            if ($i == 0){
                fwrite($arquivo_destino, "17;18");
                continue;
            }
            if ($i == 1){
                fwrite($arquivo_destino, "PREÇO COMPRA;PREÇO VENDA");
                continue;
            }

            $preco_compra   = (real) str_replace(",",".",str_replace(".","",$linhaArray[16]));
            //$preco_compra   = (float) $linhaArray[16];


            echo " - ".$preco_compra;

            //Faixa_lampadas_unidade;
            foreach ($faixas_lampadas_unidade as $k => $faixa_lampadas_unidade) {
                if($preco_compra >= $faixa_lampadas_unidade[0] && $preco_compra <= $faixa_lampadas_unidade[1]){
                    $preco_venda = round(($preco_compra * $faixa_lampadas_unidade[2]),2);

                    if ($faixa_lampadas_unidade[3]){
                        $preco_venda = $faixa_lampadas_unidade[2];
                    }

                    fwrite($arquivo_destino, $preco_compra.";".$preco_venda);
                    echo " - ".$preco_venda;
                    break;
                }
            }

            $produtos_caixa  = Produto::find()  ->andWhere(['like','codigo_global',$codigo_global])
                                                ->andWhere(['like','codigo_global',"CX."])
                                                ->all();



            foreach($produtos_caixa as $produto_caixa){
                $preco_compra   = (real) str_replace(",",".",str_replace(".","",$linhaArray[16]));

                $preco_compra   = $produto_caixa->multiplicador * $preco_compra;
                echo " - (CX)".$preco_compra;
                $codigo_global  = $produto_caixa->codigo_global;

                foreach ($faixas_lampadas_unidade as $k => $faixa) {
                    if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                        $preco_venda = round(($preco_compra * $faixa[2]),2);
                        if ($faixa[3]){
                            $preco_venda = $faixa[2];
                        }
                        fwrite($arquivo_destino, "\n".$codigo_global.';'.$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";".$linhaArray[14].";".$linhaArray[15].";".$linhaArray[16].";".$preco_compra.";".$preco_venda);
                        echo " - ".$preco_venda." - Produto Caixa encontrado";
                        break;
                    }
                }
            }
        }






        // Fecha o arquivo
        fclose($arquivo_destino);

        echo "\n\nFIM da rotina de criação preço!";
    }
}