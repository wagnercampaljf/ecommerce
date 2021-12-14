<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class VerificarProdutosExistemVannucciAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/vannucci_estoque_ean.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
	if (file_exists("/var/tmp/log_produtos_existem_vannucci.csv"))
        {
            unlink("/var/tmp/log_produtos_existem_vannucci.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_produtos_existem_vannucci.csv", "a");
                
        foreach ($LinhasArray as $i => &$linhaArray)
        {
            
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'"');
            echo "\n".$i." - ".$linhaArray[4].' - '.$linhaArray[2].' - '.$linhaArray[6].' - '.$linhaArray[7];

            if ($i <= 0)
            {
                fwrite($arquivo_log, ';STATUS');
                continue;
            }
            
            $codigo_fabricante = $linhaArray[4];
            
            $produtoFilial_fabricante = ProdutoFilial::find()   ->joinWith('produto')
                                                                ->andWhere(['=','produto_filial.filial_id',38])
                                                                ->andWhere(['=','produto.codigo_fabricante', $codigo_fabricante])
                                                                ->one();
            if ($produtoFilial_fabricante) 
            {
                echo " - Encontrado";
                fwrite($arquivo_log, ';Produto encontrado');
                continue;
            }
            else
            {
                echo " - Produto não encontrado";
                fwrite($arquivo_log, ';Produto Não encontrado');
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_log);
        
        /*if (file_exists("/var/tmp/log_produtos_existem_vannucci_pecaagora.csv"))
        {
            unlink("/var/tmp/log_produtos_existem_vannucci_pecaagora.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_produtos_existem_vannucci_pecaagora.csv", "a");
        
        $produtosFiliais = ProdutoFilial::find()->andWhere(['=','produto_filial.filial_id',38])->all();
        
        foreach($produtosFiliais as $k => $produtoFilial)
        {
            echo "\n".$k." - ".$produtoFilial->produto->codigo_fabricante;
            fwrite($arquivo_log, "\n".$produtoFilial->produto->codigo_fabricante);
            
            $e_encontrado = false;
            
            foreach ($LinhasArray as $i => &$linhaArray){
                $codigo_fabricante = $linhaArray[4];
                
                if ($produtoFilial->produto->codigo_fabricante == $codigo_fabricante) 
                {
                    $e_encontrado = true;
                    break;
                }
            }
            
            if($e_encontrado)
            {
                echo " - Produto encontrado fabricante";
                fwrite($arquivo_log, ";Produto encontrado fabricante;Mesmo codigo_fabricante");
            }
            else 
            {
                $quantidade_produtos_mesmo_global = 0;
                $codigo_fabricante_planilha = "";
                
                foreach ($LinhasArray as $i => &$linhaArray){
                    $codigo_global = $linhaArray[2];
                    
                    if (str_replace(",","",str_replace(".","",$produtoFilial->produto->codigo_global)) == $codigo_global)
                    {
                        $quantidade_produtos_mesmo_global += 1;
                        $codigo_fabricante_planilha = $linhaArray[4];
//			print_r($linhaArray);die;
                    }
                }
                
                if($quantidade_produtos_mesmo_global==1)
                {
                    $produto_filial_subcategoria = ProdutoFilial::find()->andWhere(['=','produto_filial.filial_id',38])
                                                                        ->joinWith('produto')
                                                                        ->andWhere(['=','codigo_fabricante',$codigo_fabricante_planilha])
                                                                        ->all();

                    echo " - Produto encontrado global - 1";
                    fwrite($arquivo_log, ";Produto encontrado global;Mesmo codigo_global, diferente codigo_fabricante,1 produto encontrado");
                    if(!$produto_filial_subcategoria)
                    {
                        echo " - Produto Não encontrado fabricante";
                        fwrite($arquivo_log, ";Produto encontrado global;Mesmo codigo_global, diferente codigo_fabricante,1 produto encontrado, Não encontrado Fabricante");
			echo "==>". $codigo_fabricante_planilha."<==";
			//print_r();
			$produto_correcao = Produto::find()->andWhere(['=','id', $produtoFilial->produto_id])->one();
			$produto_correcao->codigo_fabricante = $codigo_fabricante_planilha;
			echo "\n\n"; var_dump($produto_correcao->save());echo "\n\n";
                    }
                    else 
                    {
                        echo " - Produto encontrado fabricante";
                        fwrite($arquivo_log, ";Produto encontrado global;Mesmo codigo_global, diferente codigo_fabricante,1 produto encontrado, Encontrado Fabricante");
                    }
                }
                elseif($quantidade_produtos_mesmo_global==0)
                {
                    echo " - Produto não encontrado global";
                    fwrite($arquivo_log, ";Produto não encontrado global;");
                }
                else 
                {
                    echo " - Produto encontrado global - ".$quantidade_produtos_mesmo_global;
                    fwrite($arquivo_log, ";Produto encontrado global;Mesmo codigo_global, diferente codigo_fabricante,".$quantidade_produtos_mesmo_global." produto encontrado");
                }
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); */
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








