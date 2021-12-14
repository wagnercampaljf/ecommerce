<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use common\models\MarcaProduto;

class LNGAtualizarMultiplicadorAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/lng_multiplicador_05-06-2020.csv", 'r');
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_lng_multiplicador_05-06-2020.csv")){
            unlink("/var/tmp/log_lng_multiplicador_05-06-2020.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_lng_multiplicador_05-06-2020.csv", "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){

            if ($i <= 0){
                continue;
            }
            
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'"');
            
            if ($i <= 2){
                fwrite($arquivo_log, "encontrado;status");
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[1]." - ".$linhaArray[3];
            
            $codigo_fabricante = "L".str_replace("-","",$linhaArray[1]);
            
            $produto_filial = ProdutoFilial::find() ->joinWith('produto')
                                                    ->andWhere(['=','codigo_fabricante', $codigo_fabricante])
                                                    ->andWhere(['=', 'filial_id', 60])
                                                    ->one(); 
            
            if ($produto_filial){
                
                echo " - encontrado"; 
                fwrite($arquivo_log, ';Produto encontrado');
                
                $produto = Produto::find()->andWhere(['=','id',$produto_filial->produto_id])->one();
                
                $produto->multiplicador  = $linhaArray[3];
                if($produto->save()){
                    echo " - produto_alterado";
                    fwrite($arquivo_log, " - produto_alterado");
                }
                else{
                    echo " - produto_não_alterado";
                    fwrite($arquivo_log, " - produto_nao_alterado");
                }
            }
            else{
                echo " - Não encontrado";
                fwrite($arquivo_log, ';Produto Não encontrado;');
            }
        }
        
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}







