<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class ImportacaoMarianaKitEmpreagemAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/KITS_EMBREAGEM_KASHIMA_27-04-2020_precificado.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_KITS_EMBREAGEM_KASHIMA_27-04-2020_precificado.csv")){
            unlink("/var/tmp/log_KITS_EMBREAGEM_KASHIMA_27-04-2020_precificado.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_KITS_EMBREAGEM_KASHIMA_27-04-2020_precificado.csv", "a");
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            
            echo "\n".$k." - ";
            
            fwrite($arquivo_log, "\n".$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8].'";"'.$linhaArray[9].'";"'.$linhaArray[10].'";"'.$linhaArray[11].'";"'.$linhaArray[12].'";"'.$linhaArray[13].'";"'.$linhaArray[14].'";"'.$linhaArray[15].'";"'.$linhaArray[16].'";"'.$linhaArray[17].'";"'.$linhaArray[18].'";'.$linhaArray[19].";");
            
            if ($k <= 0){
                fwrite($arquivo_log, "Status");
                continue;
            }
            
            $codigo_fabricante = $linhaArray[0];
            $produto_fabricante = Produto::find()->andWhere(['=','codigo_fabricante',$codigo_fabricante])->one();
            if($produto_fabricante){
                fwrite($arquivo_log, " - codigo_fabricante já cadastrado");
		echo " - codigo_fabricante já cadastrado";
                continue;
            }
            echo $produto_fabricante;

            if (!Subcategoria::findOne(['id'=>(int)$linhaArray[10]])){
                fwrite($arquivo_log, " - Subcategoria não encontrado");
                echo " - subcategoria não encontrada";
		continue;
            }
            
            $codigo_global = $linhaArray[6];
            $produto = Produto::find()->andWhere(['=','codigo_global',$codigo_global])->one();
            if($produto){
                $codigo_global = $codigo_global.",";
                fwrite($arquivo_log, " - Produto encontrado");
		echo " - produto encontrado";
            }
            else{
                fwrite($arquivo_log, " - Produto não encontrado");
            }
            
            //continue;
            $produto = new Produto();
            $produto->codigo_fabricante         = $linhaArray[0];
            $produto->codigo_global             = $codigo_global;
            $produto->codigo_similar            = $linhaArray[7];
            $produto->nome                      = substr($linhaArray[1], 0, 150);
            $produto->aplicacao                 = $linhaArray[11];
            $produto->aplicacao_complementar    = $linhaArray[12];
            $produto->peso                      = $linhaArray[2];
            $produto->altura                    = $linhaArray[3];
            $produto->largura                   = $linhaArray[4];
            $produto->profundidade              = $linhaArray[5];
            $produto->subcategoria_id           = $linhaArray[10];
            $produto->codigo_montadora          = $linhaArray[8];
            $produto->multiplicador             = $linhaArray[13];
            $produto->fabricante_id             = 33;
            $this->slugify($produto);
            if ($produto->save()){
                echo " - ".$produto->id;
                
                fwrite($arquivo_log, " - Produto criado");
                
                $produtoFilial              = new ProdutoFilial();
                $produtoFilial->produto_id  = $produto->id;
                $produtoFilial->filial_id   = 96;
                $produtoFilial->quantidade  = 9999;
                $produtoFilial->envio       = 1;
                if ($produtoFilial->save()){
                    
                    fwrite($arquivo_log, " - Estoque criado");
                    
                    $valorProdutoFilial                     = New ValorProdutoFilial;
                    $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
                    $valorProdutoFilial->valor              = (float)str_replace(",",".",$linhaArray[19]);
                    $valorProdutoFilial->valor_cnpj         = (float)str_replace(",",".",$linhaArray[19]);
                    $valorProdutoFilial->valor_compra       = (float)str_replace(",",".",$linhaArray[18]);
                    $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
                    if($valorProdutoFilial->save()){
                        fwrite($arquivo_log, " - Valor criado");
                    }
                    else{
                        fwrite($arquivo_log, " - Valor não criado");
                    }
                }

		$imagem = Imagens::find()->andWhere(['=', 'produto_id', $produto->id])->one();
		if($imagem){
			continue;
		}

                $caminhoImagem          = "/var/tmp/mariana_kit_embreagem_fotos/com-logo/".$produto->codigo_fabricante.".jpg";
                $caminhoImagemSemLogo   = "/var/tmp/mariana_kit_embreagem_fotos/sem-logo/".$produto->codigo_fabricante.".jpg";
                
                if (file_exists($caminhoImagem)) {
                    echo " - ".$caminhoImagem." - EXISTE\n";
                    $imagem = new Imagens();
                    $imagem->produto_id         = $produtoFilial->produto->id;
                    $imagem->imagem             = base64_encode(file_get_contents($caminhoImagem));
                    $imagem->imagem_sem_logo    = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                    $imagem->ordem              = 1;
                    if($imagem->save()){
                        fwrite($arquivo_log, " - Valor criado");
                    }
                    else{
                        fwrite($arquivo_log, " - Valor não criado");
                    }
                }
                
            }
            else{
                fwrite($arquivo_log, " - Produto não criado");
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
