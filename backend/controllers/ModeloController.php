<?php

namespace backend\controllers;

use common\models\AnoModelo;
use Yii;
use common\models\Modelo;
use common\models\ModeloSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ModeloController implements the CRUD actions for Modelo model.
 */
class ModeloController extends Controller
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
     * Lists all Modelo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ModeloSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Modelo model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Modelo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Modelo();

        if (!empty(Yii::$app->request->post())) {
            $model->load(Yii::$app->request->post());
            if ($model->save()) {
                $this->registraAnos($model);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
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
     * Updates an existing Modelo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $this->atualizaAnos($model);
                return $this->redirect(['view', 'id' => $model->id]);
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
     * Deletes an existing Modelo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function registraAnos(&$model)
    {
        if (!empty($model->anoModelo)) {
            foreach ($model->anoModelo as $ano) {
                $anoModelo = new AnoModelo();
                $anoModelo->nome = $ano;
                $anoModelo->modelo_id = $model->id;
                $anoModelo->save();
            }
        }
    }

    public function atualizaAnos(&$model)
    {
        $anoModelos_id = ArrayHelper::getColumn($model->anoModelos, 'nome');
        if (!empty($model->anoModelo)) {
            foreach ($model->anoModelo as $ano) {
                if (!in_array($ano, $anoModelos_id)) {
                    $anoModelo = new AnoModelo();
                    $anoModelo->nome = $ano;
                    $anoModelo->modelo_id = $model->id;
                    $anoModelo->save();
                }
            }
        } else {
            $model->anoModelo = [];
        }
        foreach ($model->anoModelos as $anoModelo) {
            if ($anoModelo && !in_array($anoModelo, $model->anoModelo)) {
                $anoModelo->delete();
            }
        }
    }

    /**
     * Finds the Modelo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Modelo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Modelo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
