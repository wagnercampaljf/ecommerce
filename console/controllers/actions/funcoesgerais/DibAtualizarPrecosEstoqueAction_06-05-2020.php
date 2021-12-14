<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class DibAtualizarPrecosEstoqueAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        $codigos_produtos = array();
        $LinhasArray = Array();
        //$file = fopen("/var/tmp/dib_estoque_preco_13-05-20_precificado.csv", 'r');
        //$file = fopen("/var/tmp/dib_estoque_preco_04-06-2020_precificado.csv", 'r');
        //$file = fopen("/var/tmp/dib_estoque_preco_15-06-2020_antenas_par_precificado.csv", 'r');
        //$file = fopen("/var/tmp/dib_estoque_preco_20-07-2020_precificado.csv", 'r');
        //$file = fopen("/var/tmp/dib_estoque_preco_11-08-2020_precificado.csv", 'r');
        //$file = fopen("/var/tmp/dib_estoque_preco_28-08-2020_precificado.csv", 'r');



        $file = fopen("/var/tmp/dib_estoque_preco_28-09-2020_precificado.csv", 'r');

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
            $codigos_produtos[$line[0]] = $line[0];
        }
        fclose($file);

        $log = "/var/tmp/log_dib_estoque_preco_28-09-2020_precificado.csv";
        if (file_exists($log)){
            unlink($log);
        }

        $arquivo_log = fopen($log, "a");
        // Escreve no log
        //fwrite($arquivo_log,"Código;Descricao;Un;Bruto;Original;Fabrica;Grupo;Saldo;Especificações;Peso Bruto;Altura;Largura;Comprimento;NCM;CEST;%IPI;status produto;status estoque\n");
        //fwrite($arquivo_log,"Código;Descricao;Un;Bruto;Grupo;Estoque;preco_compra;preco_venda;status produto;status estoque;status_preço");

        foreach ($LinhasArray as $i => &$linhaArray){

            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[5]." - ".$linhaArray[3]." - ".$linhaArray[6]." - ".$linhaArray[7];
            //continue;



            //fwrite($arquivo_log, $linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8].';'.$linhaArray[9].';'.$linhaArray[10].';'.$linhaArray[11].$linhaArray[12].';'.';'.$linhaArray[13].';'.$linhaArray[14].';'.$linhaArray[15].';'.$linhaArray[16]);
            //fwrite($arquivo_log, "\n".$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7]);
            // fwrite($arquivo_log, "\n".$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8]);

            fwrite($arquivo_log, "\n".$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8].';'.$linhaArray[9].';'.$linhaArray[10]);



            if ($i <= 1){
                fwrite($arquivo_log, ";STATUS");
                continue;
            }

            $codigo_fabricante = (!(strpos($linhaArray[0],"CX.") === false)) ? $linhaArray[0] : 'D'.$linhaArray[0];
            
            $produto = Produto::find()  ->andWhere(['like','codigo_fabricante', $codigo_fabricante])
                                        //->andWhere(['not like','codigo_fabricante', 'CX.D'.$linhaArray[0]])
                                        ->one();
                                        
            if ($produto){
                
                echo " - Produto encontrado";
                fwrite($arquivo_log, ";Produto encontrado;");
                
                $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',97])
                                                        ->andWhere(['=', 'produto_id', $produto->id])
                                                        ->one();
                if ($produtoFilial) {
                    
                    echo " - ".$produtoFilial->id;
                    fwrite($arquivo_log, ";Estoque encontrado");
                    
                    $quantidade = $linhaArray[5];
                    if($linhaArray[4] == "239-CAPAS CONFECCAO CHINIL DIB" || $linhaArray[4] == "352-CAPAS CONFECCAO PELUCIA DIB" || $linhaArray[4] == "586-CAPAS CONFECCAO CHINIL PREMIUM" || $linhaArray[4] == "587-CAPAS CONFECCAO CORINO"){
                        $quantidade = 991;
                    }
                    
                    $nome = $linhaArray[1];
                    if ((!(strpos($nome,"CAPA PORCA") === false)) && (strpos($linhaArray[0],"CX.") === false)){
                        $quantidade = 0;
                        echo " - CAPA";
                    }
                    
                    $produtoFilial->quantidade  = $quantidade;
                    if ($produtoFilial->save()){
                        echo " - Estoque alterado";
                        fwrite($arquivo_log, ";Estoque alterado");
                    }
                    else{
                        echo " - Estoque não alterado";
                        fwrite($arquivo_log, ";Estoque não alterado");
                    }
                    
                    $valor_produto_filial = new ValorProdutoFilial;
                    $valor_produto_filial->produto_filial_id    = $produtoFilial->id;
                    $valor_produto_filial->valor                = $linhaArray[10];
                    $valor_produto_filial->valor_cnpj           = $linhaArray[10];
                    $valor_produto_filial->valor_compra         = $linhaArray[9];
                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                    $valor_produto_filial->promocao             = false;
                    if($valor_produto_filial->save()){
                        echo " - Preço criado";
                        fwrite($arquivo_log, ";Preço criado");
                    }
                    else{
                        echo " - Preço não criado";
                        fwrite($arquivo_log, ";Preço não criado");
                    }
                }
                else{
                    echo " - Estoque não encontrado";
                    fwrite($arquivo_log, ";Estoque não encontrado");
                }
            }
            else{
                echo " - Produto não encontrado";
                fwrite($arquivo_log, ";Produto não encontrado");
            }
        }




        //$produtos_filial_br= Produto::find()->andWhere(['=', 'fabricante_id',52])->all();
        $produtos_filial_dib = ProdutoFilial::find()->andWhere(['=','filial_id',97])->all();


        fwrite($arquivo_log, "\n\n\n".'"Codigo_global";"codigo_fabricante";"quantidade";"status"');

        foreach($produtos_filial_dib as $k => $produto_filial_dib){

            echo "\n".$k." - ".$produto_filial_dib->produto->codigo_fabricante;

            //print_r($codigos_produtos); die;

            $produto_encontrado = false;
            if(array_key_exists($produto_filial_dib->produto->codigo_fabricante, $codigos_produtos)){
                $produto_encontrado = true;
            }

            if(!$produto_encontrado){
                echo " - produto não encontrado na planilha";

                $quantidade = $produto_filial_dib->quantidade;

                $produto_filial_dib->quantidade = 0;
                if($produto_filial_dib->save()){
                    fwrite($arquivo_log, "\n".'"'.$produto_filial_dib->produto->codigo_global.'";"'.$produto_filial_dib->produto->codigo_fabricante.'";"'.$quantidade.'";"Quantidade zerada"');
                }
                else{
                    fwrite($arquivo_log, "\n".'"'.$produto_filial_dib->produto->codigo_global.'";"'.$produto_filial_dib->produto->codigo_fabricante.'";"'.$quantidade.'";"Quantidade não zerada"');
                }
            }
        }



        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}







