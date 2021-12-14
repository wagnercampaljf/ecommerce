<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class DibAtualizarEstoqueAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/dib_estoque_preco_26-06-2020.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $log = "/var/tmp/log_dib_estoque_26-06-2020.csv";
        if (file_exists($log)){
            unlink($log);
        }
        
        $arquivo_log = fopen($log, "a");
                
        foreach ($LinhasArray as $i => &$linhaArray){
            
            //if($linhaArray[0]!="239291"){
            //    continue;
            //}
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[4]." - ".$linhaArray[5];
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'"');
            
            if ($i <=1){
                fwrite($arquivo_log, ";status_produto;status_produto_filial");
                continue;
            }
            
            $produto = Produto::find()  ->andWhere(['like','codigo_fabricante', 'D'.$linhaArray[0]])
                                        //->andWhere(['not like','codigo_fabricante', 'CX.D'.$linhaArray[0]])
                                        ->one();
                                        
            if ($produto){
                
                echo " - Produto encontrado";
                fwrite($arquivo_log, ";Produto encontrado;");
                
                $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',97])
                                                        ->andWhere(['=', 'produto_id', $produto->id])
                                                        ->one();
                if ($produtoFilial) {
                    
                    echo " - ".$produtoFilial->id;
                    fwrite($arquivo_log, ";Estoque encontrado");
                    
                    $quantidade = $linhaArray[5];
                    if($linhaArray[4] == "239-CAPAS CONFECCAO CHINIL DIB" || $linhaArray[4] == "352-CAPAS CONFECCAO PELUCIA DIB" || $linhaArray[4] == "586-CAPAS CONFECCAO CHINIL PREMIUM" || $linhaArray[4] == "587-CAPAS CONFECCAO CORINO"){
                        $quantidade = 991;
                    }

		    $nome = $linhaArray[1];
                    if ((!(strpos($nome,"CAPA PORCA") === false)) && (strpos($linhaArray[0],"CX.") === false)){
                        $quantidade = 0;
                        echo " - CAPA";
                    }
                    
                    echo " - quantidade: ".$quantidade." ;";
                    
                    $produtoFilial->quantidade  = $quantidade;
                    if ($produtoFilial->save()){
                        echo " - Estoque alterado";
                        fwrite($arquivo_log, ";Estoque alterado");
                    }
                    else{
                        echo " - Estoque não alterado";
                        fwrite($arquivo_log, ";Estoque não alterado");
                    }
                    
                }
                else{
                    echo " - Estoque não encontrado";
                    fwrite($arquivo_log, ";Estoque não encontrado");
                }
            }
            else{
                echo " - Produto não encontrado";
                fwrite($arquivo_log, ";Produto não encontrado");
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








