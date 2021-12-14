<?php
/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 04/09/17
 * Time: 20:39
 */

namespace console\controllers\actions\skyhub;


use common\models\ProdutoFilial;
use console\models\SkyhubClient;
use yii\base\Action;

class DeleteAction extends Action
{

    public function run()
    {
        $deleteId = '3289';
        $produtoFilial = ProdutoFilial::findOne($deleteId);
        $produtoFilial->status_b2w = null;
        $produtoFilial->save();

        $skyhub = new SkyhubClient();
        echo 'Removendo produtos...';
        $response = $skyhub->products()->remove($deleteId);
        var_dump($response);
    }

}