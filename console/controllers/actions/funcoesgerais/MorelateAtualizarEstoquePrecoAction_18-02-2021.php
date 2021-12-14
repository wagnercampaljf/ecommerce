<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class MorelateAtualizarEstoquePrecoAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $codigos_produtos = array();
        
        $LinhasArray = Array();

        ///////////////////////////////////////////////////////
        //ARQUIVO COM DADOS morelate
        ///////////////////////////////////////////////////////


       // $file = fopen("/var/tmp/morelate_14_01_2021.csv", 'r');

        //$file = fopen("/var/tmp/morelate_18_01_2021.csv", 'r');

        //$file = fopen("/var/tmp/morelate_19_01_2021.csv", 'r');

        //$file = fopen("/var/tmp/morelate_29_01_2021.csv", 'r');

        //$file = fopen("/var/tmp/morelate_01_02_2021.csv", 'r');

        //$file = fopen("/var/tmp/morelate_02_02_2021.csv", 'r');

        //$file = fopen("/var/tmp/morelate_03_02_2021.csv", 'r');

        //$file = fopen("/var/tmp/morelate_04_02_2021.csv", 'r');

        //$file = fopen("/var/tmp/morelate_05_02_2021.csv", 'r');

        //$file = fopen("/var/tmp/morelate_08_02_2021.csv", 'r');

        //$file = fopen("/var/tmp/morelate_09_02_2021.csv", 'r');

        //$file = fopen("/var/tmp/morelate_10_02_2021.csv", 'r');

        //$file = fopen("/var/tmp/morelate_11_02_2021.csv", 'r');

        $file = fopen("/var/tmp/morelate_12_02_2021.csv", 'r');





        ///////////////////////////////////////////////////////
        //ARQUIVO COM DADOS
        ///////////////////////////////////////////////////////

        while (($line = fgetcsv($file,null,';')) !== false)

        {

            $LinhasArray[] = $line;

            $codigos_produtos[$line[0].".M"] = $line[0];

        }

        fclose($file);

        $log = "/var/tmp/log_morelate_11_02_2021.csv";

        if (file_exists($log)){

            unlink($log);

        }

        $arquivo_log = fopen($log, "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){

            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2];

            // if($i<29803){continue;}

	        $preco_compra = $linhaArray[2];

            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";'.$linhaArray[1].';'.$linhaArray[2].';');

            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[0].".M"])->one();
            
            if ($produto){

                echo  " - Produto encontrado - ".$produto->id." - MarcaID: ".$produto->marca_produto_id;

                fwrite($arquivo_log, 'Produto encotrado');

                //DESCONTO DE 7% EM ESTIBO PECA;
                //474 | ESTRIBOPECAS - id da marca_produto
                if($produto->marca_produto_id == 474 ){

                    echo " - ESTRIBO PE ^ A";

                    $preco_compra  = 0.93 * $preco_compra;

                }


                $preco_compra += $this->calcular_impostos($preco_compra, $produto->marca_produto_id, $produto->ipi);

                $preco_venda = $this->calcular_preco_venda($preco_compra);

                echo " - Preço venda: ".$preco_venda;

                fwrite($arquivo_log, ";".$preco_venda.';');

                $produto_filial = ProdutoFilial::find()->andWhere(['=','produto_id',$produto->id])

                                                       ->andWhere(['=','filial_id',43])

                                                       ->one();

                if($produto_filial){

                   $produto_filial->quantidade = (int) $linhaArray[1];

                   // REGRA DE ESTOQUE ZERADO ESTOQUE MENOR QUE 2 ZERAR

                   if($produto_filial->quantidade <= "2" ){

                       $produto_filial->quantidade = 0;

                   }

                   if($produto->codigo_fabricante == '084629.M' ){

                       echo " Produto fora de linha Morelate";

                       $produto_filial->quantidade = 0;

                   }

                   if($produto->codigo_fabricante == '086611.M' ){

                       echo " Produto com futo de estoque";

                       $produto_filial->quantidade = 0;

                   }

                   
                   if($produto_filial->save()){

                       echo  " - Estoque alterado: " .$produto_filial->quantidade;

                       fwrite($arquivo_log, ' - Estoque alterado' );

                   }

                   else{

                       echo " - Estoque não alterado";

                       fwrite($arquivo_log, ' - Estoque não alterado');

                   }

                   $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produto_filial->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();

                   if ($preco_venda > $valor_produto_filial->valor * 3){

                       echo " - Preco mais alto que o normal";

                       fwrite($arquivo_log, ';Preço mais alto que o normal');

                       continue;

                   }elseif ($preco_venda < $valor_produto_filial->valor * 0.70){

                       echo " - Preco mais baixo que o normal";

                       fwrite($arquivo_log, ';Preço mais baixo que o normal');

                       continue;

                   }elseif ($preco_venda == $valor_produto_filial->valor){

                       echo " - mesmo valor";

                       fwrite($arquivo_log, ';mesmo valor');

                       continue;
                   } else {

                       echo " - Preco normal";

                   }

                    /*Verifica se o valor a ser adicionado É igual ao anterior, se for, nÃo adiciona o registro novo;
                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produto_filial->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();

                    if($preco_venda == $valor_produto_filial->valor){

                        echo " - mesmo valor";

                        continue;

                    }*/

                    $valor_produto_filial                       = new ValorProdutoFilial();

                    $valor_produto_filial->produto_filial_id    = $produto_filial->id;

                    $valor_produto_filial->valor                = $preco_venda;

                    $valor_produto_filial->valor_cnpj           = $preco_venda;

                    $valor_produto_filial->valor_compra         = $preco_compra;

                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");

                    $valor_produto_filial->promocao             = false;

                    if($valor_produto_filial->save()){

                        echo " - Preço atualizado";

                        fwrite($arquivo_log, ' - Preço alterado');

                    }

                    else{

                        echo " - Preço não atualizado";

                        fwrite($arquivo_log, ' - Preço não alterado');

                    }

                }

                else{

                    echo " - Estoque não encontrado";

                    fwrite($arquivo_log, ' - Estoque não encontrado');

                }

            }

            else{

                echo ' - Produto não encontrado';

                fwrite($arquivo_log, 'Produto não encontrado');

            }

        }



        //SEGUNDA ANÁLISE
        $produtos_morelate = Produto::find()->andWhere(['=', 'fabricante_id', 130])->all();

        fwrite($arquivo_log, "\n\n\n".'"produto_id";"codigo_fabricante";"quantidade";"status"');

        foreach($produtos_morelate as $k => $produto_morelate){

            $produto_encontrado = false;

            if(array_key_exists($produto_morelate->codigo_fabricante, $codigos_produtos)){

                $produto_encontrado = true;

            }

            if(!$produto_encontrado){

                echo "\n".$k." - ".$produto_morelate->codigo_fabricante." - produto não encontrado na planilha";

                $produto_filial = ProdutoFilial::find() ->andWhere(['=','produto_id', $produto_morelate->id])

                                                        ->andWhere(['=', 'filial_id', 43])

                                                        ->one();

                if($produto_filial){

                    $quantidade = $produto_filial->quantidade;

                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produto_filial->id])->orderBy(["id"=>SORT_DESC])->one();

                    if($valor_produto_filial){

                        $status = "Valor encontrado";

                        // if($produto_filial->quantidade <= 1 && $valor_produto_filial->valor <= 500){

                            $produto_filial->quantidade = 0;

                            if($produto_filial->save()){

                                $status .= " - Quantidade zerada";

                            }

                            else{

                                $status .= " - QUantidade não zerada";

                            }
                        // }

                        fwrite($arquivo_log, "\n".'"'.$produto_morelate->id.'";"'.$produto_morelate->codigo_fabricante.'";"'.$quantidade.'";"'.$status.'"');

                    }

                }

            }

        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de atualizacao do preço!";

    }

    public function calcular_impostos($preco_compra, $marca_produto_id, $ipi = 0){

        $valor_ipi = 0;

        if($ipi > 0){

            $valor_ipi = $preco_compra * ($ipi/100);

        }

        echo " - IPI: ".$valor_ipi;

        $valor_st = 0;

        //IMPOSTO ST NAS MARCAS:
        if($marca_produto_id == 680 ||

            $marca_produto_id == 6	 ||

            $marca_produto_id == 646 ||

            $marca_produto_id == 431 ||

            $marca_produto_id == 433 ||

            $marca_produto_id == 600 ||

            $marca_produto_id == 258 ||

            $marca_produto_id == 244 ||

            $marca_produto_id == 300 ||

            $marca_produto_id == 392 ||

            $marca_produto_id == 904 ||

            $marca_produto_id == 775 ||

            $marca_produto_id == 923 ||

            $marca_produto_id == 458 ||

            $marca_produto_id == 46  ||

            $marca_produto_id == 697 ||

            $marca_produto_id == 592 ||

            $marca_produto_id == 259 ||

            $marca_produto_id == 325 ||

            $marca_produto_id == 891 ||

            $marca_produto_id == 222
        ){

            $valor_st  = 0.175 * $preco_compra;

        }

        echo " - ST: ".$valor_st;

        $valor_imposto = $valor_ipi + $valor_st;

        echo " - Valor Imposto: ".$valor_imposto;

        return $valor_imposto;

    }

    
    public function calcular_preco_venda($preco_compra){
        
	    $faixas = array();

        /*$faixas = [
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
        ];*/ // faixas usadas  ate o dia 29-08-2020

        /*$faixas = [
            1 => array(0 , 0.99 , 10, true),
            2 => array(1 , 1.99 , 10, true),
            3 => array(2 , 2.99 , 10, true),
            4 => array(3 , 3.99 , 15, true),
            5 => array(4 , 4.99 , 15, true),
            6 => array(5 , 5.99 , 20, true),
            7 => array(6 , 6.99 , 20, true),
            8 => array(7 , 7.99 , 2.5, false),
            9 => array(8 , 8.99 , 2.5, false),
            10 => array(9 , 9.99 , 2.5, false),
            11 => array(10 , 14.99 , 2.5, false),
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
            24 => array(100 , 124.99 , 1.82, false),
            25 => array(125 , 149.99 , 1.79, false),
            26 => array(150 , 174.99 , 1.77, false),
            27 => array(175 , 199.99 , 1.75, false),
            28 => array(200 , 224.99 , 1.73, false),
            29 => array(225 , 249.99 , 1.71, false),
            30 => array(250 , 299.99 , 1.70, false),
            31 => array(300 , 349.99 , 1.69, false),
            32 => array(350 , 399.99 , 1.69, false),
            33 => array(400 , 449.99 , 1.67, false),
            34 => array(450 , 499.99 , 1.66, false),
            35 => array(500 , 599.99 , 1.65, false),
            36 => array(600 , 699.99 , 1.64, false),
            37 => array(700 , 799.99 , 1.63, false),
            38 => array(800 , 899.99 , 1.62, false),
            39 => array(900 , 999.99 , 1.61, false),
            40 => array(1000 , 1099.99 , 1.60, false),
            41 => array(1100 , 1199.99 , 1.59, false),
            42 => array(1200 , 1299.99 , 1.58, false),
            43 => array(1300 , 1399.99 , 1.57, false),
            44 => array(1400 , 1499.99 , 1.56, false),
            45 => array(1500 , 1999.99 , 1.55, false),
            46 => array(2000 , 2999.99 , 1.54, false),
            47 => array(3000 , 3999.99 , 1.53, false),
            48 => array(4000 , 4999.99 , 1.52, false),
            49 => array(5000 , 100000 , 1.51, false),
            50 => array(100000 , 300000 , 1.50, false),
        ];*/ // FAIXAS USADAS ATE O DIA 23-11-2020

        /*$faixas = [
            1 => array(0 , 0.99 , 10, true),
            2 => array(1 , 1.99 , 10, true),
            3 => array(2 , 2.99 , 10, true),
            4 => array(3 , 3.99 , 15, true),
            5 => array(4 , 4.99 , 15, true),
            6 => array(5 , 5.99 , 20, true),
            7 => array(6 , 6.99 , 20, true),
            8 => array(7 , 7.99 , 2.5, false),
            9 => array(8 , 8.99 , 2.5, false),
            10 => array(9 , 9.99 , 2.5, false),
            11 => array(10 , 14.99 , 2.5, false),
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
            24 => array(100 , 124.99 , 1.82, false),
            25 => array(125 , 149.99 , 1.79, false),
            26 => array(150 , 174.99 , 1.78, false),
            27 => array(175 , 199.99 , 1.77, false),
            28 => array(200 , 224.99 , 1.76, false),
            29 => array(225 , 249.99 , 1.75, false),
            30 => array(250 , 299.99 , 1.74, false),
            31 => array(300 , 349.99 , 1.73, false),
            32 => array(350 , 399.99 , 1.72, false),
            33 => array(400 , 449.99 , 1.71, false),
            34 => array(450 , 499.99 , 1.70, false),
            35 => array(500 , 599.99 , 1.69, false),
            36 => array(600 , 699.99 , 1.68, false),
            37 => array(700 , 799.99 , 1.67, false),
            38 => array(800 , 899.99 , 1.66, false),
            39 => array(900 , 999.99 , 1.65, false),
            40 => array(1000 , 1099.99 , 1.64, false),
            41 => array(1100 , 1199.99 , 1.63, false),
            42 => array(1200 , 1299.99 , 1.62, false),
            43 => array(1300 , 1399.99 , 1.60, false),
            44 => array(1400 , 1499.99 , 1.58, false),
            45 => array(1500 , 1999.99 , 1.57, false),
            46 => array(2000 , 2999.99 , 1.56, false),
            47 => array(3000 , 3999.99 , 1.55, false),
            48 => array(4000 , 4999.99 , 1.55, false),
            49 => array(5000 , 100000 , 1.55, false),
            50 => array(100000 , 300000 , 1.55, false),
        ];*/ // FAIXAS USADAS ATE O DIA 09-12-2020

        /*$faixas = [
            1 => array(0 , 0.99 , 10, true),
            2 => array(1 , 1.99 , 10, true),
            3 => array(2 , 2.99 , 10, true),
            4 => array(3 , 3.99 , 15, true),
            5 => array(4 , 4.99 , 15, true),
            6 => array(5 , 5.99 , 20, true),
            7 => array(6 , 6.99 , 20, true),
            8 => array(7 , 7.99 , 2.5, false),
            9 => array(8 , 8.99 , 2.5, false),
            10 => array(9 , 9.99 , 2.5, false),
            11 => array(10 , 14.99 , 2.5, false),
            12 => array(15 , 19.99 , 2.5, false),
            13 => array(20 , 24.99 , 2.4, false),
            14 => array(25 , 29.99 , 2.3, false),
            15 => array(30 , 34.99 , 2.2, false),
            16 => array(35 , 39.99 , 2.1, false),
            17 => array(40 , 44.99 , 2.0, false),
            18 => array(45 , 49.99 , 1.98, false),
            19 => array(50 , 59.99 , 1.95, false),
            20 => array(60 , 69.99 , 1.90, false),
            21 => array(70 , 79.99 , 1.89, false),
            22 => array(80 , 89.99 , 1.88, false),
            23 => array(90 , 99.99 , 1.87, false),
            24 => array(100 , 124.99 , 1.86, false),
            25 => array(125 , 149.99 , 1.85, false),
            26 => array(150 , 174.99 , 1.84, false),
            27 => array(175 , 199.99 , 1.83, false),
            28 => array(200 , 224.99 , 1.82, false),
            29 => array(225 , 249.99 , 1.81, false),
            30 => array(250 , 299.99 , 1.80, false),
            31 => array(300 , 349.99 , 1.79, false),
            32 => array(350 , 399.99 , 1.78, false),
            33 => array(400 , 449.99 , 1.77, false),
            34 => array(450 , 499.99 , 1.76, false),
            35 => array(500 , 599.99 , 1.75, false),
            36 => array(600 , 699.99 , 1.74, false),
            37 => array(700 , 799.99 , 1.73, false),
            38 => array(800 , 899.99 , 1.72, false),
            39 => array(900 , 999.99 , 1.71, false),
            40 => array(1000 , 1099.99 , 1.70, false),
            41 => array(1100 , 1199.99 , 1.69, false),
            42 => array(1200 , 1299.99 , 1.68, false),
            43 => array(1300 , 1399.99 , 1.67, false),
            44 => array(1400 , 1499.99 , 1.66, false),
            45 => array(1500 , 1999.99 , 1.65, false),
            46 => array(2000 , 2999.99 , 1.64, false),
            47 => array(3000 , 3999.99 , 1.63, false),
            48 => array(4000 , 4999.99 , 1.62, false),
            49 => array(5000 , 100000 , 1.61, false),
            50 => array(100000 , 300000 , 1.60, false),
        ];*/ // FAIXAS USADAS ATE O DIA 21-12-2020

        /* $faixas = [
            1 => array(0 , 0.99 , 10, true),
            2 => array(1 , 1.99 , 10, true),
            3 => array(2 , 2.99 , 10, true),
            4 => array(3 , 3.99 , 15, true),
            5 => array(4 , 4.99 , 15, true),
            6 => array(5 , 5.99 , 20, true),
            7 => array(6 , 6.99 , 20, true),
            8 => array(7 , 7.99 , 2.5, false),
            9 => array(8 , 8.99 , 2.5, false),
            10 => array(9 , 9.99 , 2.5, false),
            11 => array(10 , 14.99 , 2.5, false),
            12 => array(15 , 19.99 , 2.5, false),
            13 => array(20 , 24.99 , 2.4, false),
            14 => array(25 , 29.99 , 2.3, false),
            15 => array(30 , 34.99 , 2.2, false),
            16 => array(35 , 39.99 , 2.1, false),
            17 => array(40 , 44.99 , 2.0, false),
            18 => array(45 , 49.99 , 1.95, false),
            19 => array(50 , 59.99 , 1.95, false),
            20 => array(60 , 69.99 , 1.90, false),
            21 => array(70 , 79.99 , 1.90, false),
            22 => array(80 , 89.99 , 1.90, false),
            23 => array(90 , 99.99 , 1.90, false),
            24 => array(100 , 124.99 , 1.90, false),
            25 => array(125 , 149.99 , 1.90, false),
            26 => array(150 , 174.99 , 1.90, false),
            27 => array(175 , 199.99 , 1.90, false),
            28 => array(200 , 224.99 , 1.90, false),
            29 => array(225 , 249.99 , 1.90, false),
            30 => array(250 , 299.99 , 1.90, false),
            31 => array(300 , 349.99 , 1.89, false),
            32 => array(350 , 399.99 , 1.88, false),
            33 => array(400 , 449.99 , 1.87, false),
            34 => array(450 , 499.99 , 1.86, false),
            35 => array(500 , 599.99 , 1.85, false),
            36 => array(600 , 699.99 , 1.84, false),
            37 => array(700 , 799.99 , 1.83, false),
            38 => array(800 , 899.99 , 1.82, false),
            39 => array(900 , 999.99 , 1.81, false),
            40 => array(1000 , 1099.99 , 1.80, false),
            41 => array(1100 , 1199.99 , 1.79, false),
            42 => array(1200 , 1299.99 , 1.78, false),
            43 => array(1300 , 1399.99 , 1.77, false),
            44 => array(1400 , 1499.99 , 1.76, false),
            45 => array(1500 , 1999.99 , 1.75, false),
            46 => array(2000 , 2999.99 , 1.74, false),
            47 => array(3000 , 3999.99 , 1.73, false),
            48 => array(4000 , 4999.99 , 1.72, false),
            49 => array(5000 , 100000 , 1.71, false),
            50 => array(100000 , 300000 , 1.70, false),
        ];*/ //     faixas usadas ate o di 30-12-2020

        $faixas = [
            1 => array(0 , 0.99 , 10, true),
            2 => array(1 , 1.99 , 10, true),
            3 => array(2 , 2.99 , 10, true),
            4 => array(3 , 3.99 , 15, true),
            5 => array(4 , 4.99 , 15, true),
            6 => array(5 , 5.99 , 20, true),
            7 => array(6 , 6.99 , 20, true),
            8 => array(7 , 7.99 , 2.5, false),
            9 => array(8 , 8.99 , 2.5, false),
            10 => array(9 , 9.99 , 2.5, false),
            11 => array(10 , 14.99 , 2.5, false),
            12 => array(15 , 19.99 , 2.5, false),
            13 => array(20 , 24.99 , 2.4, false),
            14 => array(25 , 29.99 , 2.3, false),
            15 => array(30 , 34.99 , 2.2, false),
            16 => array(35 , 39.99 , 2.1, false),
            17 => array(40 , 44.99 , 2.0, false),
            18 => array(45 , 49.99 , 1.95, false),
            19 => array(50 , 59.99 , 1.95, false),
            20 => array(60 , 69.99 , 1.90, false),
            21 => array(70 , 79.99 , 1.90, false),
            22 => array(80 , 89.99 , 1.90, false),
            23 => array(90 , 99.99 , 1.90, false),
            24 => array(100 , 124.99 , 1.90, false),
            25 => array(125 , 149.99 , 1.90, false),
            26 => array(150 , 174.99 , 1.90, false),
            27 => array(175 , 199.99 , 1.90, false),
            28 => array(200 , 224.99 , 1.90, false),
            29 => array(225 , 249.99 , 1.90, false),
            30 => array(250 , 299.99 , 1.90, false),
            31 => array(300 , 349.99 , 1.89, false),
            32 => array(350 , 399.99 , 1.88, false),
            33 => array(400 , 449.99 , 1.87, false),
            34 => array(450 , 499.99 , 1.86, false),
            35 => array(500 , 599.99 , 1.85, false),
            36 => array(600 , 699.99 , 1.85, false),
            37 => array(700 , 799.99 , 1.84, false),
            38 => array(800 , 899.99 , 1.84, false),
            39 => array(900 , 999.99 , 1.83, false),
            40 => array(1000 , 1099.99 , 1.83, false),
            41 => array(1100 , 1199.99 , 1.82, false),
            42 => array(1200 , 1299.99 , 1.82, false),
            43 => array(1300 , 1399.99 , 1.81, false),
            44 => array(1400 , 1499.99 , 1.81, false),
            45 => array(1500 , 1999.99 , 1.80, false),
            46 => array(2000 , 2999.99 , 1.80, false),
            47 => array(3000 , 3999.99 , 1.80, false),
            48 => array(4000 , 4999.99 , 1.80, false),
            49 => array(5000 , 100000 , 1.80, false),
            50 => array(100000 , 300000 , 1.80, false),
        ];


        foreach ($faixas as $k => $faixa) {

            if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){

                $preco_venda = round(($preco_compra * $faixa[2]),2);

                if ($faixa[3]){

                    $preco_venda = $faixa[2];

                }

                return $preco_venda;

            }

        }
        
        return 999999;
        
    }

}
