<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class ImportacaoVannucciMarianaAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen('/var/tmp/vannucci_mariana.csv', 'r');
        //$file = fopen('/var/tmp/mariana115-134_precificado_venda.csv', 'r');
	$file = fopen('/var/tmp/mariana115-134_precificado_venda_verificar.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        /*if (file_exists("/var/tmp/log_mariana115-134_precificado_venda.csv")){
            unlink("/var/tmp/log_mariana115-134_precificado_venda.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_mariana115-134_precificado_venda.csv", "a");*/

	if (file_exists("/var/tmp/log_mariana115-134_precificado_venda_verificar.csv")){
            unlink("/var/tmp/log_mariana115-134_precificado_venda_verificar.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_mariana115-134_precificado_venda_verificar.csv", "a");
        // Escreve no log
        //fwrite($arquivo_log, "COD_FABRICANTE;COD_GLOBAL;NOME_PRODUTO;APLICAÇÃO;CODIGO_SIMILAR;SUBCATEGORIA_ID;PESO;COMPRIMENTO;ALTURA;LARGURA;VALOR;VALOR_COMPRA;VALOR_VENDA;STATUS");
        fwrite($arquivo_log, "Código Item;NCM;Código Original;NOME;PESO;ALT;LARG;PROF;CÓD GLO;CÓD SIM;NCM;CÓD FABRIC;FABRIC;IDSUB;SUBCAT;SUBMELI_ID;SUBMELI_NOME;APLIC;APLIC COMP;DESC;MULT;Valor;Valor Compra;Valor Venda;STATUS\n");
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            
            fwrite($arquivo_log, "\n".$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';"'.$linhaArray[8].'";"'.$linhaArray[9].'";"'.$linhaArray[10].'";"'.$linhaArray[11].'";"'.$linhaArray[12].'";'.$linhaArray[13].';"'.$linhaArray[14].'";"'.$linhaArray[15].'";"'.$linhaArray[16].'";"'.$linhaArray[17].'";"'.$linhaArray[18].'";'.$linhaArray[19].';'.$linhaArray[20].';'.$linhaArray[21].';'.$linhaArray[22].';'.$linhaArray[23].";");
            
            if ($k <= 0){
                continue;
            }
            
            $codigo_global = str_replace(" ","",str_replace('-','',$linhaArray[8]));
            
            echo "\n".$k." - ".$codigo_global;
            if (!Subcategoria::findOne(['id'=>(int)$linhaArray[13]])){
                fwrite($arquivo_log, "Subcategoria não encontrado");
                continue;
            }
            
            if ($codigo_global <> null and $codigo_global <> ""){
                $produto = Produto::find()->andWhere(['=','codigo_global',$codigo_global])->one();
                
                if ($produto){
                    $codigo_global = $codigo_global."_";
                    fwrite($arquivo_log, "Produto encontrado");
                }
                else{
                    fwrite($arquivo_log, "Produto não encontrado");
                }
                
                $produto = new Produto();
                $produto->codigo_fabricante = $linhaArray[11];
                $produto->codigo_global     = $codigo_global;
                $produto->codigo_similar    = $linhaArray[9];
                $produto->nome              = substr($linhaArray[3], 0, 150);
                $produto->aplicacao         = $linhaArray[17];
                $produto->peso              = $linhaArray[4];
                $produto->altura            = $linhaArray[5];
                $produto->largura           = $linhaArray[6];
                $produto->profundidade      = $linhaArray[7];
                $produto->subcategoria_id   = $linhaArray[13];
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
                        $valorProdutoFilial->valor              = (float)str_replace(",",".",$linhaArray[23]);
                        $valorProdutoFilial->valor_cnpj         = (float)str_replace(",",".",$linhaArray[23]);
                        $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
                        if($valorProdutoFilial->save()){
                            fwrite($arquivo_log, " - Valor criado");
                        }
                        else{
                            fwrite($arquivo_log, " - Valor não criado");
                        }
                    }

                    $caminhoImagem          = "/var/tmp/vannucci_mariana_115-134/com_logo/".$produto->codigo_fabricante.".jpg";
                    $caminhoImagemSemLogo   = "/var/tmp/vannucci_mariana_115-134/sem_logo/".$produto->codigo_fabricante.".jpg";
                    
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
