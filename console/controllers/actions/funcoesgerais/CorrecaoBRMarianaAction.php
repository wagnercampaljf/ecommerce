<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class CorrecaoBRMarianaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";

        $LinhasArray = Array();
        $file = fopen('/var/tmp/produtos_novos_br_28-05-2019_mariana.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        foreach ($LinhasArray as $k => &$linhaArray ){

            if ($k <= 2){
                continue;
            }

            $produto = Produto::find()->andWhere(['=','id',$linhaArray[0]])->one();
            echo "\n".$k." - ".$linhaArray[0];
            if (isset($produto)){
                echo " - entrou!";
                $produto->nome                      = $linhaArray[1];
                $produto->codigo_global             = $linhaArray[3];
                $produto->codigo_fabricante         = $linhaArray[4];
                $produto->codigo_montadora          = $linhaArray[5];
                $produto->fabricante_id             = $linhaArray[6];
                $produto->subcategoria_id           = $linhaArray[8];
                $produto->peso                      = $linhaArray[10];
                $produto->altura                    = $linhaArray[11];
                $produto->largura                   = $linhaArray[12];
                $produto->profundidade              = $linhaArray[13];
                $produto->aplicacao                 = $linhaArray[15];
                $produto->aplicacao_complementar    = $linhaArray[16];
                $produto->codigo_barras             = $linhaArray[17];
                $this->slugify($produto);
                print_r($produto->save());
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
