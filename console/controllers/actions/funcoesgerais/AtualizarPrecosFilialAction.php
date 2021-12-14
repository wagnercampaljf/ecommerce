<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class AtualizarPrecosFilialAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do preço: \n\n";

        $LinhasArray = Array();
        $file = fopen("/var/tmp/dib_produtos_precificado.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        foreach ($LinhasArray as $i => &$linhaArray){

            if ($i == 0 || $i == 1){
                continue;
            }

            $produtoFilial = ProdutoFilial::find()->joinWith('produto')
                                                    ->andWhere(['=','filial_id',97])
                                                    ->andWhere(['=','produto.codigo_global', $linhaArray[0]])
                                                    //->andWhere(['produto_filial.id' => [176454, 164931, 181930]])
                                                    ->orderBy('produto_filial.id')
                                                    ->one();
            if ($produtoFilial) {
                echo $i." - ".$produtoFilial->id."\n";

                $valor_produto_filial = new ValorProdutoFilial;
                $valor_produto_filial->produto_filial_id    = $produtoFilial->id;
                $valor_produto_filial->valor                = $linhaArray[3];
                $valor_produto_filial->valor_cnpj           = $linhaArray[3];
                $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                $valor_produto_filial->promocao             = false;
                var_dump($valor_produto_filial->save());
            }


        }

        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}
