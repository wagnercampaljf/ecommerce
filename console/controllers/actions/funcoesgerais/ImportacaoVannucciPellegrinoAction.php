<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class ImportacaoVannucciPellegrinoAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/vannucci_1_27000_pellegrino_estoque_preco.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_vannucci_1_27000_pellegrino_estoque_preco.csv")){
            unlink("/var/tmp/log_vannucci_1_27000_pellegrino_estoque_preco.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_vannucci_1_27000_pellegrino_estoque_preco.csv", "a");

        fwrite($arquivo_log, "codigo_fabricante;codigo_global;nome;subcategoria_id;peso;altura;largura;profundidade;aplicacao;estoque;preco_venda;status");
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            
            if ($k <= 0){
                continue;
            }
            
            continue;
            
            fwrite($arquivo_log, "\n".$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8].';'.$linhaArray[9].';'.$linhaArray[10]);
            
            $codigo_fabricante = $linhaArray[0];
            $produto_fabricante = Produto::find()->andWhere(['=','codigo_fabricante',$codigo_fabricante])->one();
            if($produto_fabricante){
                fwrite($arquivo_log, ";codigo_fabricante já cadastrado");
                continue;
            }
            
            $codigo_global = str_replace(" ","",$linhaArray[1]);
            
            echo "\n".$k." - ".$codigo_global;
            if (!Subcategoria::findOne(['id'=>(int)$linhaArray[3]])){
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
                //$produto->codigo_similar    = $linhaArray[4];
                $produto->nome              = substr($linhaArray[2], 0, 150);
                $produto->aplicacao         = $linhaArray[8];
                $produto->peso              = $linhaArray[4];
                $produto->altura            = $linhaArray[5];
                $produto->largura           = $linhaArray[6];
                $produto->profundidade      = $linhaArray[7];
                $produto->subcategoria_id   = $linhaArray[3];
                $produto->fabricante_id     = 91;
                $this->slugify($produto);
                if ($produto->save()){
                    
                    if($linhaArray[9] == null || $linhaArray[9] == "" || $linhaArray[9] == 0){
                        fwrite($arquivo_log, " - estoque zerado");
                    }
                    
                    if($linhaArray[10] == null || $linhaArray[10] == "" || $linhaArray[10] == 0.0 ||$linhaArray[10] == 0 ){
                        fwrite($arquivo_log, " - estoque zerado");
                    }
                    
                    echo " - ".$produto->id;
                    
                    fwrite($arquivo_log, " - Produto criado");
                    
                    $produtoFilial              = new ProdutoFilial();
                    $produtoFilial->produto_id  = $produto->id;
                    $produtoFilial->filial_id   = 38;
                    $produtoFilial->quantidade  = $linhaArray[9];
                    $produtoFilial->envio       = 1;
                    if ($produtoFilial->save()){
                        
                        fwrite($arquivo_log, " - Estoque criado");
                        
                        $valorProdutoFilial                     = New ValorProdutoFilial;
                        $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
                        $valorProdutoFilial->valor              = (float)str_replace(",",".",$linhaArray[10]);
                        $valorProdutoFilial->valor_cnpj         = (float)str_replace(",",".",$linhaArray[10]);
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

                    /*$caminhoImagem          = "/var/tmp/vannucci_pellegrino_86-115_precificado_venda/com_logo/".$produto->codigo_fabricante.".jpg";
                    $caminhoImagemSemLogo   = "/var/tmp/vannucci_pellegrino_86-115_precificado_venda/sem_logo/".$produto->codigo_fabricante.".jpg";
                    
                    if (file_exists($caminhoImagem)) {
                    echo " - ".$caminhoImagem." - EXISTE\n";
                        $imagem = new Imagens();
                        $imagem->produto_id         = $produtoFilial->produto->id;
                        $imagem->imagem             = base64_encode(file_get_contents($caminhoImagem));
                        $imagem->imagem_sem_logo    = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                        $imagem->ordem              = 1;
                        $imagem->save();
                    }*/
                    
                }
                else{
                    print_r($produto->save());
                    fwrite($arquivo_log, " - Produto não criado");
                }
            }
            else{
                fwrite($arquivo_log, ";Código global nulo");
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
    
    private function slugify(&$model)
    {
        $text = $model->nome . ' ' . $model->codigo_global;
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        $model->slug = $text;
    }
}
