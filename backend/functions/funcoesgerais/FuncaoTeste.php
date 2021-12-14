<?php

namespace backend\functions\funcoesgerais;

use Yii;

class FuncaoTeste
{

    public static function run()
    {
        $retorno = ["status" => "ok"];

        $data_hora_atual = date("Y-m-d H-i-s");
        $file = fopen('/var/tmp/processamento/processamento_'.$data_hora_atual.'.csv', 'a');
        fclose($file);
        
        return $retorno;
    }

}
