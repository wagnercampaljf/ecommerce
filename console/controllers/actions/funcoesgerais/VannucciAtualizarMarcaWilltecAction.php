<?php 
  
namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\MarcaProduto;
use common\models\ProdutoFilial;

class VannucciAtualizarMarcaWilltecAction extends Action
{
    public function run(){

        echo "INICIO DA ATUALIZAÇÃO DA MARCA ";

        $produto_filiais            =      ProdutoFilial::find()  ->joinWith('produto')
                                                                  ->andWhere(['=','produto_filial.filial_id',38])            //                                                     
                                                                  ->andWhere(["like","produto.codigo_fabricante",'%603',false])
                                                                  ->all();

           
        foreach($produto_filiais as $i =>$produto_filial){

            //echo "\n".$produto_filial->produto->codigo_fabricante."\n";
            //die;
            //Imprime no console
            echo "\n".$i." - ".$produto_filial->produto->codigo_fabricante."\n";            
           
        }
    }
}

?>