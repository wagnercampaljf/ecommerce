<?php

namespace backend\controllers;

use common\models\Categoria;
use linslin\yii2\curl\Curl;
use Yii;
use common\models\Subcategoria;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SubCategoriaController implements the CRUD actions for SubCategoria model.
 */
class SubCategoriaController extends Controller
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
     * Lists all SubCategoria models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Subcategoria::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SubCategoria model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionGetCategoriasMeli()
    {
        if (isset($_POST['depdrop_parents'])) {
            $categoriaID = Yii::$app->request->post('depdrop_parents')[0];
            $categoria = Categoria::find()->where(['id' => $categoriaID])->one();
            $curl = new Curl();
            $url = 'https://api.mercadolibre.com/categories/' . $categoria->meli_id;
            $response = $curl->setOption(
                CURLOPT_SSL_VERIFYPEER,
                0)->setOption(CURLOPT_HTTPHEADER, ['Content-Type: application/json'])
                ->get($url);
            $response = Json::decode($response);
            $categorias = $response['children_categories'];
            echo Json::encode(['output' => $categorias, 'selected' => '']);
        }
    }

    public function actionGetSubCategoriasMeli()
    {
        if (isset($_POST['depdrop_parents'])) {
            $categoriaID = Yii::$app->request->post('depdrop_parents')[0];
            $curl = new Curl();
            $url = 'https://api.mercadolibre.com/categories/' . $categoriaID;
            $response = $curl->setOption(
                CURLOPT_SSL_VERIFYPEER,
                0)->setOption(CURLOPT_HTTPHEADER, ['Content-Type: application/json'])
                ->get($url);
            $response = Json::decode($response);
            $categorias = $response['children_categories'];
            echo Json::encode(['output' => $categorias, 'selected' => '']);
        }
    }

    /**
     * Creates a new SubCategoria model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Subcategoria();

        $categorias = Categoria::find()->all();
        $categorias = ArrayHelper::map($categorias, 'id', 'nome');
        $post = Yii::$app->request->post();

        if ($model->load(Yii::$app->request->post())) {
            $model->setMeliId($post);
            $this->slugify($model);
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'categorias' => $categorias
        ]);
    }

    /**
     * Updates an existing SubCategoria model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $categorias = Categoria::find()->all();
        $categorias = ArrayHelper::map($categorias, 'id', 'nome');
        $post = Yii::$app->request->post();

        if ($model->load(Yii::$app->request->post())) {
            $model->setMeliId($post);
            $this->slugify($model);
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'categorias' => $categorias
        ]);
    }

    /**
     * Deletes an existing SubCategoria model.
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
     * Finds the SubCategoria model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SubCategoria the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Subcategoria::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function slugify(&$model)
    {
        $text = $model->nome;

        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        $model->slug = $text;
    }
}
