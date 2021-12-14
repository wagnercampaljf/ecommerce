<?php

namespace backend\controllers;

use common\models\AnoModelo;
use common\models\Modelo;
use common\models\Subcategoria;
use Yii;
use common\models\Produto;
use backend\models\ProdutoSearch;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\base\Action;

/**
 * ProdutoController implements the CRUD actions for Produto model.
 */
class ProdutoController extends Controller
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
            'create' 		=> 'backend\actions\produto\CreateAction',
            'update' 		=> 'backend\actions\produto\UpdateAction',
	    'updateml' 		=> 'backend\actions\produto\UpdatemlAction',
	    'criaalteraomie'    => 'backend\actions\produto\CriarAlterarOmieAction',
	    'pedidoML'          => 'backend\actions\produto\PedidoMLAction',
	    'duplicarproduto'   => 'backend\actions\produto\DuplicarProdutoAction',
	    'delete'            => 'backend\actions\produto\DeleteAction',
        ];
    }

    /**
     * Lists all Produto models.
     * @return mixed
     */
    public function actionIndex($erro = '')
    {
        $searchModel = new ProdutoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
	    'erro'         => $erro,
        ]);
    }

    /**
     * Displays a single Produto model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

//    /**
//     * Deletes an existing Produto model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * @author Otavio 04/11/2016
     * @param $q
     * @return array
     */
    public function actionGetAnoModelo($q)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $results = Modelo::find()
            ->select(['am.id', "CONCAT(modelo.nome,' ',am.nome) as text"])
            ->innerJoin("ano_modelo am", "am.modelo_id = modelo.id")
            ->andWhere([
                'like',
                'lower(modelo.nome)',
                strtolower($q)
            ])
            ->orderBy('am.nome DESC')
            ->createCommand()->queryAll();

        $out['results'] = array_values($results);
        return $out;
    }

    /**
     * Finds the Produto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Produto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        if (($model = Produto::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
