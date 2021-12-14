<?php

namespace backend\controllers;

use Yii;
use common\models\MovimentacaoEstoqueDetalhe;
use backend\models\MovimentacaoEstoqueDetalheSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MovimentacaoEstoqueDetalheController implements the CRUD actions for MovimentacaoEstoqueDetalhe model.
 */
class MovimentacaoEstoqueDetalheController extends Controller
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
     * Lists all MovimentacaoEstoqueDetalhe models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MovimentacaoEstoqueDetalheSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MovimentacaoEstoqueDetalhe model.
     * @param int $id ID
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MovimentacaoEstoqueDetalhe model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($movimentacao_estoque_mestre_id)
    {
        $model = new MovimentacaoEstoqueDetalhe();
        $model->salvo_por =  isset(Yii::$app->user) ? Yii::$app->user->id : 1;
        $model->salvo_em = date("d-M-Y h:i:s");
        $model->movimentacao_estoque_mestre_id = $movimentacao_estoque_mestre_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
           // return $this->redirect(['view', 'id' => $model->id]);
           return $this->redirect(['movimentacao-estoque-mestre/update', 'id' => $movimentacao_estoque_mestre_id]);            
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MovimentacaoEstoqueDetalhe model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
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
     * Deletes an existing MovimentacaoEstoqueDetalhe model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MovimentacaoEstoqueDetalhe model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return MovimentacaoEstoqueDetalhe the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MovimentacaoEstoqueDetalhe::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
