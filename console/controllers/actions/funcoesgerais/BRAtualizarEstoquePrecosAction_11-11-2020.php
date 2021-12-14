<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use common\models\MarcaProduto;

class BRAtualizarEstoquePrecosAction extends Action
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

        $file = fopen("/var/tmp/br_preco_estoque_03-11-2020_precificado.csv", 'r');

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
            //Cria um array com dados da planilha e indice sendo o codigo_fabricante
            $codigos_produtos[$line[14]] = $line[14];

        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_br_preco_estoque_03-11-2020_precificado.csv")){
            unlink("/var/tmp/log_br_preco_estoque_03-11-2020_precificado.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_br_preco_estoque_03-11-2020_precificado.csv", "a");
        //Escreve no log
        //fwrite($arquivo_log, "coidgo_fabricante;NCM;codigo_global;valor;valor_compra;valor_venda;produto_filial_id;status_produto;status_estoque;status_preco\n");
        
        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir

            //Coloca os dados da planilha de preços no log
            //fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";");
	    fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8].'";"'.$linhaArray[9].'";"'.$linhaArray[10].'";"'.$linhaArray[11].'";"'.$linhaArray[12].'";"'.$linhaArray[13].'";"'.$linhaArray[14].'";');

            if ($i <= 1){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                fwrite($arquivo_log, "status");
                continue;
            }

	        //if ($i >= 50){
            //    die;
            //}
            
            echo "\n".$i." - ".$linhaArray[14]." - ".$linhaArray[7]." - ".$linhaArray[13]." - ".$linhaArray[8]; //Exibe no console(Terminal) as informações dos preços durante o processamento
            
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[14]])
                                        ->andWhere(['=','fabricante_id', 52])
                                        ->one(); //Procura produto pelo código do fabricante
            
            if ($produto){ //Se encontrar o produto, processa o preço
                
                echo " - encontrado"; //Escreva no termina l
                fwrite($arquivo_log, 'Produto encontrado'); //Escreve no Log que encontrou o produto
                
                //$produto->codigo_barras     = $linhaArray[12];
                //$produto->codigo_montadora  = $linhaArray[6];
                //if($produto->save()){
                //    echo " - ean e ncm atualizada";
                //    fwrite($arquivo_log, " - ean e ncm atualizada");
                //}
                //else{
                //    echo " - ean e ncm não atualizada";
                //    fwrite($arquivo_log, " - ean e ncm não atualizada");
                //}
                
                $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',72])
                                                        ->andWhere(['=', 'produto_id', $produto->id])
                                                        ->one(); //Procura o estoque do produto
                if ($produtoFilial) {//Se encontrar estoque, processa
                    echo " - ".$produtoFilial->id; //Mostra o id do estoque no terminal
                    fwrite($arquivo_log, ' - Estoque encontrado'); //Escreve no log que encontrou o estoque




                    $preco_venda = $linhaArray[13];
                    $preco_compra = $linhaArray[7];
                    
		             $quantidade = $linhaArray[8];
                    // echo "\n\n";var_dump($quantidade); echo "\n\n";

		            if($quantidade == "0" && $preco_venda <= 100 ){
                        $quantidade = 0;
                        // $quantidade = 90;


                        $preco_venda = $preco_venda * 2;


                    }
                    else if ($quantidade == "0" && $preco_venda >= 101){
                        $quantidade = 0;
                        //  $quantidade = 90;

                        $preco_venda = $preco_venda * 1.3;


                    }else {
                        $quantidade = 781;
                    }
                    
                    if($linhaArray[7] == "0.00" || $linhaArray[3] == "0"){
                        $quantidade = 0;
                    }

                    echo " - Quantidade: ".$quantidade;
                    $produtoFilial->quantidade = $quantidade;
                    if($produtoFilial->save()){
                        echo " - quantidade atualizada";
                        fwrite($arquivo_log, " - quantidade atualizada");
                    }
                    else{
                        echo " - quantidade não atualizada";
                        fwrite($arquivo_log, " - quantidade não atualizada");
                    }


                    //Verifica se o valor a ser adicionado É igual ao anterior, se for, nÃo adiciona o registro novo;
                     $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produtoFilial->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();
                    if($preco_venda == $valor_produto_filial->valor){
                        echo " - mesmo valor";
                        continue;
                    }


                    $valor_produto_filial = new ValorProdutoFilial;
                    $valor_produto_filial->produto_filial_id    = $produtoFilial->id;

                    $valor_produto_filial->valor                = $preco_venda;
                    $valor_produto_filial->valor_cnpj           = $preco_venda;
                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                    $valor_produto_filial->promocao             = false;
                    $valor_produto_filial->valor_compra         = $preco_compra;
                    if($valor_produto_filial->save()){
                        // print_r($valor_produto_filial);
                        echo " - Preço atualizado";
                        fwrite($arquivo_log, ' - Preço atualizado');
                    }
                    else{
                        //print_r($valor_produto_filial);
                        echo " - Preço não atualizado";
                        fwrite($arquivo_log, ' - Preço Não atualizado');
                    }
                }
                else{
                    echo " - Estoque não encontrado";
                    fwrite($arquivo_log, ' - Estoque Não encontrado');
                }
            }
            else{
                echo " - Não encontrado";
                fwrite($arquivo_log, 'Produto Não encontrado');
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








