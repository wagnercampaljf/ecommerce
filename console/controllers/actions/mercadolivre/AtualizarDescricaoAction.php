<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Produto;

class AtualizarDescricaoAction extends Action
{
    public function run()
    {
        $produtos = Produto::find() ->orWhere(["like", "nome", "IVECO"])
                                    ->orWhere(["like", "aplicacao", "IVECO"])
                                    ->orWhere(["like", "aplicacao_complementar", "IVECO"])
                                    ->orWhere(["like", "descricao", "IVECO"])
				    ->andWhere(["id" => [8728, 364694]])
                                    ->orderBy(["id"=>SORT_ASC])
                                    //->limit(10)
                                    ->all();

        foreach($produtos as $k => $produto){
            echo "\n".$k." - ".$produto->id;
	    //continue;

	    if($k < 0){
		echo " - Pular";
		continue;
	    }

            $produto->atualizarMLDescricao();
            //print_r($produto->atualizarMLNome());
        }
    }
}
