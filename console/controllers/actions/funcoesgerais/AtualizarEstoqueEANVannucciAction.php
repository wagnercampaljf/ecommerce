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
        //$file = fopen("/var/tmp/vannucci_estoque_ean.csv", 'r');
        //$file = fopen("/var/tmp/lista_vannucci_estoque_29-10-2019.csv", 'r');
	$file = fopen("/var/tmp/vannucci_estoque_27-11-2018.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

	$nome_arquivo_log = "/var/tmp/log_vannucci_estoque_27-11-2018.csv";
        if (file_exists($nome_arquivo_log)){
            unlink($nome_arquivo_log);
        }
        $arquivo_log = fopen($nome_arquivo_log, "a");

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

                echo " - Estoque encontrado";

                $produto = Produto::find()->andWhere(['=','id',$produtoFilial_fabricante->produto_id])->one();

                if($produto){
                    echo " - Produto encontrado";

                    $produto->codigo_barras     = $linhaArray[6];
                    $produto->codigo_montadora  = $linhaArray[3];
                    if($produto->save()){
                        echo " - EAN e NCM atualizados";
                        fwrite($arquivo_log, " - Estoque Atualizado");
                    }
                    else{
                        echo " - EAN e NCM não atualizada";
                        fwrite($arquivo_log, " - Estoque NÃO Atualizado");
                    }
                }
                else{
                    echo " - Produto não encontrado";
                }

                /*echo " - ".$produtoFilial_fabricante->quantidade." - Encontrado";
                fwrite($arquivo_log, ";".$produtoFilial_fabricante->quantidade.';Produto encontrado Fabricante');
                //continue;

                $quantidade = $linhaArray[7];

                $produtoFilial_fabricante->quantidade = $quantidade;
                if($produtoFilial_fabricante->save()){
                    echo " - quantidade atualizada";
                    fwrite($arquivo_log, " - Estoque Atualizado");
                }
                else{
                    echo " - quantidade não atualizada";
                    fwrite($arquivo_log, " - Estoque NÃO Atualizado");
                }*/
            }
            else{
                echo " - Estoque não encontrado";
            }
        }

        // Fecha o arquivo
        fclose($arquivo_log);

        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








