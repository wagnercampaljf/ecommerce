<?php

namespace console\controllers\actions\funcoesgerais;

use common\models\ValorProdutoFilial;
use common\models\Produto;
use function GuzzleHttp\json_decode;
use common\models\ProdutoFilial;

class LNGRecalcularPrecosAction extends Action
{
    public function run(){

        $arquivo_log = fopen("/var/tmp/log_lng_recalcular_preco_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "codigo_fabricante;codigo_original;codigo_montadora;nome;micro_descricao;aplicacao;aplicacao_complementar;mulpplicador;estoque;valor;peso;altura;largura;profundidade;ipi;cest;codigo_barras;imagem;status");

        $produto_filiais = ProdutoFilial::find()->andWhere(['=','filial_id',60])
						->andWhere(['=', 'produto_id', 8914])
                                                ->orderBy('id')
                                                ->all();
        
        foreach ($produto_filiais as $k => $produto_filial){
            
            echo "\n".$k." - ".$produto_filial->produto->codigo_fabricante;
            
            $ultimo_valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id',$produto_filial->id])
                                                                     ->orderBy(['dt_inicio' => SORT_DESC])
                                                                     ->one();

	    if(is_null($ultimo_valor_produto_filial->valor_compra) || $ultimo_valor_produto_filial->valor_compra == 0){
		continue;
	    }

	    $multiplicador = ($produto_filial->produto->multiplicador <= 1 || is_null($produto_filial->produto->multiplicador)) ? 1 : $produto_filial->produto->multiplicador;

            $preco_compra   = $ultimo_valor_produto_filial->valor_compra * $multiplicador;
            $ipi            = 0;
            $st             = $preco_compra * (0.144);
            if($produto_filial->produto->ipi != null && $produto_filial->produto->ipi != 0){
                $ipi            = $preco_compra * ($produto_filial->produto->ipi/100);
            }
            $preco_compra   += ($ipi + $st);
            $preco_venda = $this->calcularPrecoVenda($preco_compra);
//echo "\nIPI: ".$ipi."\nST: ".$st."\nPreco Compra: ".$preco_compra."\nPreco Venda:".$preco_venda; die;
            $valor_produto_filial = new ValorProdutoFilial;
            $valor_produto_filial->produto_filial_id    = $produto_filial->id;
            $valor_produto_filial->valor                = $preco_venda;
            $valor_produto_filial->valor_cnpj           = $preco_venda;
            $valor_produto_filial->valor_compra         = $ultimo_valor_produto_filial->valor_compra;
            $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
            $valor_produto_filial->promocao             = false;
            if($valor_produto_filial->save()){
                echo " - Preço criado";
                
                fwrite($arquivo_log, " - Valor Salvo");
            }
            else{
                echo " - Preço não criado";
                
                fwrite($arquivo_log, " - Valor Não Salvo");
            }
        }
        
        fclose($arquivo_log);
    }

    public function calcularPrecoVenda($preco_compra){

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

        //$preco_compra = $preco_compra * 0.65;
        foreach ($faixas as $k => $faixa) {
            if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                $preco_venda = round(($preco_compra * $faixa[2]),2);
                if ($faixa[3]){
                    $preco_venda = $faixa[2];
                }

                return $preco_venda;
            }
        }
    }
}
