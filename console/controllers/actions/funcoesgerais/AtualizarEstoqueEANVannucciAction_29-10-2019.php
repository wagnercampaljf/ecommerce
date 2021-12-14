<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class AtualizarEstoqueEANVannucciAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do preço: \n\n";

        $LinhasArray = Array();
        $file = fopen("/var/tmp/vannucci_estoque_ean.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_vannucci_estoque_ean.csv")){
            unlink("/var/tmp/log_vannucci_estoque_ean.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_vannucci_estoque_ean.csv", "a");

        foreach ($LinhasArray as $i => &$linhaArray){

            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'"');
            echo "\n".$i." - ".$linhaArray[4].' - '.$linhaArray[2].' - '.$linhaArray[6].' - '.$linhaArray[7];

            if ($i <= 0)
            {
                fwrite($arquivo_log, ';ESTOQUE_PECA;STATUS');
                continue;
            }

            $codigo_fabricante = $linhaArray[4];

            $produtoFilial_fabricante = ProdutoFilial::find()   ->joinWith('produto')
                                                    ->andWhere(['=','produto_filial.filial_id',38])
                                                    ->andWhere(['=','produto.codigo_fabricante', $codigo_fabricante])
                                                    ->one();
            if ($produtoFilial_fabricante) {

                echo " - ".$produtoFilial_fabricante->quantidade." - Encontrado";
                fwrite($arquivo_log, ";".$produtoFilial_fabricante->quantidade.';Produto encontrado Fabricante');
                //continue;

                $quantidade = $linhaArray[7];

                $produtoFilial_fabricante->quantidade = $quantidade;
                if($produtoFilial_fabricante->save()){
                    echo " - quantidade atualizada";
                }
                else{
                    echo " - quantidade não atualizada";
                }
            }
            else{
                $codigo_global = $linhaArray[2];

                $produtoFilial_global = ProdutoFilial::find()   ->joinWith('produto')
                                                                    ->andWhere(['=','produto_filial.filial_id',38])
                                                                    ->andWhere(['=','produto.codigo_global', $codigo_global])
                                                                    ->one();
                if ($produtoFilial_global) {

                    echo " - ".$produtoFilial_global->quantidade." - Encontrado";
                    fwrite($arquivo_log, ";".$produtoFilial_global->quantidade.';Produto encontrado Global');
                    //continue;

                    $quantidade = $linhaArray[7];

                    $produtoFilial_global->quantidade = $quantidade;
                    if($produtoFilial_global->save()){
                    echo " - quantidade atualizada";
                    }
                    else{
                    echo " - quantidade não atualizada";
                    }
                }
                else{
                    echo " - Estoque não encontrado";
                    fwrite($arquivo_log, ';;Estoque Não encontrado');
                }
            }
        }

        // Fecha o arquivo
        fclose($arquivo_log); 

        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








