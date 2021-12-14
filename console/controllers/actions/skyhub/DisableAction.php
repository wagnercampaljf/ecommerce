<?php
/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 14/09/17
 * Time: 17:13
 */


namespace console\controllers\actions\skyhub;


use common\models\ProdutoFilial;
use console\models\SkyhubClient;
use yii\base\Action;

class DisableAction extends Action
{
    public function run()
    {
        $disableID = '5092';
        $produtoFilial = ProdutoFilial::findOne($disableID);
        $produtoFilial->status_b2w = false;
        $produtoFilial->save();

        $skyhub = new SkyhubClient();
        echo 'Desabilitando produto...';
        $response = $skyhub->products()->disable($disableID);
//        var_dump($response);
    }

}