<?php

namespace console\controllers\actions\funcoesgerais;

use frontend\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;

class DibCalcularPrecoVendaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";

        $LinhasArray = Array();

        //Abre o arquivo com as informações


        // $arquivo_origem = '/var/tmp/dib_estoque_preco_21-12-2020';

        //$arquivo_origem = '/var/tmp/dib_estoque_preco_30-12-2020';

        // $arquivo_origem = '/var/tmp/dib_estoque_preco_04-01-2021';

        //$arquivo_origem = '/var/tmp/dib_estoque_preco_03-03-2021';

        $arquivo_origem = '/var/tmp/dib_estoque_preco_08-03-2021';


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


            fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].";");


            //Acrescenta mais duas colunas
            if ($i == 0){

                fwrite($arquivo_destino, "7;8");

                continue;

            }

            if ($i == 0){

                fwrite($arquivo_destino, "PREÇO COMPRA;PREÇO VENDA");

                continue;

            }



            $multiplicador 	= 1;

            $preco_compra   = (float) str_replace(",",".",str_replace(".","",$linhaArray[3]));

            $produto		= Produto::find()->andWhere(['=','codigo_fabricante','D'.$linhaArray[0]])->one();



            if($produto){

                if(!is_null($produto->multiplicador)){

                    if($produto->multiplicador > 1 ){

                        $multiplicador = $produto->multiplicador;

                    }

                }

            }

            $preco_compra = $multiplicador * $preco_compra;



	        if($linhaArray[5] == "239-CAPAS CONFECCAO CHINIL DIB" || $linhaArray[5] == "352-CAPAS CONFECCAO PELUCIA DIB" || $linhaArray[5] == "586-CAPAS CONFECCAO CHINIL PREMIUM" || $linhaArray[5] == "587-CAPAS CONFECCAO CORINO"){

                $preco_compra   = 0.55*$preco_compra;

	        }

	        else{

                $preco_compra   = 0.45*$preco_compra;

	        }

            $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();
            $margem             = $markup_mestre->margem_padrao;
            $e_margem_absoluta  = $markup_mestre->e_margem_absoluta_padrao;


            $preco_venda = $this->calcular_preco_venda($preco_compra, $markup_mestre->id, $markup_mestre->margem_padrao, $markup_mestre->e_margem_absoluta_padrao);



            echo "\n".$i." -  PRECO COMPRA: ".$preco_compra." -  PRECO VENDA: ".$preco_venda;

            echo " - ".$preco_compra;


                fwrite($arquivo_destino, $preco_compra.";".$preco_venda);



            // Verifica se existe produto caixas
            $produto_caixa  = Produto::find()->andWhere(['=','codigo_fabricante','CX.D'.$linhaArray[0]])->one();

            if($produto_caixa){

                $preco_compra       = $produto_caixa->multiplicador * $preco_compra;

                $codigo_fabricante  = 'CX.D'.$linhaArray[0];


                $markup_mestre      = MarkupMestre::find()->orderBy(["id" => SORT_DESC])->one();
                $margem             = $markup_mestre->margem_padrao;
                $e_margem_absoluta  = $markup_mestre->e_margem_absoluta_padrao;


                $preco_venda = $this->calcular_preco_venda($preco_compra, $markup_mestre->id, $markup_mestre->margem_padrao, $markup_mestre->e_margem_absoluta_padrao);


                fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$preco_compra."preço compra".";".$preco_venda."Preço venda");


            }

        }

        // Fecha o arquivo
        fclose($arquivo_destino);
        
        echo "\n\nFIM da rotina de criação preço!";

    }


    public function calcular_preco_venda($preco_compra, $markup_mestre_id, $margem_padrao, $e_margem_absoluta_padrao){

        $markup_detalhe     = MarkupDetalhe::find()->where(" (".$preco_compra." BETWEEN valor_minimo AND valor_maximo) AND (markup_mestre_id = ".$markup_mestre_id.") ")->one();

        if($markup_detalhe){

            $margem             = $markup_detalhe->margem;

            $e_margem_absoluta  = $markup_detalhe->e_margem_absoluta;

        }else{

            $margem             = $margem_padrao;

            $e_margem_absoluta  = $e_margem_absoluta_padrao;

        }

        $preco_venda            = $preco_compra * $margem;

        if($e_margem_absoluta){

            $preco_venda = $margem;

        }

        return $preco_venda;


    }

}
