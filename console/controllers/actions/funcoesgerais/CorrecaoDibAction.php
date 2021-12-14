<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class CorrecaoDibAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/DIB_correcao_25-07-2019.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            
            if ($k <= 0){
                continue;
            }
            
            echo "\n".$k." - ".$linhaArray[0];
            //continue;
            
            $produto = Produto::find()->andWhere(['=','codigo_fabricante','D'.$linhaArray[0]])->one();

            if ($produto){
                echo " - ".$produto->id;
                
                $codigo_global              = $linhaArray[3];
		//var_dump($codigo_global);
                if($codigo_global == ""){
                    $codigo_global = $linhaArray[0];
                }
                
                $peso = str_replace(" ","",str_replace(",",".",$linhaArray[8]));
                if($peso != "" && $peso != "0" && $peso != "0.0" && $peso != "0.00" && $peso != "0.000" ){
                    $produto->peso = $peso;
                    echo " - ".$produto->peso;
                }
                
                $largura = str_replace(" ","",str_replace(",",".",$linhaArray[9]));
                if($largura != "" && $largura != "0" && $largura != "0.0" && $largura != "0.00" && $largura != "0.000"){
                    $produto->largura = $largura;
                    echo " - ".$produto->largura;
                }
                
                $profundidade = str_replace(" ","",str_replace(",",".",$linhaArray[10]));
                if ($profundidade != "" && $profundidade != "0" && $profundidade != "0.0" && $profundidade != "0.00" && $profundidade != "0.000"){
                    $produto->profundidade = $profundidade;
                    echo " - ".$produto->profundidade;
                }
                 
                if ($linhaArray[7] != ""){
                    $dimensoes          = explode("CM", $linhaArray[7]);
                    $altura             = str_replace(",",".",str_replace(", ",",0", $dimensoes[0]));
                    $produto->altura    = $altura;
                    echo " - ".$produto->altura;
                }

		$ipi = $linhaArray[15];
		if($linhaArray[15] == " - "){
			$ipi = null;
		}

                var_dump($linhaArray[15]);
                $produto->cest              	= $linhaArray[14];
                $produto->ipi               	= $ipi;
                $produto->codigo_montadora  	= $linhaArray[13];
		$produto->codigo_global		= $codigo_global;
                $this->slugify($produto);

		if(!$produto->save()){
			echo " - Não salvou";
			$produto->codigo_global	= $codigo_global.".";
			if(!$produto->save()){
				echo ", Não salvou ponto";
                        	$produto->codigo_global = $codigo_global.",";
				var_dump($produto->save());
                	}
		}
		else{
			echo " - Salvou";
		}

                /*$produto_filial = ProdutoFilial::find() ->andWhere(['=', 'produto_id', $produto->id])
							->andWhere(['<>', 'filial_id', 43])
							->one();
                if ($produto_filial){
                    $estoque                    = $linhaArray[6];
                    if ($estoque == "PRONTA ENTREGA"){
                        $estoque = 99999;
                    }
                    $produto_filial->quantidade = $estoque;
                    $produto_filial->save();
                    
                    echo " - ".$produto_filial->quantidade;
                }
                else{
                    echo " - Estoque não encontrado";
                }*/
                
            }
            else{
                echo " - Produto não encontrado";
            }
        }
        
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
