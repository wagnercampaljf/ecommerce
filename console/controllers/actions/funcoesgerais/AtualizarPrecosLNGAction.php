<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AtualizarPrecosLNGAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do preço: \n\n";

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=','filial_id', 60])->all();

        foreach ($produtos_filiais as $i => &$produto_filial){

            echo "\n\n".$i." - ".$produto_filial->id;

            if ($i <= 9){
                continue;
            }

            $valor_produto_filial_preco = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id',$produto_filial->id])
                                                                    ->orderBy(['id' => SORT_DESC])
                                                                    ->one();

            if($valor_produto_filial_preco){
                $preco_venda = $valor_produto_filial_preco->valor *1.05;

                $valor_produto_filial = new ValorProdutoFilial;
                $valor_produto_filial->produto_filial_id    = $produto_filial->id;
                $valor_produto_filial->valor                = $preco_venda;
                $valor_produto_filial->valor_cnpj           = $preco_venda;
                $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                $valor_produto_filial->promocao             = false;
                if($valor_produto_filial->save()){
                    echo " - OK";
                }
                else{
                    echo " - ERROR";
                }
            }
        }
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








