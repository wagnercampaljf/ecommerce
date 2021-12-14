<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AtualizarProdutosLNGAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de comparação de produtos: \n\n";

        $LinhasArray = Array();
        $file = fopen('/home/pecaagoradev/PlanilhaLNG_subir_codigo_similar_aplicacao.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_comparar_produtos.csv")){
            unlink("/var/tmp/log_comparar_produtos.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_alterar_produtos_lng.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;codigo_global;aplicacao;aplicacao_site;codigo_similar;codigo_similar_site;status\n");

        foreach ($LinhasArray as &$linhaArray){
            $codigo_global = str_replace(' ','',$linhaArray[0]);

            if ($codigo_global == null or $codigo_global == "" or $codigo_global == "codigo_global"){
                // Escreve no log
                fwrite($arquivo_log, ";".$linhaArray[0].";".$linhaArray[1].";;".$linhaArray[2].";;Sem codigo_global\n");
            }
            else {
                $produto = Produto::find()->andWhere(['=','codigo_global',$codigo_global])->one();

                if (isset($produto)){
                    echo "Produto encontrado para código - "; print_r($codigo_global); echo " - ID - "; print_r($produto->id); echo "\n";

                    $produto->aplicacao         = $linhaArray[1];
                    $produto->codigo_similar    = $linhaArray[2];
                    $produto->save();

                    fwrite($arquivo_log, $produto->id.";".$linhaArray[0].";".$linhaArray[1].";".$produto->aplicacao.";".$linhaArray[2].";".$produto->codigo_similar.";Produto Atualizado\n");
                }
                else {
                    // Escreve no log
                    fwrite($arquivo_log, ";".$linhaArray[0].";".$linhaArray[1].";;".$linhaArray[2].";;Produto não encontrado\n");
                }
            }
        }

        // Fecha o arquivo
        fclose($arquivo_log);

        //print_r($LinhasArray);

        echo "\n\nFIM da rotina de criação preço!";
    }
}
