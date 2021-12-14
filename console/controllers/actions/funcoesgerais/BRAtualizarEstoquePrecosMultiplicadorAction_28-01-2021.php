<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use common\models\MarcaProduto;

class BRAtualizarEstoquePrecosMultiplicadorAction extends Action
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

        $file = fopen("/var/tmp/br_preco_estoque_15-01-2021.csv", 'r');

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
            //Cria um array com dados da planilha e indice sendo o codigo_fabricante
            $codigos_produtos[$line[1]] = $line[1];

        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_br_preco_estoque_15-01-2021.csv")){
            unlink("/var/tmp/log_br_preco_estoque_15-01-2021.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_br_preco_estoque_15-01-2021.csv", "a");
        //Escreve no log
        //fwrite($arquivo_log, "coidgo_fabricante;NCM;codigo_global;valor;valor_compra;valor_venda;produto_filial_id;status_produto;status_estoque;status_preco\n");

        //  ATUALIZAR MULTIPLICADOR

        foreach ($LinhasArray as &$linhaArray){
            $codigo_forncedor = $linhaArray[1]."B";

            if ( $codigo_forncedor == null or  $codigo_forncedor == "" or  $codigo_forncedor == "id"){
                // Escreve no log
                fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";Sem ID\n");
            }
            else {
                $produto  = Produto::find()   ->andWhere(["=","codigo_fabricante",$linhaArray[1].".B"])
                    ->andWhere(["=","fabricante_id",52])
                    ->one();

                if (isset($produto)){
                    echo " - Produto encontrado para codigo - $linhaArray[1].B ";;echo "\n";

                    $produto->multiplicador            = $linhaArray[13];

                   // $produto->nome  =  str_replace("CAIXA","", $produto->nome);

                    //$produto->nome                     = 'CAIXA COM '.$linhaArray[13].' '.$produto->nome;

                    //$produto->aplicacao_complementar   = 'CAIXA COM '. $linhaArray[13].' UNIDADES';

                    $produto->save();

                    fwrite($arquivo_log, $linhaArray[1].";".$linhaArray[13].";Multiplicador Atualizado\n");
                   echo " - Multiplicador Atualizado";
                }
                else {
                    // Escreve no log
                    fwrite($arquivo_log, $linhaArray[1].";".$linhaArray[13].";Multiplicador não Atualizado\n");
                    echo " - Multiplicador não Atualizado";
                }
            }
        }


        //SEGUNDA ANÁLISE

        /*$produtos_filial_br = Produto::find()  ->andWhere(['=','codigo_global', $linhaArray[0]])->all();*/ //Procura produto pelo código "



        //$produtos_filial_br= Produto::find()->andWhere(['=', 'fabricante_id',52])->all();
        // $produtos_filial_br = ProdutoFilial::find()->andWhere(['=','filial_id',72])->all();


        /*$produtos_filial_br  fwrite($arquivo_log, "\n\n\n".'"Codigo_global";"codigo_fabricante";"quantidade";"status"');

          foreach($produtos_filial_br as $k => $produto_filial_br){

              echo "\n".$k." - ".$produto_filial_br->produto->codigo_fabricante;

              //print_r($codigos_produtos); die;

              $produto_encontrado = false;
              if(array_key_exists($produto_filial_br->produto->codigo_fabricante, $codigos_produtos)){
                  $produto_encontrado = true;
              }

              if(!$produto_encontrado){
                  echo " - produto não encontrado na planilha";

                  $quantidade = $produto_filial_br->quantidade;

                  $produto_filial_br->quantidade = 0;
                  if($produto_filial_br->save()){
                      fwrite($arquivo_log, "\n".'"'.$produto_filial_br->produto->codigo_global.'";"'.$produto_filial_br->produto->codigo_fabricante.'";"'.$quantidade.'";"Quantidade zerada"');
                  }
                  else{
                      fwrite($arquivo_log, "\n".'"'.$produto_filial_br->produto->codigo_global.'";"'.$produto_filial_br->produto->codigo_fabricante.'";"'.$quantidade.'";"Quantidade não zerada"');
                  }
              }
          }*/

        
        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








