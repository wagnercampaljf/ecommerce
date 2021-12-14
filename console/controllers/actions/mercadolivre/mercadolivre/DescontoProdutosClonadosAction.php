<?php

namespace console\controllers\actions\mercadolivre;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Imagens;
use yii\helpers\ArrayHelper;

class DescontoProdutosClonadosAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/busca_produtos_clonados_completa.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            
            echo "\n".$k." - ";
            
            if($k==0){
                continue;
            }
            
            //echo " - ".$linhaArray[0] . " - ";
            
            $meli_id = explode("-", $linhaArray[0]);
            
            echo str_replace("https://produto.mercadolivre.com.br/","",$meli_id[0]).$meli_id[1]. " - " .$linhaArray[1]." - ".$linhaArray[4];
            
            //$produto_filial = ProdutoFilial::find() ->joinWith('produto', true, 'INNER JOIN')
            //                                        ->where("filial_id = 72 and (codigo_fabricante like '".$linhaArray[1]."' or codigo_fabricante like '0".$linhaArray[1]."')")
            //                                        ->one();
            
            
        
        }
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}