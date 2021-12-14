<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;

class AtualizarNomeIvecoAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do nome: \n\n";
        
        $arquivo_log = fopen("/var/tmp/log_alterar_nome.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;nome;status\n");
        
        $produtos = Produto::find() ->andWhere(['like', 'nome', 'Iveco'])
                                    ->andWhere(['not like', 'nome', 'PARA IVECO'])
                                    ->orderBy(["id"=>SORT_ASC])
                                    ->all();
        
        foreach ($produtos as $k => $produto){
            echo "\n".$k." - ".$produto->id." - ".$produto->nome;
            $produto->nome = substr(str_replace('Iveco', 'PARA IVECO', $produto->nome),0,150);
            if($produto->save()){
                echo " - Nome alterado";
                
                $produto->atualizarMLNome();
            }
            else{
                echo " - Nome não alterado";
            }
        }
        
        echo "\n\nFIM da rotina de atualizacao do nome!";
    }
}