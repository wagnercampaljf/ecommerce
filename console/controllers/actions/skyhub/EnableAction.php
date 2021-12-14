<?php
/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 18/09/17
 * Time: 15:32
 */

namespace console\controllers\actions\skyhub;

use common\models\ProdutoFilial;
use console\models\SkyhubClient;
use yii\base\Action;

class EnableAction extends Action
{
    public function run()
    {
        $enableID = '5092';
        $produtoFilial = ProdutoFilial::findOne($enableID);
        $produtoFilial->status_b2w = true;
        $produtoFilial->save();

        $skyhub = new SkyhubClient();
        echo 'Habilitando produto...';
        $response = $skyhub->products()->enable($enableID);
//        var_dump($response);
    }

}