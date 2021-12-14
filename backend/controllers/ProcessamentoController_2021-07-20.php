<?php

namespace backend\controllers;

use Yii;
use common\models\Processamento;
use common\models\ProcessamentoSearch;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ProcessamentoController implements the CRUD actions for Processamento model.
 */
class ProcessamentoController extends Controller
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
     * Lists all Processamento models.
     *private $parametros;*/
    public function actionIndex()
    {
        $searchModel = new ProcessamentoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Processamento model.
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
     * Creates a new Processamento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Processamento();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Processamento model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Processamento model.
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
     * Finds the Processamento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Processamento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Processamento::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public  function actionCriarProcessamento()
    {
        $model = new Processamento();

        if($model->load(Yii::$app->request->post()) && $model->save()){
            $post = Yii::$app->request->post();

            //var_dump($post);die;

            $model->file_planilha = UploadedFile::getInstance($model, 'file_planilha');
            
            
            //echo '<pre>'; var_dump($model->file_planilha);die;

            $json = ["coluna_codigo_fabricante"=>$post['Processamento']['coluna_codigo_fabricante'],
             "coluna_estoque"=>$post['Processamento']['coluna_estoque'],
             "coluna_preco"=>$post['Processamento']['coluna_preco'],
             "coluna_preco_compra"=>$post['Processamento']['coluna_preco_compra'],
             "coluna_capas"=>$post['Processamento']['coluna_capas'],
             "file_planilha"=>$post['Processamento']['file_planilha']];

            print_r($json);

            $json = json_encode($json);

            print_r($json);
            $model->parametros = $json;
            $model->parametros;
            $model->save();

          //echo '<pre>'; print_r($json);die;

          
          //echo '<pre>'; print_r($model->file_planilha);die;

          
           $model->file_planilha->saveAs('uploads/' . $model->file_planilha->baseName . '.' . $model->file_planilha->extension);
           
           return $this->redirect(['view', 'id' => $model->id]);

        } else {

            return $this->render('criar-processamento', [

                'model' => $model,

            ]);
        }
    }

}
