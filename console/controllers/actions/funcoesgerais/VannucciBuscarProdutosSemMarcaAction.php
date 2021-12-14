<?php 
namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use yii\base\ErrorException;

class VannucciBuscarProdutosSemMarcaAction extends Action{

    public function run(){
        
        echo "Buscando Produtos sem Marca Vannucci \n";

        $arquivo_log = "/var/tmp/log_vannucci_sem_marcas_".date("Y-m-d_H-i-s").".csv";
        $log = fopen($arquivo_log, "a");        


        $produto   =    Produto::find()     ->leftJoin('produto_filial','produto_filial.produto_id=produto.id')
                                            //->andWhere(['=','produto_filial.produto_id','produto.id'])
                                            ->where(['is','produto.marca_produto_id',NULL])
                                            ->andWhere(['=','produto_filial.filial_id',38])
                                            //->andWhere(['order by','produto_filial.produto_id','ASC'])
                                            ->orderBy(['produto_filial.id' => SORT_ASC])
                                            ->all();

         
        // $produto   =      Produto::find()       ->leftJoin('produto_filial')
        //                                         ->andWhere(['=','produto_filial.filial_id',38])
        //                                         ->andWhere(['is', 'produto.marca_produto_id',null])
        //                                         ->orderBy(['produto_filial.id' => SORT_ASC])
        //                                         ->all();
        // die();                                   
       foreach($produto as $k => $produtos){

        print_r($produtos);
        die;

            //fwrite($ $log, "produto_filial_id;permalink;status");

        }                                     
    }

}

?>