<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use common\models\MarcaProduto;

class MorelateAtualizarMarcasAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/morelate_marcas.csv", 'r');
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_morelate_marcas.csv")){
            unlink("/var/tmp/log_morelate_marcas.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_morelate_marcas.csv", "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){

            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";');
            
            if ($i <= 0){
                fwrite($arquivo_log, "status");
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[1];
            
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[0].".M"])
                                        ->andWhere(['=','fabricante_id', 130])
                                        ->one(); 
            
            if ($produto){
                
                echo " - encontrado"; 
                fwrite($arquivo_log, 'Produto encontrado');
                
                $marca_produto = MarcaProduto::find()->andWhere(['=','nome',$linhaArray[1]])->one();
                if($marca_produto){
                    echo " - Marca encontrada";
                    fwrite($arquivo_log, ' - Marca encontrado');
                    $produto->marca_produto_id = $marca_produto->id;
                }
                else{
                    echo " - Marca não encontrada";
                    fwrite($arquivo_log, ' - Marca não encontrado');
                    
                    $marca_produto          = new MarcaProduto;
                    $marca_produto->nome    = $linhaArray[1];
                    if($marca_produto->save()){
                        echo " - Marca criada";
                        fwrite($arquivo_log, ' - Marca criada');
                        $produto->marca_produto_id = $marca_produto->id;
                    }
                    else{
                        echo " - Marca não criada";
                        fwrite($arquivo_log, ' - Marca não criada');
                    }
                }
                
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







