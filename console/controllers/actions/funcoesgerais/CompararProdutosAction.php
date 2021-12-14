<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class CompararProdutosAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de comparação de produtos: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/PlanilhaLNG.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_comparar_produtos.csv")){
            unlink("/var/tmp/log_comparar_produtos.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_comparar_produtos.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;status;nome;nome_site;descricao;descricao_site;peso;peso_site;altura;altura_site;largura;largura_site;profundidade;profundidade_site;codigo_global;codigo_global_site;codigo_montadora;codigo_montadora_site;codigo_fabricante;codigo_fabricante_site;fabricante_id;fabricante_id_site;slug;slug_site;micro_descricao;micro_descricao_site;subcategoria_id;subcategoria_id_site;aplicacao;aplicacao_site;texto_vetor;texto_vetor_site;codigo_similar;codigo_similar_site;aplicacao_complementar;aplicacao_complementar_site;valor;valor_site;quantidade;quantidade_site\n");
        
        foreach ($LinhasArray as &$linhaArray){
            $codigo_global = str_replace(' ','',$linhaArray[7]);
            
            if ($codigo_global == null or $codigo_global == "" or $codigo_global == "codigo_global"){
                // Escreve no log
                fwrite($arquivo_log, ";Sem codigo_global\n");
            }
            else {
                $produto = Produto::find()->andWhere(['=','codigo_global',$codigo_global])->one();

                if (isset($produto)){
                    
                    echo "Produto encontrado para código - "; print_r($codigo_global); echo " - ID - "; print_r($produto->id); echo "\n";
                    
                    $produtoFilial = ProdutoFilial::find()->andWhere(['=', 'filial_id', 60])
                                                           ->andWhere(['=', 'produto_id', $produto->id])
                                                           ->one();
                   
                    $valorProduto   = null;
                    $quantidade     = null;
                    if (isset($produtoFilial)){
                        $quantidade = $produtoFilial->quantidade;
                        
                        $valorProdutoFilial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id',$produtoFilial->id])
                                                                        ->orderBy(['id' => SORT_DESC])
                                                                        ->one();                        
                        if(isset($valorProdutoFilial)){
                            $valorProduto = $valorProdutoFilial->valor;
                        }
                        
                    }
                    
                    //print_r($produto);
                    //print_r($produtoFilial);
                    //print_r($linhaArray);
                    
                    // Escreve no log
		    fwrite($arquivo_log, $produto->id.";"."Produto encontrato".";".$linhaArray[0].";".$produto->nome.";".$linhaArray[1].";".$produto->descricao.";".$linhaArray[2].";".$produto->peso.";".$linhaArray[3].";".$produto->altura.";".$linhaArray[4].";".$produto->largura.";".$linhaArray[5].";".$produto->profundidade.";".$linhaArray[7].";".$produto->codigo_global.";".$linhaArray[8].";".$produto->codigo_montadora.";".$linhaArray[9].";".$produto->codigo_fabricante.";".$linhaArray[10].";".$produto->fabricante_id.";".$linhaArray[11].";".$produto->slug.";".$linhaArray[12].";".$produto->micro_descricao.";".$linhaArray[13].";".$produto->subcategoria_id.";".$linhaArray[14].";".$produto->aplicacao.";".$linhaArray[15].";".$produto->texto_vetor.";".$linhaArray[16].";".$produto->codigo_similar.";".$linhaArray[17].";".$produto->aplicacao_complementar.";".$linhaArray[19].";".$valorProduto.";".$linhaArray[20].";".$quantidade."\n");
                }
                else{
                    // Escreve no log
                    fwrite($arquivo_log, ";Produto não encontrado\n");
                }
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); 
        
        //print_r($LinhasArray);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}
