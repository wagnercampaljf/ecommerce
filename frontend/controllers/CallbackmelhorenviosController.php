<?php
namespace frontend\controllers;

use Yii;
use yii\base\Controller;
use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use console\controllers\actions\omie\Omie;
use common\models\ProdutoFilial;

class CallbackmelhorenviosController extends Controller
{

    const APP_KEY_OMIE_SP              = '468080198586';
    const APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';
    const APP_KEY_OMIE_MG              = '469728530271';
    const APP_SECRET_OMIE_MG           = '6b63421c9bb3a124e012a6bb75ef4ace';

    public function actionIndex()
    {
        //Yii::$app->user->setReturnUrl(Url::to(['site/index']));

        return $this->render('index');
    }

    public function actionMelhorenvios(){

        $json = file_get_contents('php://input');
        $post_ml = json_decode($json);

        print_r($post_ml);
        echo 123;

    }
}


