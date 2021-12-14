<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class VannucciVerificarProdutosAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/vannucci_produtos_nao_existem_02-07-2020.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $log = "/var/tmp/log_vannucci_produtos_nao_existem_02-07-2020.csv";
        if (file_exists($log)){
            unlink($log);
        }
        $arquivo_log = fopen($log, "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){
            
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'"');
            
            if ($i == 0){
                fwrite($arquivo_log,";8;9;10;11");
                continue;
            }
            
            if ($i == 1){
                fwrite($arquivo_log,";status;produto_id_encontrado;codigo_fabricante;codigo_global;nome");
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[3]." - ".$linhaArray[4];
            //continue;
            
            $codigo_fabricante_reduzido = explode("-", $linhaArray[4]);
            
            $produto = Produto::find()  ->andWhere(['like','codigo_fabricante', $codigo_fabricante_reduzido[0]])->one();
            
            if ($produto){
                echo " - Produto encontrado";
                fwrite($arquivo_log, ';"Produto encontrado";"'.$produto->id.'";"'.$produto->codigo_fabricante.'";"'.$produto->codigo_global.'";"'.$produto->nome.'"');
            }
            else{
                echo " - Produto não encontrado";
                fwrite($arquivo_log, ';"Produto não encontrado";""');
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}







