<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;

class BRCalcularPrecoVendaAction extends Action
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
	];

        $LinhasArray = Array();
        //$arquivo_origem = '/var/tmp/br_estoque_preco_19-12-2019';
        //$arquivo_origem = '/var/tmp/br_22-01-2020';
        //$arquivo_origem = '/var/tmp/br_estoque_preco_18-02-2020';
	    //$arquivo_origem = '/var/tmp/br_15-04-2020';
	    //$arquivo_origem = '/var/tmp/br_estoque_preco_19-05-2020';
	    //$arquivo_origem = '/var/tmp/br_preco_22-06-2020';
	    //$arquivo_origem = '/var/tmp/br_preco_estoque_03-08-2020';
        //$arquivo_origem = '/var/tmp/br_preco_estoque_08-09-2020';
        //$arquivo_origem = '/var/tmp/br_preco_estoque_05-10-2020';
        //$arquivo_origem = '/var/tmp/br_preco_estoque_19-10-2020';
        //$arquivo_origem = '/var/tmp/br_preco_estoque_20-10-2020';
        //$arquivo_origem = '/var/tmp/br_preco_estoque_03-11-2020';

        $arquivo_origem = '/var/tmp/br_preco_estoque_11-11-2020';

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
            
            echo "\n".$i." - ".$linhaArray[1]. " - ".$linhaArray[7];
            
            $novo_codigo_fabricante = $linhaArray[1].".B";
            
            if ($i <= 1){
                //fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";Preço Venda;novo_codigo_fabricante\n");
                fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";Preço Venda;novo_codigo_fabricante\n");
                continue;
            }
            
            /*if ($i >= 50){
                die;
            }*/
         
            //print_r($linhaArray);
            $preco_compra   = (float) $linhaArray[7];




            $this->existe_produto_caixa($linhaArray, $arquivo_destino);
        
	    //continue;
    
            //$preco_compra = $preco_compra * 0.65;
            foreach ($faixas as $k => $faixa) {
                if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                    $preco_venda = round(($preco_compra * $faixa[2]),2);
                    if ($faixa[3]){
                        $preco_venda = $faixa[2];
                    }
		    echo " - ".$preco_venda." - ".$novo_codigo_fabricante;
                    fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$preco_venda.";".$novo_codigo_fabricante."\n");
                    break;
                }
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_destino);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
    
    public function existe_produto_caixa($linha, $arquivo_destino){
        
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

        $produto = Produto::find()  ->andWhere(["=","codigo_fabricante","CX.".$linha[1].".B"])
                                    ->andWhere(["=","fabricante_id",52])
                                    ->one();

        if($produto){
            
            $produto_sem_caixa  = Produto::find()   ->andWhere(["=","codigo_fabricante",$linha[1].".B"])
                                                    ->andWhere(["=","fabricante_id",52])
                                                    ->one();

            if(!$produto_sem_caixa){
                $produto_novo                           = new Produto;
                $produto_novo->nome                     = str_replace("CAIXA ", "", $produto->nome);
                $produto_novo->descricao                = $produto->descricao;
                $produto_novo->peso                     = $produto->peso;
                $produto_novo->altura                   = $produto->altura;
                $produto_novo->profundidade             = $produto->profundidade;
                $produto_novo->largura                  = $produto->largura;
                $produto_novo->codigo_global            = str_replace("CX.","",$produto->codigo_global);
                $produto_novo->codigo_montadora         = $produto->codigo_montadora;
                $produto_novo->codigo_fabricante        = str_replace("CX.","",$produto->codigo_fabricante);
                $produto_novo->fabricante_id            = $produto->fabricante_id;
                $produto_novo->slug                     = str_replace("kit-","",str_replace("caixa-","",$produto->slug));
                $produto_novo->micro_descricao          = $produto->micro_descricao;
                $produto_novo->subcategoria_id          = $produto->subcategoria_id;
                $produto_novo->aplicacao                = $produto->aplicacao;
                $produto_novo->texto_vetor              = $produto->texto_vetor;
                $produto_novo->codigo_similar           = $produto->codigo_similar;
                //$produto_novo->aplicacao_complementar   = $produto->aplicacao_complementar;
                $produto_novo->multiplicador            = 1;
                $produto_novo->video                    = $produto->video;
                $produto_novo->codigo_barras            = $produto->codigo_barras;
                $produto_novo->cest                     = $produto->cest;
                $produto_novo->ipi                      = $produto->ipi;
                if($produto_novo->save()){
                    echo " - produto salvo";
                    
                    $produto_filial             = new ProdutoFilial;
                    $produto_filial->produto_id = $produto_novo->id;
                    $produto_filial->filial_id  = 72;
                    $produto_filial->quantidade = 781;
                    $produto_filial->envio      = 1;
                    if($produto_filial->save()){
                        echo " - produto_filial salvo";
                    }
                    else{
                        echo " - produto_filial não salvo";
                    }
                }
                else{
                    echo " - produto não salvo";
                }
            }
                                                    
            $preco_compra   = (float) $linha[7];
            $preco_compra   = $preco_compra * $produto->multiplicador;
            
            foreach ($faixas as $i => $faixa) {
                if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                    $preco_venda = round(($preco_compra * $faixa[2]),2);
                    if ($faixa[3]){
                        $preco_venda = $faixa[2];
                    }
                    //fwrite($arquivo_destino, $linha[0].";".$linha[1].";".$linha[2].";".$linha[3].";".$linha[4].";".$linha[5].";".$linha[6].";".$linha[7].";".$preco_venda.";"."CX.".$linha[1].".B\n");
		    echo " - ".$preco_venda." - "."CX.".$linha[1].".B";
                    fwrite($arquivo_destino, $linha[0].";".$linha[1].";".$linha[2].";".$linha[3].";".$linha[4].";".$linha[5].";".$linha[6].";".$linha[7].";".$linha[8].";".$linha[9].";".$linha[10].";".$linha[11].";".$linha[12].";".$preco_venda.";"."CX.".$linha[1].".B\n");

                    break;
                }
            }
        }
    }
}



