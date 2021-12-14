<?php

namespace frontend\controllers;

use common\models\Empresa;
use common\models\EnderecoEmpresa;
use common\models\Grupo;
use common\models\Usuario;
use vendor\iomageste\Moip\Moip;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Json;
use common\models\Lojista;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LojistaController implements the CRUD actions for Lojista model.
 */
class LojistaController extends Controller
{
    public $defaultAction = 'create';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],

            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['*'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }

    public function actions()
    {
        return [
            'create' => [
                'class' => 'frontend\controllers\actions\lojista\CreateLojistaAction',
                'modelClass' => Lojista::className(),
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ];
    }

    public function actionIntro()
    {
        return $this->render('intro');
    }

    public function actionAuthorizeMoip()
    {
        $url = 'https://api.moip.com.br/oauth/authorize';
        //$url = 'https://sandbox.moip.com.br/oauth/authorize';
        $u = Url::to([
            $url,
            'responseType' => 'CODE',
            'appId' => Moip::APPID,
            'redirectUri' => Yii::$app->urlManager->createAbsoluteUrl(['callback-api/moip'], 'https'),
            'scope' => 'CREATE_ORDERS|VIEW_ORDERS|CREATE_PAYMENTS|VIEW_PAYMENTS'
        ], 'https');

        return $this->redirect($u);
    }

    public function actionGetEndereco($cep)
    {
        $cep = str_replace('-', '', $cep);
        $cep = substr($cep, 0, 8);
        try {
            $end = file_get_contents('https://viacep.com.br/ws/' . $cep . '/json/');
        } catch (ErrorException $e) {
            return Json::encode(['error' => true]);

        }
        $endereco = Json::decode($end);
        if (!empty($endereco)) {
            return Json::encode($endereco);
        }

        return $endereco;
    }

    /**
     * Finds the Lojista model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lojista the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lojista::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
