<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class ImportacaoPrecosBonfanteAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";

        $LinhasArray = Array();
        //$file = fopen('/home/dev_peca_agora/valor_produtos_bonfante.csv', 'r');
        $file = fopen('/var/tmp/produtos_19_99_peca_agora_sp_acabamentos_precificado.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_importar_precos_bonfante.csv")){
            unlink("/var/tmp/log_importar_precos_bonfante.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_importar_precos_bonfante.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "produto_id;codigo_global;produto_filial_id;valor;status\n");

        foreach ($LinhasArray as &$linhaArray){
            $codigo = str_replace(' ','',$linhaArray[0]);

            if ($codigo == null or $codigo == ""){
                // Escreve no log
                fwrite($arquivo_log, ';'.$linhaArray[0].';;;"Sem codigo_referencia"'."\n");
            }
            else {

                echo "Produto encontrado para código - "; print_r($codigo); echo " - Valor - ".$linhaArray[7]."\n";

                $produtoFilial = ProdutoFilial::find()->andWhere(['=', 'filial_id', 86])
                                                      ->andWhere(['=', 'produto.codigo_fabricante', $codigo])
                                                      ->joinWith('produto')
                                                      ->one();
                $produtoFilialId        = null;
                $produtoFilialStatus    = "Não existe PRODUTO_FILIAL";

                if ($produtoFilial){
                    $valorProdutoFilial                     = New ValorProdutoFilial;
                    $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
                    $valorProdutoFilial->valor              = str_replace(',','.',$linhaArray[7]);
                    $valorProdutoFilial->valor_cnpj         = str_replace(',','.',$linhaArray[7]);
                    $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
                    $valorProdutoFilial->promocao           = false;
                    $valorProdutoFilial->save();
                    $produtoFilialId = $valorProdutoFilial->id;

                    $produtoFilialStatus    = "Existe PRODUTO_FILIAL";
                }
                else{
                    continue;
                }

                // Escreve no log
                fwrite($arquivo_log, $produtoFilial->produto->id.";".$produtoFilial->produto->codigo_global.";".$produtoFilialId.";".$linhaArray[7].";Valor Alterado!;".$produtoFilialStatus."\n");

            }
        }

        // Fecha o arquivo
        fclose($arquivo_log); 

        //print_r($LinhasArray);

        echo "\n\nFIM da rotina de criação preço!";
    }
}
