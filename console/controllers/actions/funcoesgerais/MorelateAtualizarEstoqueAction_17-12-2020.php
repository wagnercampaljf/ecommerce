<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class MorelateAtualizarEstoqueAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $codigos_produtos = array();
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/morelate_estoque_04-08-2020.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
            $codigos_produtos[$line[0].".M"] = $line[0];
        }
        fclose($file);
        
        $log = "/var/tmp/log_morelate_estoque_04-08-2020.csv";
        if (file_exists($log)){
            unlink($log);
        }
        $arquivo_log = fopen($log, "a");
        
        /*foreach ($LinhasArray as $i => &$linhaArray){
            
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'"');
            
            if ($i == 0){
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[5];
                        
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[0].".M"])->one();
            
            if ($produto){
                
                $produto_filial = ProdutoFilial::find()->andWhere(['=','produto_id',$produto->id])
                                                       ->andWhere(['=','filial_id',43])
                                                       ->one();
                
                if($produto_filial){
                    
                    $produto_filial->quantidade = $linhaArray[5];
                    if($produto_filial->save()){
                        echo  " - Estoque alterado";
                        fwrite($arquivo_log, ';"Estoque alterado"');
                    }
                    else{
                        echo " - Estoque não alterado";
                        fwrite($arquivo_log, ';"Estoque não alterado"');
                    }
                }
                else{
                    echo " - Estoque não encontrado";
                    fwrite($arquivo_log, ';"Estoque não encontrado"');
                }
            }
            else{
                echo " - Produto não encontrado";
                fwrite($arquivo_log, ';"Produto não encontrado"');
            }
        }*/
        
        $produtos_morelate = Produto::find()->andWhere(['=', 'fabricante_id', 130])->all();
        
        fwrite($arquivo_log, "\n\n\n".'"produto_id";"codigo_fabricante";"quantidade";"status"');
        
        foreach($produtos_morelate as $k => $produto_morelate){
            $produto_encontrado = false;
            
            /*foreach ($LinhasArray as $j => &$linhaArray){
                if($linhaArray[0].".M" == $produto_morelate->codigo_fabricante){
                    $produto_encontrado = true;
                    break;
                }
            }*/
            
            if(array_key_exists($produto_morelate->codigo_fabricante, $codigos_produtos)){
                $produto_encontrado = true;
            }
            
            if(!$produto_encontrado){
                echo "\n".$k." - ".$produto_morelate->codigo_fabricante." - produto não encontrado na planilha";
                
                $produto_filial = ProdutoFilial::find() ->andWhere(['=','produto_id', $produto_morelate->id])
                                                        ->andWhere(['=', 'filial_id', 43])
                                                        ->one();
                
                if($produto_filial){
                    
                    $quantidade = $produto_filial->quantidade;
                    
                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produto_filial->id])->orderBy(["id"=>SORT_DESC])->one();
                    
                    if($valor_produto_filial){
                        $status = "Produto não encontrado";
                        
                        if($produto_filial->quantidade <= 3 && $valor_produto_filial->valor <= 500){
                            
                            $produto_filial->quantidade = 0;
                            if($produto_filial->save()){
                                $status .= " - Quantidade zerada";
                            }
                            else{
                                $status .= " - QUantidade não zerada";
                            }
                        }
                        
                        fwrite($arquivo_log, "\n".'"'.$produto_morelate->id.'";"'.$produto_morelate->codigo_fabricante.'";"'.$quantidade.'";"'.$status.'"');
                    }
                }
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}







