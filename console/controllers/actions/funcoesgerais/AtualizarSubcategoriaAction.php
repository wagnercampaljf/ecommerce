<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use common\models\MarcaProduto;

class AtualizarSubcategoriaAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";


        $codigos_produtos = array();
        
        $LinhasArray = Array();
        //$file = fopen("/var/tmp/br_estoque_preco_18-02-2020_precificado.csv", 'r'); //Abre arquivo com preços para subir
	    //$file = fopen("/var/tmp/br_18-02-2020_precificado.csv", 'r');
	    //$file = fopen("/var/tmp/br_15-04-2020_precificado.csv", 'r');
	    //$file = fopen("/var/tmp/br_estoque_preco_19-05-2020_precificado.csv", 'r');
	    //$file = fopen("/var/tmp/br_preco_22-06-2020_precificado.csv", 'r');
        //$file = fopen("/var/tmp/br_preco_estoque_03-08-2020_precificado.csv", 'r');
        //$file = fopen("/var/tmp/br_preco_estoque_08-09-2020_precificado.csv", 'r');
        // $file = fopen("/var/tmp/br_preco_estoque_19-10-2020_precificado.csv", 'r');
        // $file = fopen("/var/tmp/br_preco_estoque_20-10-2020_precificado.csv", 'r');
        //$file = fopen("/var/tmp/br_preco_estoque_11-11-2020_precificado.csv", 'r');
        //    $file = fopen("/var/tmp/br_preco_estoque_14-12-2020_precificado.csv", 'r');

        $file = fopen("/var/tmp/produtos_Mercado_Envios.csv", 'r');

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
            //Cria um array com dados da planilha e indice sendo o codigo_fabricante
            $codigos_produtos[$line[1]] = $line[1];

        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_produtos_Mercado_Envios.csv")){
            unlink("/var/tmp/log_produtos_Mercado_Envios.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_produtos_Mercado_Envios.csv", "a");
        //Escreve no log
        //fwrite($arquivo_log, "coidgo_fabricante;NCM;codigo_global;valor;valor_compra;valor_venda;produto_filial_id;status_produto;status_estoque;status_preco\n");

        //  ATUALIZAR MULTIPLICADOR

        foreach ($LinhasArray as &$linhaArray){


            $codigo_global = $linhaArray[0];

            if ( $codigo_global == null or  $codigo_global == "" or  $codigo_global == "id"){
                // Escreve no log

                fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[2].";Sem ID\n");
            }
            else {
                $produto  = Produto::find()   ->andWhere(["=","codigo_global",$linhaArray[0]])
                   // ->andWhere(["=","fabricante_id",52])
                    ->one();

                if (isset($produto)){
                    echo " - Produto encontrado para codigo - $linhaArray[0] ";;echo "\n";

                    $produto->subcategoria_id           = 276;



                    //$produto->nome  =  str_replace("CAIXA","", $produto->nome);

                    //$produto->nome                     = 'CAIXA COM '.$linhaArray[13].' '.$produto->nome;

                    //$produto->aplicacao_complementar   = 'CAIXA COM '. $linhaArray[13].' UNIDADES';

                    $produto->save();

                    fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[2].";Categoria Atualizado\n");
                   echo " - Categoria Atualizada";
                }
                else {
                    // Escreve no log
                    fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[2].";Categoria não Atualizado\n");
                    echo " - Categoria não Atualizado";
                }
            }
        }

        
        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








