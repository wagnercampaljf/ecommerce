<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;


class TesteConsoleController extends Controller
{
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'testeb2wcomlogo'           => 'console\controllers\actions\testeconsole\TesteB2WComLogoAction',
            'testeapilngrelatorio'      => 'console\controllers\actions\testeconsole\TesteAPILNGRelatorioAction',
            'teste'                     => 'console\controllers\actions\testeconsole\TesteAction',
            'testepedro'                => 'console\controllers\actions\testeconsole\TestePedroAction',
            'testepedrook'              => 'console\controllers\actions\testeconsole\TestePedroOkAction',
	    'testediogo'		=> 'console\controllers\actions\testeconsole\TesteDiogoAction',
        ]);
    }
}
