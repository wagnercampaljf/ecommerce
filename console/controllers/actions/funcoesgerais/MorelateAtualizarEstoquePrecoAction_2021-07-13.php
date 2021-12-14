<?php

namespace console\controllers\actions\funcoesgerais;

use common\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
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

        //$file = fopen("/var/tmp/morelate_22_06_2021.csv", 'r');
       // $file = fopen("/var/tmp/morelate_23_06_2021.csv", 'r');
        //$file = fopen("/var/tmp/morelate_25_06_2021.csv", 'r');
       // $file = fopen("/var/tmp/morelate_28_06_2021.csv", 'r');
       // $file = fopen("/var/tmp/morelate_29_06_2021.csv", 'r');
        //$file = fopen("/var/tmp/morelate_30_06_2021.csv", 'r');
        //$file = fopen("/var/tmp/morelate_01_07_2021.csv", 'r');
      //$file = fopen("/var/tmp/morelate_02_07_2021.csv", 'r');
       // $file = fopen("/var/tmp/morelate_07_07_2021.csv", 'r');
       // $file = fopen("/var/tmp/morelate_06_07_2021.csv", 'r');
       // $file = fopen("/var/tmp/morelate_07_07_2021.csv", 'r');
       // $file = fopen("/var/tmp/morelate_08_07_2021.csv", 'r');

        $file = fopen("/var/tmp/morelate_12_07_2021.csv", 'r');

        ///////////////////////////////////////////////////////
        //ARQUIVO COM DADOS
        ///////////////////////////////////////////////////////

        while (($line = fgetcsv($file,null,';')) !== false)

        {

            $LinhasArray[] = $line;

            $codigos_produtos[$line[0].".M"] = $line[0];

        }

        fclose($file);

        $log = "/var/tmp/log_morelate_12_07_2021.csv";

        if (file_exists($log)){

            unlink($log);

        }

        $arquivo_log = fopen($log, "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){

            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2];

             if($i<0){continue;}

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

                   if($produto_filial->quantidade <= "1" ){

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

                    if($produto->codigo_fabricante == '041900.M' ){

                        echo " Produto com futo de estoque";

                        $produto_filial->quantidade = 0;

                    }

                    if($produto->codigo_fabricante == '041899.M' ){

                        echo " Produto com futo de estoque";

                        $produto_filial->quantidade = 0;

                    }

                    if($produto->codigo_fabricante == '076343.M' ){

                        echo " Produto com futo de estoque";

                        $produto_filial->quantidade = 0;

                    }

                    if($produto->codigo_fabricante == '073242.M' ){

                        echo " Produto com futo de estoque";

                        $produto_filial->quantidade = 0;

                    }

                    if($produto->codigo_fabricante == '019731.M' ){

                        echo " Produto com futo de estoque";

                        $produto_filial->quantidade = 0;

                    }
                    if($produto->codigo_fabricante == '044335.M' ){

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


        $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

        $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();

        $faixas = [];

        foreach ($markups_detalhe as $markup_detalhe){
            $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

        }


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
