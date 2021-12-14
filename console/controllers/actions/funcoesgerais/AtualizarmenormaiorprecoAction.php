<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\ValorProdutoFilial;
use common\models\Produto;
use common\models\ValorProdutoMenorMaior;

class AtualizarmenormaiorprecoAction extends Action
{
    public function run($global_id){
        echo Yii::getAlias('@yii')." \n ";
       
        //$arrayid = [1113, 1184, 1223, 1330, 1643];
        //$produtos = Produto::find()->andWhere(['id' => $arrayid])->orderBy('id')->all();
        $produtos = Produto::find()
                            //->andwhere(['>','id',9100])
                            //->andWhere(['<','id',9120])
                            //->andWhere(['id' => [231135]])
                            ->orderBy('id')
                            ->all();
        echo "INÍCIO da rotina de atualização de menor preço e maior preço: \n";
        //for ($i = 0; $i < 4; $i++) {
        foreach ($produtos as $produto){
            //$produto = ArrayHelper::getValue($produtos, $i);
            //$alt = $produto->getLabel();
            $maxValue = ValorProdutoFilial::find()->ativo()->maiorValorProduto($produto->id)->one();
            $minValue = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
            if ($minValue != NULL) {
                echo $produto->id."\n";
                //echo $minValue->labelTitulo().' - ';
                //echo $maxValue->labelTitulo()."\n";
                //echo $minValue->getValorFinal(false).' - ';
                //echo $maxValue->getValorFinal(true)."\n";
                
                $menor_maior_valor = ValorProdutoMenorMaior::find()->where(['produto_id'=>$produto->id])->one();
                if ($menor_maior_valor == null){
                    $menorValor = new ValorProdutoMenorMaior;
                    $menorValor->produto_id = $produto->id;
                    $menorValor->menor_valor = $minValue->getValorFinal(false);
                    $menorValor->maior_valor = $maxValue->getValorFinal(false);
                    $menorValor->menor_valor_cnpj = $minValue->getValorFinal(true);
                    $menorValor->maior_valor_cnpj = $maxValue->getValorFinal(true);
                    $menorValor->save();
                    //echo "1 \n";
                }
                else{
                    $menor_maior_valor->produto_id = $produto->id;
                    $menor_maior_valor->menor_valor = $minValue->getValorFinal(false);
                    $menor_maior_valor->maior_valor = $maxValue->getValorFinal(false);
                    $menor_maior_valor->menor_valor_cnpj = $minValue->getValorFinal(true);
                    $menor_maior_valor->maior_valor_cnpj = $maxValue->getValorFinal(true);
                    $menor_maior_valor->save();
                    //echo "2 \n";
                }
            }
        } 
        echo "FIM da rotina de atualização de menor preço e maior preço!";
    }
}
