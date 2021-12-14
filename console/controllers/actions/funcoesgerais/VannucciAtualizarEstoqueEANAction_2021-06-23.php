<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class VannucciAtualizarEstoqueEANAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();

        //$file = fopen("/var/tmp/vannucci_estoque_01-07-2020.csv", 'r');
        //$file = fopen("/var/tmp/vannucci_estoque_06-08-2020.csv", 'r');
        //$file = fopen("/var/tmp/vannucci_estoque_14-09-2020.csv", 'r');
        // $file = fopen("/var/tmp/vannucci_estoque_07-10-2020.csv", 'r');
        //$file = fopen("/var/tmp/vannucci_estoque_10-12-2020.csv", 'r');
        //$file = fopen("/var/tmp/vannucci_estoque_28-12-2020.csv", 'r');
        // $file = fopen("/var/tmp/vannucci_estoque_18-01-2021.csv", 'r');
        //$file = fopen("/var/tmp/vannucci_estoque_09-02-2021.csv", 'r');
      //$file = fopen("/var/tmp/vannucci_estoque_06-04-2021.csv", 'r');
        //$file = fopen("/var/tmp/vannucci_estoque_13-05-2021.csv", 'r');
       // $file = fopen("/var/tmp/vannucci_estoque_01-06-2021.csv", 'r');

        $file = fopen("/var/tmp/vannucci_estoque_22-06-2021.csv", 'r');

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_vannucci_estoque_22-06-2021.csv")){
            unlink("/var/tmp/log_vannucci_estoque_22-06-2021.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_vannucci_estoque_22-06-2021.csv", "a");
                
        foreach ($LinhasArray as $i => &$linhaArray){

            if($i <= 0 ){
                continue;
            }
            
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";');

            echo "\n".$i." - ".$linhaArray[4].' - '.$linhaArray[2].' - '.$linhaArray[6].' - '.$linhaArray[7];

            if ($i <= 1)
            {
                fwrite($arquivo_log, 'STATUS');
                continue;
            }
            
            $codigo_fabricante = $linhaArray[4];
            
            $produtoFilial_fabricante = ProdutoFilial::find()   ->joinWith('produto')
                                                                ->andWhere(['=','produto_filial.filial_id',38])
                                                                ->andWhere(['=','produto.codigo_fabricante', $codigo_fabricante])
                                                                ->one();
            if ($produtoFilial_fabricante) {
                
                echo "Estoque encontrado";
                
                /*$produto = Produto::find()->andWhere(['=','id',$produtoFilial_fabricante->produto_id])->one();
                
                if($produto){
                    echo " - Produto encontrado";
                    fwrite($arquivo_log, " - Produto encontrado");
                    
                    //$produto->codigo_montadora  = $linhaArray[3];
                    //$produto->codigo_barras     = $linhaArray[6];
                    if($produto->save()){
                        echo " - EAN e NCM atualizados";
                        fwrite($arquivo_log, " - Estoque Atualizado");
                    }
                    else{
                        echo " - EAN e NCM não atualizada";
                        fwrite($arquivo_log, " - Estoque NÃO Atualizado");
                    }
                }
                else{
                    echo " - Produto não encontrado";
                    fwrite($arquivo_log, " - Produto não encontrado");
                }*/
                
                echo " - ".$produtoFilial_fabricante->quantidade." - Encontrado";
                fwrite($arquivo_log, ";".$produtoFilial_fabricante->quantidade.';Produto encontrado Fabricante');
                //continue;
                
                $quantidade = $linhaArray[7];

                $produtoFilial_fabricante->quantidade = $quantidade;
                if($produtoFilial_fabricante->save()){
                    echo " - quantidade atualizada";
                    fwrite($arquivo_log, " - Estoque Atualizado");
                }
                else{
                    echo " - quantidade não atualizada";
                    fwrite($arquivo_log, " - Estoque NÃO Atualizado");
                }
            }
            else{
                echo " - Estoque não encontrado";
                fwrite($arquivo_log, " - Estoque não encontrado");
            }
        }


        $produtoFiliais = ProdutoFilial::find()->andWhere(['=','produto_filial.filial_id',38])->all();
        foreach($produtoFiliais as $x => $produtoFilial){
	    echo "\n".$x;
            $produto_encontrado = false;
            foreach ($LinhasArray as $i => &$linhaArray){
                $codigo_fabricante = $linhaArray[4];
                
                if($codigo_fabricante == $produtoFilial->produto->codigo_fabricante){
                    $produto_encontrado = true;
                    break;
                }
            }
            
            if(!$produto_encontrado){
		echo " - produto não encontrado";
                fwrite($arquivo_log, "\n".$x.";".$produtoFilial->produto->nome.";".$produtoFilial->produto->codigo_global.";".$produtoFilial->produto->codigo_fabricante.";".$produtoFilial->produto->descricao.";".$produtoFilial->produto->codigo_montadora.";".$produtoFilial->quantidade.";Produto não encontrado na planilha Vannucci");

		$produtoFilial->quantidade = 0;
		if($produtoFilial->save()){
			fwrite($arquivo_log, " - estoque zerado");
            echo " - estoque zerado";
		}
		else{
			fwrite($arquivo_log, " - estoque não zerado");
            echo " - estoque não zerado";
		}
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








