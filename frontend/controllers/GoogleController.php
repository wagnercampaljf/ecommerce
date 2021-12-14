<?php
namespace frontend\controllers;

use Yii;
use yii\base\Controller;
use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use console\controllers\actions\omie\Omie;
use common\models\ProdutoFilial;

class GoogleController extends Controller
{
    public function actionIndex()
    {
        //Yii::$app->user->setReturnUrl(Url::to(['site/index']));

        return "google-site-verification: google6cbbae050be36cee.html";
    }
}


