<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Produto;

class AtualizarVideoAction extends Action
{
    public function run()
    {
        $produtos = Produto::find() ->where(" video like 'https://www.youtube.com/watch?v=rqvlr169tfE' and id in (select distinct produto_id from produto_filial where meli_id is not null and filial_id in (95,98,78,94,77,43,62,76,97,99,69,59,84,86,38,96,8,60,72) ) ")
                                    ->orderBy(["id"=>SORT_ASC])
                                    //->limit(10)
                                    ->all();

        foreach($produtos as $k => $produto){
            echo "\n".$k." - ".$produto->id." - ".$produto->video;
	    //continue;

	    if($k < 28750){
		echo " - Pular";
		continue;
	    }

            $produto->atualizarMLVideo();
	    //die;
            //print_r($produto->atualizarMLNome());
        }
    }
}
