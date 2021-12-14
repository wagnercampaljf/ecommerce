<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class CorrecaoBRAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";

        $LinhasArray = Array();
        $file = fopen('/var/tmp/correcao_BR_09-05-2019.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        foreach ($LinhasArray as $k => &$linhaArray ){

            if ($linhaArray[0] == "id"){
                continue;
            }

            $produto = Produto::find()->andWhere(['=','id',$linhaArray[0]])->one();
            echo "\n".$k." - ".$linhaArray[0];
            print_r($produto);
            if (isset($produto)){
                echo "\n".$k." - ".$produto->id;

                $produto->codigo_fabricante = $linhaArray[1];
                $produto->codigo_similar    = $linhaArray[2];
                $produto->nome              = $linhaArray[3];
                $produto->aplicacao         = $linhaArray[4];
                $produto->peso              = $linhaArray[5];
                $produto->altura            = $linhaArray[6];
                $produto->largura           = $linhaArray[7];
                $produto->profundidade      = $linhaArray[8];
                $produto->subcategoria_id   = $linhaArray[9];
                $this->slugify($produto);
                $produto->save();
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
