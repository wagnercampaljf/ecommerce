<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class AtualizarPrecosDibAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen("/var/tmp/dib_estoque_15-08-19.csv", 'r');
        //$file = fopen("/var/tmp/ESTOQUE_DIB_26-08-2019_precificado.csv", 'r');
        //$file = fopen("/var/tmp/Lista_Geral_DIB_09-09-19_precificado.csv", 'r');
        $file = fopen("/var/tmp/produtos_fisica_sem_preço_2021-07-06.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        foreach ($LinhasArray as $i => &$linhaArray){
            
            if ($i <= 0){
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[1];

            $produto = Produto::find()->andWhere(['=','codigo_global', $linhaArray[0]])->one();
            
            if ($produto){
                echo " - encontrado";
                $produto_filial = ProdutoFilial::find() ->andwhere(["=", "produto_id", $produto->id])
                    ->andWhere(["=", "filial_id", 86])
                    ->one();
                if($produto_filial){
                    echo " - ".$produto_filial->id;
                    $valor_produto_filial = ValorProdutoFilial::find()->andwhere(["=", "produto_filial_id", $produto_filial->id])
                        ->orderBy(["dt_inicio" => SORT_DESC])
                        ->one();
                    if($valor_produto_filial){
                        echo " - ".$valor_produto_filial->valor;
                        $produto_filial_fibra = ProdutoFilial::find()   ->andWhere(["=", "produto_id", $produto->id])
                            //->andwhere(["=", "filial_id", 87])
                            //->andwhere(["=", "filial_id", 88])
                            //->andwhere(["=", "filial_id", 89])
                            ->andwhere(["=", "filial_id", 96])
                            ->one();
                        echo " - ".$produto_filial_fibra->id;

                        $valor_produto_filial_novo = new ValorProdutoFilial;
                        $valor_produto_filial_novo->valor = $valor_produto_filial->valor;
                        $valor_produto_filial_novo->valor_cnpj = $valor_produto_filial->valor_cnpj;
                        $valor_produto_filial_novo->valor_compra = $valor_produto_filial->valor_compra;
                        $valor_produto_filial_novo->produto_filial_id = $produto_filial_fibra->id;
                        $valor_produto_filial_novo->dt_inicio = date("Y-m-d H:i:s");
                        var_dump($valor_produto_filial_novo->save());
                    }
                }else{
                    echo " - estoque não encontrado";
                }
            }
            else{
                echo " - Não encontrado";
            }
        }
        
        echo "\n\nFIM da rotina de atualizacao do preço!";


    }





}







