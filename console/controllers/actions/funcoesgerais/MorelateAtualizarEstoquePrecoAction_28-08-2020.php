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
        //ARQUIVO COM DADOS
        ///////////////////////////////////////////////////////
        
        //$file = fopen("/var/tmp/morelate_21_08_2020.csv", 'r');
        $file = fopen("/var/tmp/morelate_25_08_2020.csv", 'r');
        
        ///////////////////////////////////////////////////////
        //ARQUIVO COM DADOS
        ///////////////////////////////////////////////////////
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
            $codigos_produtos[$line[0].".M"] = $line[0];
        }
        fclose($file);
        
        $log = "/var/tmp/log_morelate_21_08_2020.csv";
        if (file_exists($log)){
            unlink($log);
        }
        $arquivo_log = fopen($log, "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){
            
            $preco_venda = $this->calcular_preco_venda($linhaArray[2]);
            
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";'.$linhaArray[1].';'.$linhaArray[2].';'.$preco_venda.';');
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2];
                        
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[0].".M"])->one();
            
            if ($produto){
                
                echo  " - Produto alterado";
                fwrite($arquivo_log, 'Produto encotrado');
                
                $produto_filial = ProdutoFilial::find()->andWhere(['=','produto_id',$produto->id])
                                                       ->andWhere(['=','filial_id',43])
                                                       ->one();
                
                if($produto_filial){
                    
                    $produto_filial->quantidade = $linhaArray[1];
                    if($produto_filial->save()){
                        echo  " - Estoque alterado";
                        fwrite($arquivo_log, ' - Estoque alterado');
                    }
                    else{
                        echo " - Estoque não alterado";
                        fwrite($arquivo_log, ' - Estoque não alterado');
                    }
                    
                    $valor_produto_filial                       = new ValorProdutoFilial();
                    $valor_produto_filial->produto_filial_id    = $produto_filial->id;
                    $valor_produto_filial->valor                = $preco_venda;
                    $valor_produto_filial->valor_cnpj           = $preco_venda;
                    $valor_produto_filial->valor_compra         = $linhaArray[0];
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
        /*$produtos_morelate = Produto::find()->andWhere(['=', 'fabricante_id', 130])->all();
        
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
                        $status = "Produto não encontrado";
                        
                        if($produto_filial->quantidade <= 3 && $valor_produto_filial->valor <= 500){
                            
                            $produto_filial->quantidade = 0;
                            if($produto_filial->save()){
                                $status .= " - Quantidade zerada";
                            }
                            else{
                                $status .= " - QUantidade não zerada";
                            }
                        }
                        
                        fwrite($arquivo_log, "\n".'"'.$produto_morelate->id.'";"'.$produto_morelate->codigo_fabricante.'";"'.$quantidade.'";"'.$status.'"');
                    }
                }
            }
        }*/
        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
    
    public function calcular_preco_venda($preco_compra){
        
        $faixas = array();
        $faixas = [
            1 => array(0 , 0.99 , 10, true),
            2 => array(1 , 1.99 , 10, true),
            3 => array(2 , 2.99 , 10, true),
            4 => array(3 , 3.99 , 15, true),
            5 => array(4 , 4.99 , 15, true),
            6 => array(5 , 5.99 , 15, true),
            7 => array(6 , 6.99 , 15, true),
            8 => array(7 , 7.99 , 3, false),
            9 => array(8 , 8.99 , 2.5, false),
            10 => array(9 , 9.99 , 2.5, false),
            11 => array(10 , 14.99 , 2.5, false),
            12 => array(15 , 19.99 , 2.5, false),
            13 => array(20 , 24.99 , 2.5, false),
            14 => array(25 , 29.99 , 2.5, false),
            15 => array(30 , 34.99 , 2.5, false),
            16 => array(35 , 39.99 , 2.4, false),
            17 => array(40 , 44.99 , 2.3, false),
            18 => array(45 , 49.99 , 2.2, false),
            19 => array(50 , 59.99 , 2.1, false),
            20 => array(60 , 69.99 , 2, false),
            21 => array(70 , 79.99 , 1.95, false),
            22 => array(80 , 89.99 , 1.95, false),
            23 => array(90 , 99.99 , 1.95, false),
            24 => array(100 , 124.99 , 1.78, false),
            25 => array(125 , 149.99 , 1.78, false),
            26 => array(150 , 174.99 , 1.78, false),
            27 => array(175 , 199.99 , 1.78, false),
            28 => array(200 , 224.99 , 1.75, false),
            29 => array(225 , 249.99 , 1.75, false),
            30 => array(250 , 299.99 , 1.75, false),
            31 => array(300 , 349.99 , 1.72, false),
            32 => array(350 , 399.99 , 1.72, false),
            33 => array(400 , 449.99 , 1.69, false),
            34 => array(450 , 499.99 , 1.69, false),
            35 => array(500 , 599.99 , 1.58, false),
            36 => array(600 , 699.99 , 1.55, false),
            37 => array(700 , 799.99 , 1.52, false),
            38 => array(800 , 899.99 , 1.5, false),
            39 => array(900 , 999.99 , 1.5, false),
            40 => array(1000 , 1099.99 , 1.5, false),
            41 => array(1100 , 1199.99 , 1.49, false),
            42 => array(1200 , 1299.99 , 1.48, false),
            43 => array(1300 , 1399.99 , 1.47, false),
            44 => array(1400 , 1499.99 , 1.46, false),
            45 => array(1500 , 1999.99 , 1.45, false),
            46 => array(2000 , 2999.99 , 1.44, false),
            47 => array(3000 , 3999.99 , 1.43, false),
            48 => array(4000 , 4999.99 , 1.42, false),
            49 => array(5000 , 100000 , 1.41, false),
            50 => array(100000 , 300000 , 1.41, false),
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







