<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Imagens;

class ImportacaoFConfortoAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/F_CONFUORTO_400_PARA_CIMA.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_importar_fconforto.csv")){
            unlink("/var/tmp/log_importar_fconforto.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_importar_fconforto.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "codigo;nome;;preco_venda;status\n");
        
        //$transaction = Yii::$app->db->beginTransaction();
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            
            echo $k." - ";
            
            if($k==0 || $k==1 || $k==2){
                continue;
            }
            
            //echo "\n\n";print_r($linhaArray); echo "\n\n";
            
            $produto = new Produto();
            $produto->codigo_fabricante = $linhaArray[0];
            $produto->codigo_montadora  = $linhaArray[0];
            $produto->codigo_global     = $linhaArray[0];
            $produto->nome              = $linhaArray[1];
            $produto->aplicacao         = $linhaArray[1];
            $produto->peso              = 3;
            $produto->altura            = 30;
            $produto->largura           = 30;
            $produto->profundidade      = 30;
            $produto->subcategoria_id   = 285; 
            $produto->fabricante_id     = 118;
            $this->slugify($produto);
            //print_r($produto);
            if ($produto->save()){
                echo "Produto CRIADO\n";
                fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[6].";Produto CRIADO");
            } else{
                fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[6].";Produto NAO CRIADO\n");
                echo "Produto NÃO CRIADO\n";
                continue;
            }
            
            $produtoFilial              = new ProdutoFilial();
            $produtoFilial->produto_id  = $produto->id;
            $produtoFilial->filial_id   = 4;
            $produtoFilial->quantidade  = 99999;
            $produtoFilial->envio       = 1;
            if ($produtoFilial->save()){
                fwrite($arquivo_log, " - ProdutoFilial CRIADO\n");
            } else{
                fwrite($arquivo_log, " - ProdutoFilial NAO CRIADO\n");
                continue;
            }
            
            //$preco = (float)str_replace(" ","",(str_replace("@",".",(str_replace(".",",",str_replace(",","@",$linhaArray[6]))))));
            $preco = $linhaArray[6];
                        
            $valorProdutoFilial                     = New ValorProdutoFilial;
            $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
            $valorProdutoFilial->valor              = $preco;
            $valorProdutoFilial->valor_cnpj         = $preco;
            $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
            //var_dump($valorProdutoFilial);
            if ($valorProdutoFilial->save()){
                fwrite($arquivo_log, " - ValorProdutoFilial CRIADO\n");
            } else{
                fwrite($arquivo_log, " - ValorProdutoFilial NAO CRIADO\n");
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
