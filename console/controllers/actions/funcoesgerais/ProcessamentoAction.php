<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\Processamento;

class ProcessamentoAction extends Action
{
    public function run($id = null){

        echo "INÍCIO PROCESSAMENTO! \n\n";
        
        $procesamentos = Processamento::find()->andWhere(["is", "data_hora_inicial", null])->all();

        if(!is_null($id)){
		$procesamentos = Processamento::find()->andWhere(["=", "id", $id])->all();
	}

        foreach($procesamentos as $k => $procesamento){
            
            $procesamento->data_hora_inicial = date("Y-m-d H:i:s");
            $procesamento->save();
            
            $funcao = $procesamento->funcao->caminho.$procesamento->funcao->funcao_nome;
            
            $retorno = $funcao::run($procesamento->parametros, $procesamento->file_planilha);
            //echo "("; var_dump($retorno);  echo ")";
            
            $procesamento->status           = $retorno["status"];
            $procesamento->data_hora_final  = date("Y-m-d H:i:s");
            $procesamento->save();

        }
        
        /*$data_hora_atual = date("Y-m-d H-i-s");
        $file = fopen('/var/tmp/processamento/processamento_'.$data_hora_atual.'.csv', 'a');
        fclose($file);*/
        
        echo "\n\nFIM PROCESSAMENTO!";
    }
}
