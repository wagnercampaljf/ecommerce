<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;

class VannucciCalcularPrecoVendaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";

        $faixas_compra = array();
        $faixas_compra = [
	    "9959"  => 0.376,
            "91"    => 0.376,
            "1279"  => 0.376,
            "2550"  => 0.376,
            "372"   => 0.376,
            "140"   => 0.376,
            "2220"  => 0.376,
            "644"   => 0.376,
            "729"   => 0.376,
            "726"   => 0.376,
            "587"   => 0.376,
            "2202"  => 0.376,
            "49"    => 0.376,
            "501"   => 0.376,
            "600"   => 0.376,
            "4486"  => 0.376,
            "2010"  => 0.376,
            "579"   => 0.376,
            "40"    => 0.376,
            "2017"  => 0.376,
            "9945"  => 0.376,
            "850"   => 0.376,
            "500"   => 0.376,
            "2144"  => 0.376,
            "126"   => 0.376,
            "2018"  => 0.376,
            "2069"  => 0.376,
            "2209"  => 0.376,
            "1154"  => 0.376,
            "1000"  => 0.376,
            "1196"  => 0.376,
            "9956"  => 0.376,
            "1050"  => 0.376,
            "421"   => 0.376,
            "475"   => 0.376,
            "440"   => 0.376,
            "1227"  => 0.376,
            "2563"  => 0.376,
            "847"   => 0.376,
            "9937"  => 0.376,
            "494"   => 0.376,
            "1035"  => 0.376,
            "1245"  => 0.376,
            "731"   => 0.376,
            "757"   => 0.376,
            "695"   => 0.376,
            "1238"  => 0.376,
            "2000"  => 0.376,
            "511"   => 0.376,
            "660"   => 0.376,
            "98"    => 0.376,
            "199"   => 0.376,
            "502"   => 0.376,
            "367"   => 0.376,
            "133"   => 0.376,
            "1272"  => 0.376,
            "285"   => 0.376,
            "2523"  => 0.376,
            "2522"  => 0.376,
            "9955"  => 0.376,
            "503"   => 0.376,
            "275"   => 0.376,
            "881"   => 0.376,
            "248"   => 0.376,
            "800"   => 0.376,
            "256"   => 0.376,
            "396"   => 0.376,
            "238"   => 0.376,
            "455"   => 0.376,
            "869"   => 0.376,
            "754"   => 0.376,
            "460"   => 0.376,
            "338"   => 0.376,
            "687"   => 0.376,
            "1305"  => 0.376,
            "914"   => 0.376,
            "828"   => 0.376,
            "482"   => 0.376,
            "423"   => 0.376,
            "159"   => 0.376,
            "1253"  => 0.376,
            "215"   => 0.376,
            "2200"  => 0.376,
            "904"   => 0.376,
            "110"   => 0.376,
            "553"   => 0.376,
            "265"   => 0.376,
            "1271"  => 0.376,
            "2171"  => 0.17,
            "2101"  => 0.17,
            "902"   => 0.17,
            "910"   => 0.17,
            "2300"  => 0.17,
            "707"   => 0.17,
	    "718"   => 0.17,
	    "119"   => 0.17,
            "2221"  => 0.17,
	    "2245"  => 0.17,
        ];
        
        /*$faixas_venda = array();
        $faixas_venda = [
            1 => array(0 , 0.99 , 10, true),
            2 => array(1 , 1.99 , 10, true),
            3 => array(2 , 2.99 , 10, true),
            4 => array(3 , 3.99 , 15, true),
            5 => array(4 , 4.99 , 15, true),
            6 => array(5 , 5.99 , 15, true),
            7 => array(6 , 6.99 , 15, true),
            8 => array(7 , 7.99 , 2.9, false),
            9 => array(8 , 8.99 , 2.4, false),
            10 => array(9 , 9.99 , 2.4, false),
            11 => array(10 , 14.99 , 2.4, false),
            12 => array(15 , 19.99 , 2.4, false),
            13 => array(20 , 24.99 , 2.4, false),
            14 => array(25 , 29.99 , 2.4, false),
            15 => array(30 , 34.99 , 1.9, false),
            16 => array(35 , 39.99 , 1.89, false),
            17 => array(40 , 44.99 , 1.88, false),
            18 => array(45 , 49.99 , 1.87, false),
            19 => array(50 , 59.99 , 1.86, false),
            20 => array(60 , 69.99 , 1.8, false),
            21 => array(70 , 79.99 , 1.77, false),
            22 => array(80 , 89.99 , 1.75, false),
            23 => array(90 , 99.99 , 1.73, false),
            24 => array(100 , 124.99 , 1.7, false),
            25 => array(125 , 149.99 , 1.68, false),
            26 => array(150 , 174.99 , 1.65, false),
            27 => array(175 , 199.99 , 1.64, false),
            28 => array(200 , 224.99 , 1.63, false),
            29 => array(225 , 249.99 , 1.62, false),
            30 => array(250 , 299.99 , 1.61, false),
            31 => array(300 , 349.99 , 1.6, false),
            32 => array(350 , 399.99 , 1.59, false),
            33 => array(400 , 449.99 , 1.58, false),
            34 => array(450 , 499.99 , 1.57, false),
            35 => array(500 , 599.99 , 1.54, false),
            36 => array(600 , 699.99 , 1.53, false),
            37 => array(700 , 799.99 , 1.53, false),
            38 => array(800 , 899.99 , 1.52, false),
            39 => array(900 , 999.99 , 1.51, false),
            40 => array(1000 , 1099.99 , 1.5, false),
            41 => array(1100 , 1199.99 , 1.5, false),
            42 => array(1200 , 1299.99 , 1.5, false),
            43 => array(1300 , 1399.99 , 1.5, false),
            44 => array(1400 , 1499.99 , 1.5, false),
            45 => array(1500 , 1999.99 , 1.5, false),
            46 => array(2000 , 2999.99 , 1.5, false),
            47 => array(3000 , 3999.99 , 1.5, false),
            48 => array(4000 , 4999.99 , 1.5, false),
            49 => array(5000 , 100000 , 1.5, false),
            50 => array(100000 , 300000 , 1.5, false),
        ];*/
        
        $faixas_venda = array();
        $faixas_venda = [
            1 => array(0 , 0.99 , 10, true),
            2 => array(1 , 1.99 , 10, true),
            3 => array(2 , 2.99 , 10, true),
            4 => array(3 , 3.99 , 15, true),
            5 => array(4 , 4.99 , 15, true),
            6 => array(5 , 5.99 , 15, true),
            7 => array(6 , 6.99 , 15, true),
            8 => array(7 , 7.99 , 2.9, false),
            9 => array(8 , 8.99 , 2.8, false),
            10 => array(9 , 9.99 , 2.7, false),
            11 => array(10 , 14.99 , 2.6, false),
            12 => array(15 , 19.99 , 2.5, false),
            13 => array(20 , 24.99 , 2.4, false),
            14 => array(25 , 29.99 , 2.3, false),
            15 => array(30 , 34.99 , 2.2, false),
            16 => array(35 , 39.99 , 2.1, false),
            17 => array(40 , 44.99 , 2.0, false),
            18 => array(45 , 49.99 , 1.98, false),
            19 => array(50 , 59.99 , 1.95, false),
            20 => array(60 , 69.99 , 1.92, false),
            21 => array(70 , 79.99 , 1.89, false),
            22 => array(80 , 89.99 , 1.86, false),
            23 => array(90 , 99.99 , 1.83, false),
            24 => array(100 , 124.99 , 1.80, false),
            25 => array(125 , 149.99 , 1.77, false),
            26 => array(150 , 174.99 , 1.74, false),
            27 => array(175 , 199.99 , 1.72, false),
            28 => array(200 , 224.99 , 1.69, false),
            29 => array(225 , 249.99 , 1.68, false),
            30 => array(250 , 299.99 , 1.67, false),
            31 => array(300 , 349.99 , 1.66, false),
            32 => array(350 , 399.99 , 1.65, false),
            33 => array(400 , 449.99 , 1.64, false),
            34 => array(450 , 499.99 , 1.63, false),
            35 => array(500 , 599.99 , 1.62, false),
            36 => array(600 , 699.99 , 1.61, false),
            37 => array(700 , 799.99 , 1.60, false),
            38 => array(800 , 899.99 , 1.59, false),
            39 => array(900 , 999.99 , 1.58, false),
            40 => array(1000 , 1099.99 , 1.57, false),
            41 => array(1100 , 1199.99 , 1.56, false),
            42 => array(1200 , 1299.99 , 1.55, false),
            43 => array(1300 , 1399.99 , 1.54, false),
            44 => array(1400 , 1499.99 , 1.53, false),
            45 => array(1500 , 1999.99 , 1.53, false),
            46 => array(2000 , 2999.99 , 1.53, false),
            47 => array(3000 , 3999.99 , 1.53, false),
            48 => array(4000 , 4999.99 , 1.52, false),
            49 => array(5000 , 100000 , 1.51, false),
            50 => array(100000 , 300000 , 1.50, false),
        ];
        
        $LinhasArray = Array();

        $arquivo_origem = '/var/tmp/vannucci_15-05-2020';
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
        
        foreach ($LinhasArray as $i => &$linhaArray){
            
            //if($linhaArray[0] != "VA6594X-482"){ continue; }
            
            echo "\n".$i." - ".$linhaArray[0]." - ";
            
            if ($i <= 0){
                fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";Preço Compra;Preço Venda;codigo_fabricante_completo\n");
                continue;
            }
            
            //print_r($linhaArray)
            $codigo_fabricante  = explode("-",$linhaArray[0]);
            
            $desconto = 1-0.3760;
            
            if (array_key_exists(1,$codigo_fabricante)){
                echo $codigo_fabricante[1];

                $sufixo             = $codigo_fabricante[1];
                $sufixo             = str_replace("*1","",$sufixo);
                $sufixo             = str_replace("*2","",$sufixo);
                $sufixo             = str_replace("*3","",$sufixo);
                $sufixo             = str_replace("*4","",$sufixo);
                $sufixo             = str_replace("*5","",$sufixo);
                $sufixo             = str_replace("*6","",$sufixo);
                $sufixo             = str_replace("*7","",$sufixo);
                $sufixo             = str_replace("*8","",$sufixo);
                $sufixo             = str_replace("*9","",$sufixo);
                
                if (array_key_exists($sufixo,$faixas_compra)){
                    $desconto = 1-$faixas_compra[$sufixo];
                }
            }
            
            echo " - ".$desconto;
            
            $this->existe_produto_caixa($linhaArray, $arquivo_destino, $faixas_compra, $faixas_venda);
            
            //$preco_compra   = (float) str_replace(",",".",str_replace(".","",$linhaArray[5]));
            $preco_compra   = (float) $linhaArray[5];
            echo " - ".$preco_compra;
            $preco_compra   = $preco_compra * $desconto;
            
            //$preco_compra = $preco_compra * 0.65;
            foreach ($faixas_venda as $k => $faixa_venda) {
                if($preco_compra >= $faixa_venda[0] && $preco_compra <= $faixa_venda[1]){
                    $preco_venda = round(($preco_compra * $faixa_venda[2]),2);
                    if ($faixa_venda[3]){
                        $preco_venda = $faixa_venda[2];
                    }
                    break;
                }
            }
            
            fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$preco_compra.";".$preco_venda.";".$linhaArray[0]."\n");
        }
        
        // Fecha o arquivo
        fclose($arquivo_destino);

        
        echo "\n\nFIM da rotina de criação preço!";
    }
    
    
    public function existe_produto_caixa($linha, $arquivo_destino, $faixas_compra, $faixas_venda){
        
        $produto = Produto::find()  ->andWhere(["=","codigo_fabricante","CX.".$linha[0]])
                                    ->andWhere(["=","fabricante_id",91])
                                    ->one();
        
        if($produto){
                        
            $codigo_fabricante  = explode("-",$linha[0]);
            
            $desconto = 1-0.3760;
            
            if (array_key_exists(1,$codigo_fabricante)){
                echo $codigo_fabricante[1];
                
                if (array_key_exists($codigo_fabricante[1],$faixas_compra)){
                    $desconto = 1-$faixas_compra[$codigo_fabricante[1]];
                }
            }
            
            echo " - desconto: ".$desconto;
            
            $preco_compra   = (float) str_replace(",",".",str_replace(".","",$linha[5]));
            echo " - ".$preco_compra;
            $preco_compra   = $preco_compra * $desconto * $produto->multiplicador;
 
            //$preco_compra = $preco_compra * 0.65;
            foreach ($faixas_venda as $k => $faixa_venda) {
                if($preco_compra >= $faixa_venda[0] && $preco_compra <= $faixa_venda[1]){
                    $preco_venda = round(($preco_compra * $faixa_venda[2]),2);
                    if ($faixa_venda[3]){
                        $preco_venda = $faixa_venda[2];
                    }
                    fwrite($arquivo_destino, $linha[0].";".$linha[1].";".$linha[2].";".$linha[3].";".$linha[4].";".$linha[5].";".$preco_compra.";".$preco_venda.";CX.".$linha[0]."\n");
                    break;
                }
            }
        }
    }
}
