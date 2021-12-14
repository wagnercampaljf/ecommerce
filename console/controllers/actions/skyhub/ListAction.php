<?php
/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 06/09/17
 * Time: 17:14
 */

namespace console\controllers\actions\skyhub;


use console\models\SkyhubClient;
use yii\base\Action;

class ListAction extends action
{
    public function run(){
        $skyhub = new SkyhubClient();
        $products = $skyhub->products()->findAll(1, 10);
        var_dump($products);
}

}