<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class VerificacaoVannucciPellegrinoAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/PELEGRINO_2083_AO_FINAL_precificado_venda_verificar.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_PELEGRINO_2083_AO_FINAL_precificado_venda_verificar.csv")){
            unlink("/var/tmp/log_PELEGRINO_2083_AO_FINAL_precificado_venda_verificar.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_PELEGRINO_2083_AO_FINAL_precificado_venda_verificar.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "COD_FABRICANTE;COD_GLOBAL;NOME_PRODUTO;APLICAÇÃO;CODIGO_SIMILAR;SUBCATEGORIA_ID;PESO;COMPRIMENTO;ALTURA;LARGURA;VALOR;VALOR_COMPRA;VALOR_VENDA;STATUS;VERIFICAÇÃO");
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            
            if ($k <= 0){
                continue;
            }
            
            fwrite($arquivo_log, "\n".$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8].';'.$linhaArray[9].';'.$linhaArray[10].';'.$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";");            
            
            $codigo_fabricante = $linhaArray[1];
            
            $produto = Produto::find()  ->andWhere(['=', 'codigo_fabricante',$codigo_fabricante])
            ->andWhere(['=', 'fabricante_id', 91])
            ->one();
            
            if($produto){
                echo "\nProduto encontrado!";
                fwrite($arquivo_log, "Produto encontrado");
            }
            else{
                echo "\nProduto Não encontrado!";
                fwrite($arquivo_log, "Produto não encontrado");
            }
            
            /*echo "\n".$k." - ".$codigo_global;
            if (!Subcategoria::findOne(['id'=>(int)$linhaArray[5]])){
                fwrite($arquivo_log, ";Subcategoria não encontrado");
                continue;
            }
            
            if ($codigo_global <> null and $codigo_global <> ""){
                $produto = Produto::find()->andWhere(['=','codigo_global',$codigo_global])->one();
                
                if ($produto){
                    $codigo_global = $codigo_global.",";
                    fwrite($arquivo_log, ";Produto encontrado");
                }
                else{
                    fwrite($arquivo_log, ";Produto não encontrado");
                }
                
                $produto = new Produto();
                $produto->codigo_fabricante = $linhaArray[0];
                $produto->codigo_global     = $codigo_global;
                $produto->codigo_similar    = $linhaArray[4];
                $produto->nome              = substr($linhaArray[2], 0, 150);
                $produto->aplicacao         = $linhaArray[3];
                $produto->peso              = $linhaArray[6];
                $produto->altura            = $linhaArray[8];
                $produto->largura           = $linhaArray[9];
                $produto->profundidade      = $linhaArray[7];
                $produto->subcategoria_id   = $linhaArray[5];
                $produto->fabricante_id     = 91;
                $this->slugify($produto);
                if ($produto->save()){
                    echo " - ".$produto->id;
                    
                    fwrite($arquivo_log, " - Produto criado");
                    
                    $produtoFilial              = new ProdutoFilial();
                    $produtoFilial->produto_id  = $produto->id;
                    $produtoFilial->filial_id   = 38;
                    $produtoFilial->quantidade  = 99999;
                    $produtoFilial->envio       = 1;
                    if ($produtoFilial->save()){
                        
                        fwrite($arquivo_log, " - Estoque criado");
                        
                        $valorProdutoFilial                     = New ValorProdutoFilial;
                        $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
                        $valorProdutoFilial->valor              = (float)str_replace(",",".",$linhaArray[12]);
                        $valorProdutoFilial->valor_cnpj         = (float)str_replace(",",".",$linhaArray[12]);
                        $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
                        if($valorProdutoFilial->save()){
                            fwrite($arquivo_log, " - Valor criado");
                        }
                        else{
                            fwrite($arquivo_log, " - Valor não criado");
                        }
                    }
                    else{
                        fwrite($arquivo_log, " - Estoque não criado");
                    }

                    $caminhoImagem          = "/var/tmp/vannucci_2083-maior/com_logo/".$produto->codigo_fabricante.".jpg";
                    $caminhoImagemSemLogo   = "/var/tmp/vannucci_2083-maior/sem_logo/".$produto->codigo_fabricante.".jpg";
                    
                    if (file_exists($caminhoImagem)) {
                    echo " - ".$caminhoImagem." - EXISTE\n";
                        $imagem = new Imagens();
                        $imagem->produto_id         = $produtoFilial->produto->id;
                        $imagem->imagem             = base64_encode(file_get_contents($caminhoImagem));
                        $imagem->imagem_sem_logo    = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                        $imagem->ordem              = 1;
                        $imagem->save();
                    }
                    
                }
                else{
                    print_r($produto->save());
                    fwrite($arquivo_log, " - Produto não criado");
                }
            }
            else{
                fwrite($arquivo_log, ";Código global nulo");
            }*/
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}
