<?php

namespace frontend\controllers;

use common\models\Cidade;
use common\models\Fabricante;
use common\models\Filial;
use frontend\models\ProdutoSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SearchController extends Controller
{


    public $defaultAction = 'search';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionSearch()
    {

        $busca = Yii::$app->request->get()['nome'];
        $busca = str_replace(" ", "", $busca);
        if ($busca == "" || $busca == null) {
            return $this->redirect(\Yii::$app->request->referrer);
        }

        $busca_nome    = Yii::$app->request->get()['nome'];
        //print_r($busca_nome);
        $busca_nome    = str_replace("'", "", str_replace('"', '', str_replace('/', ' ', $busca_nome)));
        //print_r($busca_nome);
        $busca_array     = ['nome' => preg_replace('/\p{C}+/u', "", $busca_nome)];
        //print_r($busca_array);
        $busca_array    = ['nome' => preg_replace('/\p{C}+/u', "", Yii::$app->request->get()['nome'])];
        $searchModel     = new ProdutoSearch();
        $dataProvider     = $searchModel->search($busca_array);
        //$dataProvider = $searchModel->search(Yii::$app->request->get());
        //$dataProvider = $searchModel->searchVazio(Yii::$app->request->get());
        return $this->render('search', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
    }

    /**
     * @author Igor Mageste 15/10/2015
     * @param $q
     * @return array
     */
    public function actionGetCidade($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }

        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => Cidade::findOne($id)->nome]];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = Cidade::find()->select([
                'cidade.id',
                'cidade.nome as text'
            ])->byNome($q)->byEstado(Yii::$app->request->get('estado'))->limit(10)->createCommand()->queryAll();
            $out['results'] = array_values($results);
        }

        return $out;
    }

    /**
     * @author Igor Mageste 15/10/2015
     * @param $q
     * @return array
     */
    public function actionGetFabricante($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }

        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => Fabricante::findOne($id)->nome]];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = Fabricante::find()->select(['id', 'nome as text'])->andWhere([
                'like',
                'lower(nome)',
                strtolower($q)
            ])->limit(10)->createCommand()->queryAll();
            $out['results'] = array_values($results);
        }

        return $out;
    }

    /**
     * @author Igor Mageste 15/10/2015
     * @param $q
     * @return array
     */
    public function actionGetFilial($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => Filial::findOne($id)->nome]];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = Filial::find()->select(['filial.id', 'filial.nome as text'])->andWhere([
                'like',
                'lower(filial.nome)',
                strtolower($q)
            ])->limit(10)->createCommand()->queryAll();
            $out['results'] = array_values($results);
        }

        return $out;
    }
}
