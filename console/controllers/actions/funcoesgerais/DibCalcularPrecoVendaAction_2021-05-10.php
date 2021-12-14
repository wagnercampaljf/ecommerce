<?php

namespace console\controllers\actions\funcoesgerais;

use common\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;

class DibCalcularPrecoVendaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $faixas = array();



        $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

        $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();

        $faixas = [];

        foreach ($markups_detalhe as $markup_detalhe){
            $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

        }

        //print_r($faixas);

        $LinhasArray = Array();

        //Abre o arquivo com as informações

        //$arquivo_origem = '/var/tmp/dib_estoque_preco_09-02-2021';

        //$arquivo_origem = '/var/tmp/dib_estoque_preco_15-02-2021';

        //$arquivo_origem = '/var/tmp/dib_estoque_preco_15-03-2021';

        //$arquivo_origem = '/var/tmp/dib_estoque_preco_22-03-2021';

        //$arquivo_origem = '/var/tmp/dib_estoque_preco_05-04-2021';

        //$arquivo_origem = '/var/tmp/dib_estoque_preco_12-04-2021';

        //$arquivo_origem = '/var/tmp/dib_estoque_preco_19-04-2021';

        //$arquivo_origem = '/var/tmp/dib_estoque_preco_26-04-2021';

        $arquivo_origem = '/var/tmp/dib_estoque_preco_03-04-2021';





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

        //Percorre todas a linhas da planilha
        foreach ($LinhasArray as $i => &$linhaArray){
            
            $codigo_fabricante = $linhaArray[0];

            //fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].";");

            fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].";");

            /*if ($i == 0){
                fwrite($arquivo_destino, "8;9");
                continue;
            }*/

            //Acrescenta mais duas colunas
            if ($i == 0){

                fwrite($arquivo_destino, "7;8");

                continue;

            }

            if ($i == 0){

                fwrite($arquivo_destino, "PREÇO COMPRA;PREÇO VENDA");

                continue;

            }

            // Preco de compra
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

	        if($linhaArray[5] == "239-CAPAS CONFECCAO CHINIL DIB" || $linhaArray[5] == "352-CAPAS CONFECCAO PELUCIA DIB" || $linhaArray[5] == "586-CAPAS CONFECCAO CHINIL PREMIUM" || $linhaArray[5] == "587-CAPAS CONFECCAO CORINO"){

                $preco_compra   = 0.55*$preco_compra;

	        }

	        else{

                $preco_compra   = 0.45*$preco_compra;

	        }

	        //$preco_compra   = 0.45*$preco_compra;

            echo " - ".$preco_compra;
            
            //$preco_compra = $preco_compra * 0.65;

            // Percorre as faixas e os preços da planilha

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

            // Verifica se existe produto caixas
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

                       fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$preco_compra.";".$preco_venda);

                        //fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$preco_compra.";".$preco_venda);

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
