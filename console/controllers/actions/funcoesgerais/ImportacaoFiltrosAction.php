<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Imagens;
use common\models\Subcategoria;
use common\models\MarcaProduto;

class ImportacaoFiltrosAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $date = date('Y-m-d H:i');
        echo "\n\nInicio: ";echo $date;
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/FILTROS_AGOSTO_COM_PRECO.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_FILTROS_AGOSTO_COM_PRECO.csv")){
            unlink("/var/tmp/log_FILTROS_AGOSTO_COM_PRECO.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_FILTROS_AGOSTO_COM_PRECO.csv", "a");
        // Escreve no log
        //fwrite($arquivo_log, "codigo;nome;;preco_venda;status\n");
        
        //$transaction = Yii::$app->db->beginTransaction();
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            
            echo "\n".$k." - ";
            fwrite($arquivo_log, "\n".$linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13]);
            echo "\n".$linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13];
//continue;            
            if($k==0){
                continue;
            }
            
            $subcategoria_id                = 285;
            $subcategoria                   = Subcategoria::find()->andWhere(["like", "nome", utf8_encode($linhaArray[8])])->one();
            if($subcategoria){
                $subcategoria_id            = $subcategoria->id;
            }
            
            $marca_id                       = null;
            $marca                          = MarcaProduto::find()->andWhere(['like', 'nome', $linhaArray[10]])->one();
            if($marca){
                $marca_id                   = $marca->id;
            }
            else{
                $marca                      = new MarcaProduto;
                $marca->nome                = $linhaArray[10];
                $marca->save();
                
                $marca_id                   = $marca->id;
            }

            $produto                        = new Produto();
            $produto->codigo_fabricante     = $linhaArray[5];
            $produto->codigo_global         = $linhaArray[5];
            $produto->nome                  = $linhaArray[0];
            $produto->aplicacao             = $linhaArray[9];
            $produto->peso                  = $linhaArray[1];
            $produto->altura                = $linhaArray[2];
            $produto->largura               = $linhaArray[3];
            $produto->profundidade          = $linhaArray[4];
            $produto->subcategoria_id       = $subcategoria_id; 
            $produto->produto_condicao_id   = 1;
            $produto->codigo_montadora      = $linhaArray[6];
            $produto->fabricante_id         = 33;
            $produto->marca_produto_id      = $marca_id;
            $this->slugify($produto);
            //print_r($produto);
            echo 3333;

            if ($produto->save()){
                echo "Produto CRIADO\n";
                fwrite($arquivo_log, ";Produto CRIADO");
            } else{
                $produto->codigo_global         = $linhaArray[5].",";
                if ($produto->save()){
                    echo "Produto CRIADO\n";
                    fwrite($arquivo_log, ";Produto CRIADO");
                } else{
                    $produto->codigo_global         = $linhaArray[5].".";
                    if ($produto->save()){
                        echo "Produto CRIADO\n";
                        fwrite($arquivo_log, ";Produto CRIADO");
                    } else{
                        fwrite($arquivo_log, ";Produto NAO CRIADO");
                        echo "Produto NÃO CRIADO\n";
                        continue;
                    }
                }
            }
            
            $produtoFilial              = new ProdutoFilial();
            $produtoFilial->produto_id  = $produto->id;
            $produtoFilial->filial_id   = 8;
            $produtoFilial->quantidade  = 99999;
            $produtoFilial->envio       = 1;
            if ($produtoFilial->save()){
                fwrite($arquivo_log, " - ProdutoFilial CRIADO");
            } else{
                fwrite($arquivo_log, " - ProdutoFilial NAO CRIADO");
                continue;
            }
            
            
            $preco_compra = (float) str_replace(",",".",$linhaArray[11]);
            $preco_venda = (float) str_replace(",",".",$linhaArray[13]);
                                    
            $valorProdutoFilial                     = New ValorProdutoFilial;
            $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
            $valorProdutoFilial->valor_compra       = $preco_compra;
            $valorProdutoFilial->valor              = $preco_venda;
            $valorProdutoFilial->valor_cnpj         = $preco_venda;
            $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
            //var_dump($valorProdutoFilial);
            if ($valorProdutoFilial->save()){
                fwrite($arquivo_log, " - ValorProdutoFilial CRIADO");
            } else{
                fwrite($arquivo_log, " - ValorProdutoFilial NAO CRIADO");
                continue;
            }

            /*$caminhoImagemComLogo   = "/var/tmp/fotos_universal/foto_universal_com_logo/".$produto->codigo_fabricante.".jpg";
            $caminhoImagemSemLogo   = "/var/tmp/fotos_universal/foto_universal_sem_logo/".$produto->codigo_fabricante.".jpg";
            
            if (file_exists($caminhoImagemComLogo)) {
                echo $caminhoImagemComLogo." - EXISTE\n";
                $imagem = new Imagens();
                $imagem->produto_id         = $produtoFilial->produto->id;
                $imagem->imagem             = base64_encode(file_get_contents($caminhoImagemComLogo));
                $imagem->imagem_sem_logo    = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                $imagem->ordem              = 1;
                $imagem->save();
            }*/ 
        }
            
        fclose($arquivo_log); 
        
        //print_r($LinhasArray);
        
        $date = date('Y-m-d H:i');
        echo "\n\nTermino: ";echo $date;
        
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