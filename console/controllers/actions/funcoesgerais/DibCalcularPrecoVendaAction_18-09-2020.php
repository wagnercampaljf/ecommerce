<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;

class DibCalcularPrecoVendaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $faixas = array();
        $faixas = [ 
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
        //print_r($faixas);
        
        $LinhasArray = Array();
        //$arquivo_origem = '/var/tmp/750_1115_pellegrino_vannucci';
        //$arquivo_origem = '/var/tmp/dib_produtos';
        //$arquivo_origem = '/var/tmp/LISTA_WAGNER_SUBIR_SEM_TRATAR_150-199_2083-MAIOR';
        //$arquivo_origem = '/var/tmp/produtos_19_99_peca_agora_sp_acabamentos';
        //$arquivo_origem = '/var/tmp/dib_estoque_15-08-19';
        //$arquivo_origem = '/var/tmp/ESTOQUE_DIB_26-08-2019';
        //$arquivo_origem = '/var/tmp/Lista_Geral_DIB_09-09-19';
        //$arquivo_origem = '/var/tmp/estuque_dib_11-11-2019';
        //$arquivo_origem = '/var/tmp/estoque_dib_06-12-2019';
        //$arquivo_origem = '/var/tmp/dib_estoque_preco_19-12-2019';
        //$arquivo_origem = '/var/tmp/dib_estoque_20-01-2020';
        //$arquivo_origem = '/var/tmp/dib_estoque_13-02-2020';
        //$arquivo_origem = '/var/tmp/dib_estoque_09-03-2020';
        //$arquivo_origem = '/var/tmp/dib_estoque_preco_13-05-20';
        //$arquivo_origem = '/var/tmp/dib_estoque_preco_04-06-2020';
        //$arquivo_origem = '/var/tmp/dib_estoque_preco_20-07-2020';
        //$arquivo_origem = '/var/tmp/dib_estoque_preco_28-08-2020';
        $arquivo_origem = '/var/tmp/dib_estoque_preco_18-09-2020';

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
        //fwrite($arquivo_destino, "Código;Descricao;Un;Bruto;Original;Fabrica;Grupo;Saldo;Especificações;Peso Bruto;Altura;Largura;Comprimento;NCM;CEST;%IPI\n");
        //fwrite($arquivo_destino, "Código DIB;Descricao;Un;PREÇO BRUTO;Original;Grupo;ESTOQUE;PREÇO COMPRA;PREÇO VENDA\n");
        
        foreach ($LinhasArray as $i => &$linhaArray){
            
            $codigo_fabricante = $linhaArray[0];
            //fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].";");
            //fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].";");

            fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].";");



            if ($i == 0){
                fwrite($arquivo_destino, "6;7");
                continue;
            }
            /*if ($i == 0){
                fwrite($arquivo_destino, "7;8");
                continue;
            }*/




            if ($i == 1){
                fwrite($arquivo_destino, "PREÇO COMPRA;PREÇO VENDA");
                continue;
            }
         
            $preco_compra   = (float) str_replace(",",".",str_replace(".","",$linhaArray[3]));
            //$preco_compra   = $linhaArray[3];

            $multiplicador 	= 1;
            $produto		= Produto::find()->andWhere(['=','codigo_fabricante','D'.$linhaArray[0]])->one();
            if($produto){
                if(!is_null($produto->multiplicador)){
                    if($produto->multiplicador > 1 ){
                        $multiplicador = $produto->multiplicador;
                    }
                }
            }

	        $preco_compra = $multiplicador * $preco_compra;

            echo "\n".$i." - ".$preco_compra;

	        if($linhaArray[4] == "239-CAPAS CONFECCAO CHINIL DIB" || $linhaArray[4] == "352-CAPAS CONFECCAO PELUCIA DIB" || $linhaArray[4] == "586-CAPAS CONFECCAO CHINIL PREMIUM" || $linhaArray[4] == "587-CAPAS CONFECCAO CORINO"){
                $preco_compra   = 0.55*$preco_compra;
            }
            else{
                $preco_compra   = 0.45*$preco_compra;
            }
            //$preco_compra   = 0.45*$preco_compra;

            echo " - ".$preco_compra;
            
            //$preco_compra = $preco_compra * 0.65;
            foreach ($faixas as $k => $faixa) {
                if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                    $preco_venda = round(($preco_compra * $faixa[2]),2);
                    if ($faixa[3]){
                        $preco_venda = $faixa[2];
                    }
                    fwrite($arquivo_destino, $preco_compra.";".$preco_venda);
                    break;
                }
            }

            $produto_caixa  = Produto::find()->andWhere(['=','codigo_fabricante','CX.D'.$linhaArray[0]])->one();
            if($produto_caixa){
                $preco_compra       = $produto_caixa->multiplicador * $preco_compra;
                $codigo_fabricante  = 'CX.D'.$linhaArray[0];
                
                foreach ($faixas as $k => $faixa) {
                    if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                        $preco_venda = round(($preco_compra * $faixa[2]),2);
                        if ($faixa[3]){
                            $preco_venda = $faixa[2];
                        }
                        //fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$preco_compra.";".$preco_venda);
                        //fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$preco_compra.";".$preco_venda);

                        fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$preco_compra.";".$preco_venda);

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
