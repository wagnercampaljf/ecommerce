<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 27/06/2016
+ * Time: 18:54
 */
namespace console\controllers\actions\mercadolivre;

use common\models\Produto;

class AtualizarNomesAction extends Action
{
    public function run()
    {
        $produtos = Produto::find() ->andWhere(["like", "nome", "IVECO"])
				    ->andWhere(["id" => [8728, 364694]])
				    //->andWhere(["=", "id", 226431])
                                    ->orderBy(["id"=>SORT_ASC])
                                    //->limit(10)
                                    ->all();
        
        foreach($produtos as $k => $produto){
            echo "\n".$k." - ".$produto->id;

	    if($k < 0){
		echo " - Pular";
		continue;
	    }

            $produto->atualizarMLNome();
            //print_r($produto->atualizarMLNome());
        }
    }
}
