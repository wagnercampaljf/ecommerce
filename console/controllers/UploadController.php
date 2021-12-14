<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 06/11/2017
 * Time: 17:22
 */

namespace console\controllers;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class UploadController extends Controller
{
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'upload' => 'console\controllers\actions\upload\UploadAction',
            'create' => 'console\controllers\actions\upload\CreateAction',
            'adicionar' => 'console\controllers\actions\upload\AdicionarAction',
            'adicionar-sem-logo' => 'console\controllers\actions\upload\AdicionarSemLogoAction',
        ]);
    }
}