<?php

namespace backend\controllers;

use common\models\Banner;
use common\models\CategoriaBanner;
use common\models\Cidade;
use common\models\Fabricante;
use common\models\Produto;
use common\models\Subcategoria;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * BannerController implements the CRUD actions for Banner model.
 */
class BannerController extends Controller
{
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
                'rules' => [
                    [
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
            'create' => 'backend\actions\banner\CreateAction',
            'update' => 'backend\actions\banner\UpdateAction',
        ];
    }

    /**
     * Lists all Banner models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Banner::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Banner model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Deletes an existing Banner model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @author Igor Mageste 15/10/2015
     * @param $q
     * @return array
     */
    public function actionGetFabricante($q)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $subcategoria_id = Yii::$app->request->get('subcategoria_id');
        $results = Fabricante::find()->select(['fabricante.id', 'fabricante.nome as text'])->andWhere([
            'like',
            'lower(fabricante.nome)',
            strtolower($q)
        ])->bySubcategoria($subcategoria_id)->limit(10)->createCommand()->queryAll();
        $out['results'] = array_values($results);

        return $out;
    }

    /**
     * @author Igor Mageste 15/10/2015
     * @param $q
     * @param $fabricante_id
     * @return array
     */
    public function actionGetProduto($q, $fabricante_id)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $subcategoria_id = Yii::$app->request->get('subcategoria_id');
        $fabricante_id = empty($fabricante_id) ? null : $fabricante_id;
        $results = Produto::find()->select(['produto.id', 'produto.nome as text'])->andWhere([
            'like',
            'lower(produto.nome)',
            strtolower($q)
        ])->byFabricante($fabricante_id)->bySubCategoria($subcategoria_id)->limit(10)->createCommand()->queryAll();
        $out['results'] = array_values($results);

        return $out;
    }

    /**
     * @author Igor Mageste 15/10/2015
     * @param $q
     * @return array
     */
    public function actionGetCidade($q)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = Cidade::find()->select(['id', 'nome as text'])->andWhere([
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
    public function actionGetSubCategoria($q)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $results = SubCategoria::find()->select(['subcategoria.id', 'subcategoria.nome as text'])->andWhere([
            'like',
            'lower(subcategoria.nome)',
            strtolower($q)
        ])->limit(10)->createCommand()->queryAll();
        $out['results'] = array_values($results);

        return $out;
    }

    /**
     * @author Otavio 22/04/2016
     * @param $q
     * @return array
     */
    public function actionGetCategoriaBanner($q)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $results = CategoriaBanner::find()->select(['categoria_banner.id', 'categoria_banner.nome as text'])->andWhere([
            'like',
            'lower(categoria_banner.nome)',
            strtolower($q)
        ])->limit(10)->createCommand()->queryAll();
        $out['results'] = array_values($results);

        return $out;
    }

    /**
     * Finds the Banner model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Banner the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        if (($model = Banner::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
