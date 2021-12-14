<?php

namespace backend\controllers;

use common\models\Produto;
use yii\web\Response;
use Yii;
use common\models\Imagens;
use common\models\ImagensSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ImagensController implements the CRUD actions for Imagens model.
 */
class ImagensController extends Controller
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
        ];
    }

    /**
     * Lists all Imagens models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ImagensSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Imagens model.
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
     * Creates a new Imagens model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Imagens();
        if ($model->load(Yii::$app->request->post())) {

            $this->uploadImages($model);
//            echo "<pre>";
//            $model->validate();
//            var_dump($model->errors);
//            die;
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                die("oi2");
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Imagens model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $this->uploadImages($model);
            if ($model->save()) {
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Imagens model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionGetImg($id)
    {
        $model = $this->findModel($id);

        header('Content-Type: image/jpeg');
        return base64_decode(stream_get_contents($model->imagem));
    }

    /**
     * Finds the Imagens model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Imagens the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Imagens::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $model Imagens
     */
    private function uploadImages(&$model)
    {
        if ($file = UploadedFile::getInstance($model, 'imagem')) {
            $model->imagem = base64_encode(file_get_contents($file->tempName));
        } else {
            $model->imagem = $model->oldAttributes['imagem'];
        }
        if ($file = UploadedFile::getInstance($model, 'imagem_sem_logo')) {
            $model->imagem_sem_logo = base64_encode(file_get_contents($file->tempName));
        } else {
            $model->imagem_sem_logo = $model->oldAttributes['imagem_sem_logo'];
        }

    }

    /**
     * @author Otavio Augusto 26/09/17
     * @param $q
     * @return array
     */
    public function actionGetProduto($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => Produto::findOne($id)->nome]];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = Produto::find()
                ->select(['produto.id', 'produto.nome as text'])
                ->where([
                    'like',
                    'lower(produto.nome)',
                    strtolower($q)
                ])
                ->orWhere([
                    'lower(produto.id::VARCHAR)' =>  strtolower($q)
                ])
                ->limit(10)
                ->createCommand()
                ->queryAll();
            $out['results'] = array_values($results);
        }

        return $out;
    }
}
