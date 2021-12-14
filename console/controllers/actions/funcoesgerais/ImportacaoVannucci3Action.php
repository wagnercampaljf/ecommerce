<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class ImportacaoVannucci3Action extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/vannucci_363-467.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_importar_precos_vanucci_363-467.csv")){
            unlink("/var/tmp/log_importar_precos_vanucci_363-467.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_importar_precos_vanucci_363-467.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "codigo_fabricante;codigo_global;numero_caracteres;nome;aplicacao;valor;peso;altura;largura;profundidade;subcategoria_id;preco_venda;status\n");
        
        //$transaction = Yii::$app->db->beginTransaction();
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            
            /*if ($k == 11){
             $transaction->commit();
             //$transaction->rollBack();
             break;
             }*/
            
            if ($linhaArray[1] == "Código Original"){
                continue;
            }
            
            //print_r($linhaArray); echo "\n";
            $codigo_global = str_replace(" ","",str_replace('-','',$linhaArray[1]));
            
            echo $k." - ".$codigo_global."\n";
            if (!Subcategoria::findOne(['id'=>(int)$linhaArray[9]])){
                continue;
            }
            
            if ($codigo_global == null and $codigo_global == ""){
                // Escreve no log
                fwrite($arquivo_log, ';'.$linhaArray[1].';"Sem codigo_referencia"'."\n");
            }
            else {
                $produto = Produto::find()->andWhere(['=','codigo_global',$codigo_global])->one();
                
                if (isset($produto)){
                    $codigo_global = $codigo_global.",";
                }
                
                $produto = new Produto();
                $produto->codigo_fabricante = $linhaArray[0];
                $produto->codigo_global     = str_replace(" ", "", $codigo_global);
                $produto->codigo_similar    = $linhaArray[2];
                $produto->nome              = substr($linhaArray[3], 0, 150);
                $produto->aplicacao         = $linhaArray[4];
                $produto->peso              = $linhaArray[5];
                $produto->altura            = $linhaArray[6];
                $produto->largura           = $linhaArray[8];
                $produto->profundidade      = $linhaArray[7];
                $produto->subcategoria_id   = $linhaArray[9];
                $produto->fabricante_id     = 91;
                $this->slugify($produto);
                if ($produto->save()){
                    fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";"."Produto CRIADO");
                } else{
                    print_r($produto);
                    fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";"."Produto NAO CRIADO\n");
                    continue;
                }
                
                $produtoFilial              = new ProdutoFilial();
                $produtoFilial->produto_id  = $produto->id;
                $produtoFilial->filial_id   = 96;
                $produtoFilial->quantidade  = 99999;
                $produtoFilial->envio       = 1;
                if ($produtoFilial->save()){
                    fwrite($arquivo_log, " - ProdutoFilial CRIADO");
                } else{
                    fwrite($arquivo_log, " - ProdutoFilial NAO CRIADO\n");
                    continue;
                }
                
                $valorProdutoFilial                     = New ValorProdutoFilial;
                $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
                $valorProdutoFilial->valor              = (float)str_replace(",",".",$linhaArray[10]);
                $valorProdutoFilial->valor_cnpj         = (float)str_replace(",",".",$linhaArray[10]);
                $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
                //var_dump($valorProdutoFilial);
                if ($valorProdutoFilial->save()){
                    fwrite($arquivo_log, " - ValorProdutoFilial CRIADO\n");
                } else{
                    fwrite($arquivo_log, " - ValorProdutoFilial NAO CRIADO\n");
                    continue;
                }
                /*echo "kkkkkkkkkkkkkkkkkkkkk";
                 $caminhoImagem          = "/var/tmp/vnc1200_900logo/".$produto->codigo_global." cópia.jpg";
                 $caminhoImagemSemLogo   = "/var/tmp/vnc1200_900/".$produto->codigo_global.".jpg";
                 
                 if (file_exists($caminhoImagem)) {
                 echo $caminhoImagem." - EXISTE\n";
                 $imagem = new Imagens();
                 $imagem->produto_id         = $produtoFilial->produto->id;
                 $imagem->imagem             = base64_encode(file_get_contents($caminhoImagem));
                 $imagem->imagem_sem_logo    = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                 $imagem->ordem              = 1;
                 $imagem->save();
                 
                 //var_dump(rename($caminhoImagem, "/var/tmp/vnc1200_900/".str_replace("/","-",$produtoFilial->produto->nome).".jpg"));
                 } else {
                 echo $caminhoImagem." - NÃO EXISTE\n";
                 continue;
                 }*/
            }
        }
        
        //$transaction->commit();
        //$transaction->rollBack();
        
        // Fecha o arquivo
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
