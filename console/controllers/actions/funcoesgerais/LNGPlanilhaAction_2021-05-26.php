<?php

namespace console\controllers\actions\funcoesgerais;

use common\models\ValorProdutoFilial;
use common\models\Produto;
use function GuzzleHttp\json_decode;
use common\models\ProdutoFilial;

class LNGPlanilhaAction extends Action
{
    public function run(){

        $arquivo_log = fopen("/var/tmp/log_lng_api".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "codigo_fabricante;codigo_original;codigo_montadora;nome;micro_descricao;aplicacao;aplicacao_complementar;mulpplicador;estoque;valor;peso;altura;largura;profundidade;ipi;cest;codigo_barras;imagem;status");

        $produtosArray = Array();
        //$file = fopen("/var/tmp/lista_produtos_lng_api_2020-07-22.csv", 'r');
        //$file = fopen("/var/tmp/lista_produtos_lng_api_2020-10-06.csv", 'r');
	//$file = fopen("/var/tmp/lng_api_produtos_2020-10-21.csv", 'r');
	$file = fopen("/var/tmp/lng_api_produtos_2020-11-03.csv", 'r');
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $produtosArray[] = $line;
            //print_r($line);
        }
        fclose($file);
        
        foreach ($produtosArray as $k => $produto_importado){

            //if(!($k == "5010")){ continue;}
            //if(!(str_replace(" ","",$produto_importado[0]) == "01-010")){ continue;}

            echo "\n".$k." - ".$produto_importado[0];

            $codigo_fabricante = (str_replace(" ","",$produto_importado[0]));
            $codigo_fabricante_peca = "L".str_replace("-","",$codigo_fabricante);
            echo " - ".$codigo_fabricante_peca;

            
            fwrite($arquivo_log, "\n".  $codigo_fabricante_peca.";".
                                        $produto_importado[0].";".
                                        $produto_importado[1].";".
                                        $produto_importado[2].";".
                                        $produto_importado[3].";".
                                        $produto_importado[4].";".
                                        $produto_importado[5].";".
                                        $produto_importado[6].";".
                                        $produto_importado[7].";".
                                        $produto_importado[8].";".
                                        $produto_importado[9].";".
                                        $produto_importado[10].";".
                                        $produto_importado[11].";".
                                        $produto_importado[12].";".
                                        $produto_importado[13].";".
                                        $produto_importado[14].";".
                                        $produto_importado[15].";".
                                        $produto_importado[16].";".
                                        $produto_importado[17].";".
                                        $produto_importado[18].";"
            );

            $produto_peca = Produto::find()->andWhere(['=','codigo_fabricante',$codigo_fabricante_peca])->one();
        
            if($produto_peca){
                
                fwrite($arquivo_log, " - Produto Encontrado Peça");

                $produto_peca->ipi              = $produto_importado[14];
                $produto_peca->cest             = $produto_importado[15];
                $produto_peca->codigo_barras    = $produto_importado[16];
                $produto_peca->produto_condicao_id = 1;
                
                if($produto_importado[1] != ""){
                    $pos = strpos($produto_peca->codigo_similar, $produto_importado[1]);
                    
                    if($pos === false)
                    {
                        $produto_peca->codigo_similar .= " | ".$produto_importado[1];
                        echo " - código_similar não preenchido";
                    }
                    else
                    {
                        echo " - código_similar já preenchido";
                    }
                }

                if($produto_peca->save()){
                    
                    fwrite($arquivo_log, " - Produto Salvo");
                    
                    echo " - produto salvo";

                    echo " - Produto encontrado no PEÇA";

                    $produto_filial = ProdutoFilial::find() ->andWhere(['=','filial_id',60])
                                                            ->andWhere(['=','produto_id',$produto_peca->id])
                                                            ->one();
                    if($produto_filial){
                        
                        fwrite($arquivo_log, " - Estoque encontrado");
                        
                        echo " - Estoque encontrado";

                        $produto_filial->quantidade = $produto_importado[8];
                        if($produto_filial->save()){
                            
                            fwrite($arquivo_log, " - Estoque Salvo");
                            
                            echo " - Estoque atualizado";

                            $preco_compra   = $produto_importado[9];

			    $multiplicador = 1;
			    if($produto_filial->produto->multiplicador != null){
				$multiplicador = $produto_filial->produto->multiplicador;
			    }
			    $preco_compra *= $multiplicador;

                            $ipi            = 0;
                            $st             = $preco_compra * (0.144);
                            if($produto_peca->ipi != null && $produto_peca->ipi != 0){
                                $ipi            = $preco_compra * ($produto_peca->ipi/100);
                            }
                            $preco_compra   += ($ipi + $st);
                            $preco_venda = $this->calcularPrecoVenda($preco_compra);

                            $valor_produto_filial = new ValorProdutoFilial;
                            $valor_produto_filial->produto_filial_id    = $produto_filial->id;
                            $valor_produto_filial->valor                = $preco_venda;
                            $valor_produto_filial->valor_cnpj           = $preco_venda;
                            $valor_produto_filial->valor_compra         = $produto_importado[9];
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
                        else{
                            echo " - Estoque não salvo";
                            
                            fwrite($arquivo_log, " - Estoque Não Salvo");
                        }
                    }
                    else{
                        echo " - Estoque não encontrado";
                        
                        fwrite($arquivo_log, " - Estoque Não Salvo");
                    }
                }
                else{
                    
                    print_r($produto_peca); die;
                    
                    echo " - produto não salvo";
                    
                    fwrite($arquivo_log, " - Produto Não Salvo");
                }
            }
            else{
                
                fwrite($arquivo_log, " - Produto Não Encontrado Peça");
                echo " - Produto Não Encontrado Peça";
                
                continue;
                
                if(str_replace(" ","",$produto_importado[3]) == ""){
                    echo " - PRODTUO SEM NOME";    
                    
                    fwrite($arquivo_log, " - Produto Sem Nome");
                    
                    continue;
                }

                $peso           = 3;
                $altura         = 30;
                $largura        = 30;
                $profundidade   = 30;
                if($produto_importado[10] <> 0 && $produto_importado[10] <> null && $produto_importado[10] <> ""){
                    $peso = $produto_importado[10];
                }
                if($produto_importado[11] <> 0 && $produto_importado[11] <> null && $produto_importado[11] <> ""){
                    $altura = $produto_importado[11];
                }
                if($produto_importado[12] <> 0 && $produto_importado[12] <> null && $produto_importado[12] <> ""){
                    $largura = $produto_importado[12];
                }
                if($produto_importado[13] <> 0 && $produto_importado[13] <> null && $produto_importado[13] <> ""){
                    $profundidade = $produto_importado[13];
                }

                $codigo_global = explode("/",str_replace("  ","",$produto_importado[1]));

                $produto_novo                           = new Produto;
                $produto_novo->nome                     = str_replace("  ","",$produto_importado[3]);
                $produto_novo->aplicacao                = str_replace("  ","",$produto_importado[5]);
                $produto_novo->aplicacao_complementar   = str_replace("  ","",$produto_importado[6]);
                $produto_novo->descricao                = str_replace("  ","",$produto_importado[4]);
                $produto_novo->micro_descricao          = str_replace("  ","",$produto_importado[4]);
                $produto_novo->peso                     = $peso;
                $produto_novo->altura                   = $altura;
                $produto_novo->largura                  = $largura;
                $produto_novo->profundidade             = $profundidade;
                $produto_novo->codigo_barras            = str_replace(" ","",$produto_importado[16]);
                $produto_novo->codigo_fabricante        = "L".str_replace("  ","",str_replace("-","",$produto_importado[0]));
                $produto_novo->codigo_montadora         = str_replace("  ","",$produto_importado[2]);
                $produto_novo->codigo_global            = $codigo_global[0];
                $produto_novo->codigo_similar           = str_replace("  ","",$produto_importado[1]);
                $produto_novo->ipi                      = $produto_importado[14];
                $produto_novo->cest                     = str_replace("  ","",$produto_importado[15]);
                $produto_novo->fabricante_id            = 33;
                $produto_novo->subcategoria_id          = 285;
                $this->slugify($produto_novo);
                //print_r($produto_novo); die;
                if ($produto_novo->save()){
                    echo " - Produto cadastrado";
                    
                    fwrite($arquivo_log, " - Produto Salvo");

                    $produtoFilial              = new ProdutoFilial();
                    $produtoFilial->produto_id  = $produto_novo->id;
                    $produtoFilial->filial_id   = 60;
                    $produtoFilial->quantidade  = 99999;
                    $produtoFilial->envio       = 1;
                    if ($produtoFilial->save()){
                        echo " - ProdutoFilial CRIADO";
                        
                        fwrite($arquivo_log, " - Estoque Salvo");
                        
                        $preco_compra   = $produto_importado[9];
                        $ipi            = 0;
                        $st             = $preco_compra * (0.144);
                        if($produto_novo->ipi != null && $produto_novo->ipi != 0){
                            $ipi            = $preco_compra * ($produto_novo->ipi/100);
                        }
                        $preco_compra   += ($ipi + $st);
                        $preco_venda = $this->calcularPrecoVenda($preco_compra);

                        $valorProdutoFilial                     = New ValorProdutoFilial;
                        $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
                        $valorProdutoFilial->valor              = $preco_venda;
                        $valorProdutoFilial->valor_cnpj         = $preco_venda;
                        $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
                        if ($valorProdutoFilial->save()){
                            echo " - ValorProdutoFilial CRIADO\n";
                            
                            fwrite($arquivo_log, " - Valor Salvo");
                        } else{
                            echo " - ValorProdutoFilial NAO CRIADO\n";
                            
                            fwrite($arquivo_log, " - Valor Não Salvo");
                        }
                    } else{
                        echo " - ProdutoFilial NAO CRIADO\n";
                        
                        fwrite($arquivo_log, " - Estoque Não Salvo");
                    }
                } else{
                    echo " - Produto não cadastrado";
                    
                    fwrite($arquivo_log, " - Produto Não Salvo");
                }
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
            32 => array(350 , 399.99 , 1.68, false),
            33 => array(400 , 449.99 , 1.67, false),
            34 => array(450 , 499.99 , 1.66, false),
            35 => array(500 , 599.99 , 1.65, false),
            36 => array(600 , 699.99 , 1.64, false),
            37 => array(700 , 799.99 , 1.63, false),
            38 => array(800 , 899.99 , 1.62, false),
            39 => array(900 , 999.99 , 1.61, false),
            40 => array(1000 , 1099.99 , 1.6, false),
            41 => array(1100 , 1199.99 , 1.59, false),
            42 => array(1200 , 1299.99 , 1.58, false),
            43 => array(1300 , 1399.99 , 1.57, false),
            44 => array(1400 , 1499.99 , 1.56, false),
            45 => array(1500 , 1999.99 , 1.55, false),
            46 => array(2000 , 2999.99 , 1.54, false),
            47 => array(3000 , 3999.99 , 1.53, false),
            48 => array(4000 , 4999.99 , 1.52, false),
            49 => array(5000 , 100000 , 1.51, false),
            50 => array(100000 , 300000 , 1.5, false),
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

    private function slugify(&$produto_slugfy)
    {
        $text = $produto_slugfy->nome . ' ' . $produto_slugfy->codigo_global;
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        $produto_slugfy->slug = $text;
    }
}
