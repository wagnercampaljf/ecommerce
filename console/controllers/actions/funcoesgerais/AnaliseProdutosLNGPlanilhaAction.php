<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AnaliseProdutosLNGPlanilhaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de analise de produtos LNG: \n\n";

        $LinhasArray = Array();
        //$file = fopen('/var/tmp/lng_vericacao_12-09-2019.csv', 'r');
        $file = fopen('/var/tmp/lng_atualizado_28-08-19.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_lng_vericacao_12-09-2019_planilha.csv")){
            unlink("/var/tmp/log_lng_vericacao_12-09-2019_planilha.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_lng_vericacao_12-09-2019_planilha.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "produto_filial_id;codigo_fabricante;codigo_global;filial_id;dt_ultimo_valor;valor;status\n");

        foreach($LinhasArray as $k => $LinhaArray){

            echo "\n".$k." - ".$LinhaArray[0];

            $produtos_lng = ProdutoFilial::find()   ->joinWith('produto')
                                                    ->andWhere(['=','produto.codigo_fabricante',$LinhaArray[0]])
                                                    ->andWhere(['=','filial_id',60])
                                                    //->andWhere(['=','produto_filial.id',37726])
                                                    ->one();

            if($produtos_lng){
                echo " - Encontrado";
                fwrite($arquivo_log, $LinhaArray[0].";".$LinhaArray[1].";".$LinhaArray[2].";".$LinhaArray[3].";Produto Encontrado\n");
            }
            else{
                echo " - Não Encontrado";
                fwrite($arquivo_log, $LinhaArray[0].";".$LinhaArray[1].";".$LinhaArray[2].";".$LinhaArray[3].";Produto Não Encontrado\n");
            }
        }

        // Fecha o arquivo
        fclose($arquivo_log);

        echo "\n\nFIM da rotina de analise de produtos LNG!";
    }
}
