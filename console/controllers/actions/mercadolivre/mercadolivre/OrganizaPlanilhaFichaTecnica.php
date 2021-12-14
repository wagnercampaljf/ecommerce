<?php

namespace console\controllers\actions\mercadolivre;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use common\models\MarcaProduto;

class OrganizaPlanilhaFichaTecnicaAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen("/var/tmp/br_estoque_preco_18-02-2020_precificado.csv", 'r'); //Abre arquivo com preços para subir
	    //$file = fopen("/var/tmp/br_18-02-2020_precificado.csv", 'r');
	    //$file = fopen("/var/tmp/br_15-04-2020_precificado.csv", 'r');
	    //$file = fopen("/var/tmp/br_estoque_preco_19-05-2020_precificado.csv", 'r');
	    //$file = fopen("/var/tmp/br_preco_22-06-2020_precificado.csv", 'r');
        //$file = fopen("/var/tmp/br_preco_estoque_03-08-2020_precificado.csv", 'r');
        //$file = fopen("/var/tmp/br_preco_estoque_08-09-2020_precificado.csv", 'r');

        $file = fopen("/var/tmp/br_preco_estoque_16-09-2020_precificado.csv", 'r');

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_br_preco_estoque_16-09-2020_precificado.csv")){
            unlink("/var/tmp/log_br_preco_estoque_16-09-2020_precificado.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_br_preco_estoque_16-09-2020_precificado.csv", "a");
        //Escreve no log
        //fwrite($arquivo_log, "coidgo_fabricante;NCM;codigo_global;valor;valor_compra;valor_venda;produto_filial_id;status_produto;status_estoque;status_preco\n");
        
        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as informações de preços a subir


	        fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"');

                if ($i <= 1){
                fwrite($arquivo_log, "status");
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[14]." - ".$linhaArray[7]." - ".$linhaArray[13]." - ".$linhaArray[8]; //Exibe no console(Terminal) as informações dos preços durante o processamento






                /*$produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[14]])
                                            ->andWhere(['=','fabricante_id', 52])
                                            ->one(); //Procura produto pelo código do fabricante "VA"

                if ($produto){ //Se encontrar o produto, processa o preço

                    echo " - encontrado"; //Escreva no termina l
                    fwrite($arquivo_log, 'Produto encontrado'); //Escreve no Log que encontrou o produto

                    /*$produto->codigo_barras     = $linhaArray[12];
                    $produto->codigo_montadora  = $linhaArray[6];
                    if($produto->save()){
                        echo " - ean e ncm atualizada";
                        fwrite($arquivo_log, " - ean e ncm atualizada");
                    }
                    else{
                        echo " - ean e ncm não atualizada";
                        fwrite($arquivo_log, " - ean e ncm não atualizada");
                    }
                
                $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',72])
                                                        ->andWhere(['=', 'produto_id', $produto->id])
                                                        ->one(); //Procura o estoque do produto na loja Vannucci
                if ($produtoFilial) {//Se encontrar estoque, processa
                    echo " - ".$produtoFilial->id; //Mostra o id do estoque no terminal
                    fwrite($arquivo_log, ' - Estoque encontrado'); //Escreve no log que encontrou o estoque

		    $produto                    = Produto::find()->andWhere(['=','id',$produtoFilial->produto_id])->one();
                    //$produto->codigo_barras      = $linhaArray[8];
                    //$produto->codigo_montadora   = $linhaArray[6];

		    /*$marca = $linhaArray[9];
		    if($marca != ""){
			if($marca == "BRC" || $marca == "BR COMPANY" || $marca == "BRCOMPANY"){
				$marca = "OPT DIESEL";
			}

			$marca_produto = MarcaProduto::find()->andWhere(['=','nome',$marca])->one();
			if($marca_produto){
				$produto->marca_produto_id = $marca_produto->id;
				echo " - Marca encontrada: ".$marca_produto->nome;
			}
			else{
				$marca_produto_novo 		= new MarcaProduto;
				$marca_produto_novo->nome 	= $marca;
				if($marca_produto_novo->save()){
					echo " - Marca nova: ".$marca_produto_novo->nome;
					$produto->marca_produto_id = $marca_produto_novo->id;
				}
				else{
					echo " - Marca não criada";
				}
			}
		    }

		    if($produto->save()){
			" - Produto salvo";
		    }
		    else{
			" - Produto não salvo";
		    }

                    $preco_venda = $linhaArray[13];
                    $preco_compra = $linhaArray[7];
                    
		    $quantidade = $linhaArray[8];
                //echo "\n\n";var_dump($quantidade); echo "\n\n";
		    if($quantidade == "0"){
                        $quantidade = 90;
                        $preco_venda = $preco_venda * 1.1;
                    }
                    else{
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
                    
                    $valor_produto_filial = new ValorProdutoFilial;
                    $valor_produto_filial->produto_filial_id    = $produtoFilial->id;
                    $valor_produto_filial->valor                = $preco_venda;
                    $valor_produto_filial->valor_cnpj           = $preco_venda;
                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                    $valor_produto_filial->promocao             = false;
                    $valor_produto_filial->valor_compra         = $preco_compra;
                    if($valor_produto_filial->save()){
                        echo " - Preço atualizado";
                        fwrite($arquivo_log, ' - Preço atualizado');
                    }
                    else{
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
        }*/
        
        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
        }
    }
}








