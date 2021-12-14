<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class ImportacaoVannucciSemTratarAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de importação da Vannucci sem tratar: \n\n";

        $LinhasArray = Array();
        $file = fopen('/var/tmp/LISTA_WAGNER_SUBIR_SEM_TRATAR_150-199_2083-MAIOR_precificado.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        foreach ($LinhasArray as $k => &$linhaArray ){

            if ($k == 0){
                continue;
            }
	    /*if ($k >= 2){
                die;
            }*/

            $codigo_global = str_replace(" ","",str_replace("-","",$linhaArray[2]));

            echo "\n".$k." - ".$codigo_global;

            if ($codigo_global <> null and $codigo_global <> ""){
                $produto = Produto::find()->andWhere(['=','codigo_global',$codigo_global])->one();

                if (isset($produto)){
                    $codigo_global = $codigo_global.",";
                }

                $produto = new Produto();
                $produto->codigo_fabricante = $linhaArray[0];
                $produto->codigo_global     = str_replace(" ", "", $codigo_global);
                $produto->nome              = substr($linhaArray[3], 0, 150);
                $produto->aplicacao         = $linhaArray[4];
                $produto->peso              = 3;
                $produto->altura            = 30;
                $produto->largura           = 30;
                $produto->profundidade      = 30;
                $produto->subcategoria_id   = 285;
                $produto->fabricante_id     = 91;
                $this->slugify($produto);
                if ($produto->save()){
                    echo " - ".$produto->id;

                    $produtoFilial              = new ProdutoFilial();
                    $produtoFilial->produto_id  = $produto->id;
                    $produtoFilial->filial_id   = 96;
                    $produtoFilial->quantidade  = 99999;
                    $produtoFilial->envio       = 1;
                    if ($produtoFilial->save()){
                        $valorProdutoFilial                     = New ValorProdutoFilial;
                        $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
                        $valorProdutoFilial->valor              = (float)str_replace(",",".",$linhaArray[8]);
                        $valorProdutoFilial->valor_cnpj         = (float)str_replace(",",".",$linhaArray[8]);
                        $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
                        $valorProdutoFilial->save();
                    }

                    if (file_exists('/var/tmp/Vannucci_sem_tratar/com_logo/'.strtoupper($produto->codigo_fabricante).".jpg")){
                        echo " - EXISTE";
                        $imagem = new Imagens();
                        $imagem->produto_id         = $produtoFilial->produto->id;
                        $imagem->imagem             = base64_encode(file_get_contents('/var/tmp/Vannucci_sem_tratar/com_logo/'.strtoupper($produto->codigo_fabricante).".jpg"));
                        $imagem->imagem_sem_logo    = (file_exists('/var/tmp/Vannucci_sem_tratar/sem_logo/'.strtoupper($produto->codigo_fabricante).".jpg") ? base64_encode(file_get_contents('/var/tmp/Vannucci_sem_tratar/sem_logo/'.strtoupper($produto->codigo_fabricante).".jpg")) : null);
                        $imagem->ordem              = 1;
                        $imagem->save();
                    }
                    if (file_exists('/var/tmp/Vannucci_sem_tratar/com_logo/'.strtoupper($produto->codigo_fabricante).".JPG")){
                        echo " - EXISTE";
                        $imagem = new Imagens();
                        $imagem->produto_id         = $produtoFilial->produto->id;
                        $imagem->imagem             = base64_encode(file_get_contents('/var/tmp/Vannucci_sem_tratar/com_logo/'.strtoupper($produto->codigo_fabricante).".JPG"));
                        $imagem->imagem_sem_logo    = (file_exists('/var/tmp/Vannucci_sem_tratar/sem_logo/'.strtoupper($produto->codigo_fabricante).".JPG") ? base64_encode(file_get_contents('/var/tmp/Vannucci_sem_tratar/sem_logo/'.strtoupper($produto->codigo_fabricante).".JPG")) : null);
                        $imagem->ordem              = 1;
                        $imagem->save();
                    }
                    if (file_exists('/var/tmp/Vannucci_sem_tratar/com_logo/'.strtoupper($produto->codigo_fabricante).".png")){
                        echo " - EXISTE";
                        $imagem = new Imagens();
                        $imagem->produto_id         = $produtoFilial->produto->id;
                        $imagem->imagem             = base64_encode(file_get_contents('/var/tmp/Vannucci_sem_tratar/com_logo/'.strtoupper($produto->codigo_fabricante).".png"));
                        $imagem->imagem_sem_logo    = (file_exists('/var/tmp/Vannucci_sem_tratar/sem_logo/'.strtoupper($produto->codigo_fabricante).".png") ? base64_encode(file_get_contents('/var/tmp/Vannucci_sem_tratar/sem_logo/'.strtoupper($produto->codigo_fabricante).".png")) : null);
                        $imagem->ordem              = 1;
                        $imagem->save();
                    }
                }
            }
        }

        echo "\n\nFIM da rotina de importação da Vannucci sem tratar!";
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
