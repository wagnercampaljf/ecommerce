<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class CorrecaoGaussAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";

        $LinhasArray = Array();
        $file = fopen('/var/tmp/gauss_correcao.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        $produtos = Produto::find() ->andWhere(["=","fabricante_id",86])
				    ->all();

        foreach ($produtos as $i => $produto){

            echo $i . " - " . $produto->codigo_global;

            $codigo = explode("-",$produto->codigo_global);

            $multiplicador = 1;
            if (array_key_exists(1, $codigo)){
                $multiplicador = (integer) str_replace("CX","",$codigo[1]);
            }

            foreach ($LinhasArray as $k => &$linhaArray ){

                if ($linhaArray[1]==$codigo[0]){
                    echo " - encontrado";

                    $produto->peso              = str_replace(",",".",$linhaArray[12])*$multiplicador;
                    $produto->altura            = $linhaArray[7]/10;
                    $produto->largura           = $linhaArray[9]/10*$multiplicador;
                    $produto->profundidade      = $linhaArray[8]/10;
                    $produto->codigo_barras     = $linhaArray[6];
                    $produto->multiplicador     = $multiplicador;
                    echo " - ";var_dump($produto->save());

                    break;
                }
            }
            echo "\n";
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
