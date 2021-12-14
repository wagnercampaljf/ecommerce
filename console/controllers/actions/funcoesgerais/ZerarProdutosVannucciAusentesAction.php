<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class ZerarProdutosVannucciAusentesAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do preço: \n\n";

        $LinhasArray = Array();
        $file = fopen("/var/tmp/vannucci_produtos_ausentes.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_vannucci_produtos_ausentes.csv"))
        {
            unlink("/var/tmp/log_vannucci_produtos_ausentes.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_vannucci_produtos_ausentes.csv", "a");

        foreach ($LinhasArray as $i => &$linhaArray)
        {

            fwrite($arquivo_log, "\n".$linhaArray[0].';'.$linhaArray[1]);
            echo "\n".$i." - ".$linhaArray[0];

            if ($i <= 0)
            {
                fwrite($arquivo_log, ';STATUS');
                continue;
            }

            $codigo_fabricante = $linhaArray[0];

            $produtoFilial_fabricante = ProdutoFilial::find()   ->joinWith('produto')
                                                                ->andWhere(['=','produto_filial.filial_id',38])
                                                                ->andWhere(['=','produto.codigo_fabricante', $codigo_fabricante])
                                                                ->one();
            if ($produtoFilial_fabricante) 
            {
                echo " - Encontrado";
                fwrite($arquivo_log, ';Produto encontrado');

                $produtoFilial_fabricante->quantidade = 0;
                if($produtoFilial_fabricante->save())
                {
                    echo " - Estoque alterado";
                    fwrite($arquivo_log, ';Estoque alterado');
                }
                else
                {
                    echo " - Estoque NÃO alterado";
                    fwrite($arquivo_log, ';Estoque NÃO alterado');
                }
            }
            else
            {
                echo " - Produto não encontrado";
                fwrite($arquivo_log, ';Produto Não encontrado;');
            }
        }

        // Fecha o arquivo
        fclose($arquivo_log);

        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}
