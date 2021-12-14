<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class ImportacaoVannucciCorrecaoAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/vannucci_320-363_correcao.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            
            if ($linhaArray[0] == "Código Original"){
                continue;
            }
            
            $codigo_global = str_replace(" ","",str_replace('-','',$linhaArray[0]));
            
            if (!Subcategoria::findOne(['id'=>(int)$linhaArray[4]])){
                continue;
            }

            $produto = Produto::find()->andWhere(['=','codigo_global',$codigo_global])->one();
            
            if (isset($produto)){
                echo "\n".$k." - ".$codigo_global." - ".$produto->id;
                
                $produto->codigo_fabricante = $linhaArray[1];
                $produto->codigo_similar    = "";
                $produto->nome              = $linhaArray[2];
                $produto->aplicacao         = $linhaArray[3];
                $produto->peso              = $linhaArray[5];
                $produto->altura            = $linhaArray[6];
                $produto->largura           = $linhaArray[7];
                $produto->profundidade      = $linhaArray[8];
                $produto->subcategoria_id   = $linhaArray[4];
                $this->slugify($produto);
                $produto->save();
                
                $produtoFilial              = ProdutoFilial::find()->andWhere(['=','produto_id',$produto->id])->one();
                if (isset($produtoFilial)){
                    echo " - ".$produtoFilial->id." - ";
                    $valorProdutoFilial                     = New ValorProdutoFilial;
                    $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
                    $valorProdutoFilial->valor              = (float)str_replace(",",".",$linhaArray[10]);
                    $valorProdutoFilial->valor_cnpj         = (float)str_replace(",",".",$linhaArray[10]);
                    $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
                    var_dump($valorProdutoFilial->save());
                }
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
