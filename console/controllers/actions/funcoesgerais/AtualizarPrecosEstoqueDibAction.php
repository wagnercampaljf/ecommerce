<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class AtualizarPrecosEstoqueDibAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/dib_estoque_15-08-19_precificado.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $log = "/var/tmp/dib_estoque_15-08-19_log.csv";
        if (file_exists($log)){
            unlink($log);
        }
        
        $arquivo_log = fopen($log, "a");
        // Escreve no log
        fwrite($arquivo_log,"Código;Descricao;Un;Bruto;Original;Fabrica;Grupo;Saldo;Especificações;Peso Bruto;Altura;Largura;Comprimento;NCM;CEST;%IPI;status produto;status estoque\n");
        
        foreach ($LinhasArray as $i => &$linhaArray){
            
            if ($i == 0){
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[7]." - ".$linhaArray[3]." - ".$linhaArray[16];
            //continue;
            
            fwrite($arquivo_log, $linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8].';'.$linhaArray[9].';'.$linhaArray[10].';'.$linhaArray[11].$linhaArray[12].';'.';'.$linhaArray[13].';'.$linhaArray[14].';'.$linhaArray[15].';'.$linhaArray[16]);
            
            $produto = Produto::find()  ->andWhere(['like','codigo_fabricante', 'D'.$linhaArray[0]])
                                        ->andWhere(['not like','codigo_fabricante', 'CX.D'.$linhaArray[0]])
                                        ->one();
            if ($produto){
                
                echo " - Produto encontrado";
                fwrite($arquivo_log, ";Produto encontrado;".$produto->codigo_fabricante);
                
                $produtoFilial = ProdutoFilial::find()->andWhere(['=','filial_id',97])
                ->andWhere(['=', 'produto_id', $produto->id])
                ->one();
                if ($produtoFilial) {
                    
                    echo " - ".$produtoFilial->id."\n";
                    fwrite($arquivo_log, ";Estoque encontrado");
                    
                    $produtoFilial->quantidade  = $linhaArray[7];
                    if ($produtoFilial->save()){
                        fwrite($arquivo_log, ";Estoque alterado");
                    }
                    else{
                        fwrite($arquivo_log, ";Estoque não alterado");
                    }
                    
                    $valor_produto_filial = new ValorProdutoFilial;
                    $valor_produto_filial->produto_filial_id    = $produtoFilial->id;
                    $valor_produto_filial->valor                = $linhaArray[16];
                    $valor_produto_filial->valor_cnpj           = $linhaArray[16];
                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                    $valor_produto_filial->promocao             = false;
                    if($valor_produto_filial->save()){
                        fwrite($arquivo_log, ";Preço criado");
                    }
                    else{
                        fwrite($arquivo_log, ";Preço não criado");
                    }
                }
                else{
                    fwrite($arquivo_log, ";Estoque não encontrado");
                }
            }
            else{
                echo " - Produto não encontrado";
                fwrite($arquivo_log, ";Produto não encontrado");
            }
            
            fwrite($arquivo_log, "\n");
        }
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








