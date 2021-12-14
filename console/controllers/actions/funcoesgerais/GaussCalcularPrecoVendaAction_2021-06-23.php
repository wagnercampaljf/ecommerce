<?php

namespace console\controllers\actions\funcoesgerais;

use common\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class GaussCalcularPrecoVendaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";


        $faixas_lampadas_unidade = array();



        $markup_mestre      = MarkupMestre::find()->andWhere(['=','id', 16])->orderBy(["id" => SORT_DESC])->one();

        $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();

        $faixas_lampadas_unidade = [];

        foreach ($markups_detalhe as $markup_detalhe){
            $faixas_lampadas_unidade [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

        }
       /* $faixas_lampadas_unidade = [
            1 => array(0 , 0.99 , 5, true),
            2 => array(1 , 1.99 , 7, true),
            3 => array(2 , 2.99, 10, true),
            4 => array(3 , 4.99 , 12, true),
            5 => array(5 , 5.99 , 20, true),
            6 => array(6 , 6.99 , 20, true),
            7 => array(7 , 7.99 , 2.5, false),
            8 => array(8 , 8.99 , 2.5, false),
            9 => array(9 , 9.99 , 2.5, false),
            10 => array(10 , 14.99 , 2.5, false),
            11 => array(15 , 19.99 , 2.5, false),
            12 => array(20 , 24.99 , 2.4, false),
            13 => array(25 , 29.99 , 2.3, false),
            14 => array(30 , 34.99 , 2.2, false),
            15 => array(35 , 39.99 , 2.1, false),
            16 => array(40 , 44.99 , 2.0, false),
            17 => array(45 , 49.99 , 1.98, false),
            18 => array(50 , 59.99 , 1.95, false),
            19 => array(60 , 69.99 , 1.92, false),
            20 => array(70 , 79.99 , 1.89, false),
            21 => array(80 , 89.99 , 1.86, false),
            22 => array(90 , 99.99 , 1.83, false),
            23 => array(100 , 124.99 , 1.82, false),
            24 => array(125 , 149.99 , 1.79, false),
            25 => array(150 , 174.99 , 1.77, false),
            26 => array(175 , 199.99 , 1.75, false),
            27 => array(200 , 224.99 , 1.73, false),
            28 => array(225 , 249.99 , 1.71, false),
            29 => array(250 , 299.99 , 1.70, false),
            30 => array(300 , 349.99 , 1.69, false),
            31 => array(350 , 399.99 , 1.69, false),
            32 => array(400 , 449.99 , 1.67, false),
            33 => array(450 , 499.99 , 1.66, false),
            34 => array(500 , 599.99 , 1.65, false),
            35 => array(600 , 699.99 , 1.64, false),
            36 => array(700 , 799.99 , 1.63, false),
            37 => array(800 , 899.99 , 1.62, false),
            38 => array(900 , 999.99 , 1.61, false),
            39 => array(1000 , 1099.99 , 1.60, false),
            40 => array(1100 , 1199.99 , 1.59, false),
            41 => array(1200 , 1299.99 , 1.58, false),
            42 => array(1300 , 1399.99 , 1.57, false),
            43 => array(1400 , 1499.99 , 1.56, false),
            44 => array(1500 , 1999.99 , 1.55, false),
            45 => array(2000 , 2999.99 , 1.54, false),
            46 => array(3000 , 3999.99 , 1.53, false),
            47 => array(4000 , 4999.99 , 1.52, false),
            48 => array(5000 , 100000 , 1.51, false),
            49 => array(100000 , 300000 , 1.50, false),



        ];*/


        $LinhasArray = Array();
        //$arquivo_origem = '/var/tmp/gauss_preco_estoque_11-05-2021';
        //$arquivo_origem = '/var/tmp/gauss_preco_estoque_12-05-2021';
        //$arquivo_origem = '/var/tmp/produtos_gauss_2021-06-01';

        $arquivo_origem = '/var/tmp/produtos_gauss_2021-06-09';


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

            //if($linhaArray[1] != "GL94H7"){
            //   continue;
            //}

            $codigo_global = $linhaArray[1];
            echo "\n".$i." - ".$codigo_global;

            fwrite($arquivo_destino, "\n".$codigo_global.';'.$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";".$linhaArray[14].";".$linhaArray[15].";".$linhaArray[16].";");

            if ($i == 0){
                fwrite($arquivo_destino, "18;19");
                continue;
            }
            if ($i == 1){
                fwrite($arquivo_destino, "PREÇO COMPRA;PREÇO VENDA");
                continue;
            }

            $preco_compra   = (real) str_replace(",",".",str_replace(".","",$linhaArray[16]));
            //$preco_compra   = (float) $linhaArray[16];

            $preco_compra = round($preco_compra, 2);

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