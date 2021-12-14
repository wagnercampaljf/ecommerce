<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use common\models\MarcaProduto;

class MorelateAtualizarProdutosAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen("/var/tmp/morelate_para_subir_completa_precificado_dimensoes_correcao.csv", 'r');
        //$file = fopen("/var/tmp/morelate_30-03-2020_MARIANA_1746.csv", 'r');
        //$file = fopen("/var/tmp/24-04_Mariana_517_produtos.csv", 'r');
        //$file = fopen("/var/tmp/20-04_Mariana_534_produtos.csv", 'r');
        //$file = fopen("/var/tmp/04-05_Mariana_434_produtos.csv", 'r');
        $file = fopen("/var/tmp/pellegrino_morelate_nome_acima_150_SUBIR_1.csv", 'r');
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_pellegrino_morelate_nome_acima_150_SUBIR_1.csv")){
            unlink("/var/tmp/log_pellegrino_morelate_nome_acima_150_SUBIR_1.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_pellegrino_morelate_nome_acima_150_SUBIR_1.csv", "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){

            if ($i <= 1){
                continue;
            }
            
            //fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8].'";"'.$linhaArray[9].'";"'.$linhaArray[10].'";"'.$linhaArray[11].'";"'.$linhaArray[12].'";"'.$linhaArray[13].'";"'.$linhaArray[14].'";"'.$linhaArray[15].'";"'.$linhaArray[16].'";"'.$linhaArray[17].'";');
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'"');
            
            if ($i <= 2){
                fwrite($arquivo_log, "encontrado;status");
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[5]." - ".$linhaArray[0];
            
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[5].".M"])
                                        ->andWhere(['=','fabricante_id', 130])
                                        ->one(); 
            
            if ($produto){
                
                echo " - encontrado"; 
                fwrite($arquivo_log, ';Produto encontrado');
                
                /*$marca_produto = MarcaProduto::find()->andWhere(['=','nome',$linhaArray[17]])->one();
                if($marca_produto){
                    echo " - Marca encontrada";
                    fwrite($arquivo_log, ' - Marca encontrado');
                    $produto->marca_produto_id = $marca_produto->id;
                }
                else{
                    echo " - Marca não encontrada";
                    fwrite($arquivo_log, ' - Marca não encontrado');
                    
                    $marca_produto          = new MarcaProduto;
                    $marca_produto->nome    = $linhaArray[17];
                    if($marca_produto->save()){
                        echo " - Marca criada";
                        fwrite($arquivo_log, ' - Marca criada');
                        $produto->marca_produto_id = $marca_produto->id;
                    }
                    else{
                        echo " - Marca não criada";
                        fwrite($arquivo_log, ' - Marca não criada');
                    }
                }*/
                
                //$codigo_global        = $linhaArray[1];
                
                $reman = $linhaArray[1];
                if($reman == "X"){
                    $produto->produto_condicao_id = 3;
                }
                else{
                    $produto->produto_condicao_id = 1;
                }
                
                $nome_limpo = substr($linhaArray[2], 0, 150);
                
                $produto->nome              = $nome_limpo;
                $produto->aplicacao         = $linhaArray[3];
                $produto->codigo_montadora  = $linhaArray[6];
                //$produto->codigo_similar    = $linhaArray[3];
                //$produto->codigo_global     = $codigo_global;
                if($produto->save()){
                    echo " - produto_alterado";
                    fwrite($arquivo_log, " - produto_alterado");
                }
                else{
                    echo " - produto_não_alterado";
                    fwrite($arquivo_log, " - produto_nao_alterado");
                    
                    /*echo " - produto_nao_alterado";
                    $produto->codigo_global = $codigo_global.".";
                    if($produto->save()){
                        echo " - produto_alterado";
                        fwrite($arquivo_log, " - produto_alterado (.)");
                    }
                    else{
                        echo " - produto_nao_alterado";
                        $produto->codigo_global = $codigo_global.",";
                        if($produto->save()){
                            echo " - produto_alterado";
                            fwrite($arquivo_log, " - produto_alterado (,)");
                        }
                        else{
                            echo " - produto_nao_alterado";
                            $produto->codigo_global = $codigo_global. "_";
                            if($produto->save()){
                                echo " - produto_alterado";
                                fwrite($arquivo_log, " - produto_alterado (_)");
                            }
                            else{
                                echo " - produto_nao_alterado";
                                $produto->codigo_global = $codigo_global."|";
                                if($produto->save()){
                                    echo " - produto_alterado";
                                    fwrite($arquivo_log, " - produto_alterado (|)");
                                }
                                else{
                                    echo " - produto_nao_alterado";
                                    $produto->codigo_global = $codigo_global."-";
                                    if($produto->save()){
                                        echo " - produto_alterado";
                                        fwrite($arquivo_log, " - produto_alterado (-)");
                                    }
                                    else{
                                        echo " - produto_nao_alterado";
                                        $produto->codigo_global = $codigo_global."*";
                                        if($produto->save()){
                                            echo " - produto_alterado";
                                            fwrite($arquivo_log, " - produto_alterado (*)");
                                        }
                                        else{
                                            echo " - produto_nao_alterado";
                                            $produto->codigo_global = $codigo_global.")";
                                            if($produto->save()){
                                                echo " - produto_alterado";
                                                fwrite($arquivo_log, " - produto_alterado ())");
                                            }
                                            else{
                                                echo " - produto_não_criado";
                                                fwrite($arquivo_log, " - produto_nao_alterado");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }*/
                }
                
                /*$produto_filial = ProdutoFilial::find()->andWhere(['=','produto_id',$produto->id])->one();
                if($produto_filial){
                    echo " - estoque encontrado";
                    fwrite($arquivo_log, " - estoque_encontrado");

                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id',$produto_filial->id])->orderBy('id')->one();
                    if($valor_produto_filial){
                        echo " - valor encontrado";
                        fwrite($arquivo_log, " - valor_encontrado");
                        
                        $valor_produto_filial->valor        = $linhaArray[22];
                        $valor_produto_filial->valor_cnpj   = $linhaArray[22];
                        if($valor_produto_filial->save()){
                            echo " - valor alterado";
                            fwrite($arquivo_log, " - valor_alterado");
                        }
                        else{
                            echo " - valor não alterado";
                            fwrite($arquivo_log, " - valor_nao_alterado");
                        }
                    }
                    else{
                        echo " - valor não encontrado";
                        fwrite($arquivo_log, " - valor_nao_encontrado");
                    }
                }
                else{
                    echo " - estoque não encontrado";
                    fwrite($arquivo_log, " - estoque_nao_encontrado");
                }*/
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







