<?php
/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 28/08/17
 * Time: 14:11
 */

namespace console\controllers;

use common\models\ProdutoFilial;
use console\models\SkyhubClient;
use yii\console\Controller;

class SkyhubController extends Controller
{
    public function actions()
    {
        return [
            'create' 		=> 'console\controllers\actions\skyhub\CreateAction',
            'delete' 		=> 'console\controllers\actions\skyhub\DeleteAction',
            'disable' 		=> 'console\controllers\actions\skyhub\DisableAction',
            'enable' 		=> 'console\controllers\actions\skyhub\EnableAction',
            'list' 		=> 'console\controllers\actions\skyhub\ListAction',
            'sinc-orders' 	=> 'console\controllers\actions\skyhub\SincOrdersAction',
	    'createuni' 	=> 'console\controllers\actions\skyhub\CreateuniAction',
	    'createpreco' 	=> 'console\controllers\actions\skyhub\CreatePrecoAction',
        ];
    }

}
