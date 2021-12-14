<?php

namespace backend\controllers;

use Yii;
use common\models\MovimentacaoEstoqueMestre;
use common\models\Processamento;
use backend\models\MovimentacaoEstoqueMestreSearch;
use backend\models\MovimentacaoEstoqueDetalheSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MovimentacaoEstoqueMestreController implements the CRUD actions for MovimentacaoEstoqueMestre model.
 */
class MovimentacaoEstoqueMestreController extends Controller
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
     * Lists all MovimentacaoEstoqueMestre models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MovimentacaoEstoqueMestreSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MovimentacaoEstoqueMestre model.
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
     * Creates a new MovimentacaoEstoqueMestre model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MovimentacaoEstoqueMestre();
        $model->salvo_por =  isset(Yii::$app->user) ? Yii::$app->user->id : 1;
        $model->salvo_em = date("d-M-Y h:i:s");
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id]);
             return $this ->redirect(['update','id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                
            ]);
        }
        
    }

    /**
     * Updates an existing MovimentacaoEstoqueMestre model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

	    $movimentacao_estoque_mestre_conferencia = MovimentacaoEstoqueMestre::find()->andWhere(["=", "e_remessa_recebida", false])->one();
	    if(!$movimentacao_estoque_mestre_conferencia){
		$processamento 			= new Processamento;
		$processamento->funcao_id	= 16;
		$processamento->save();
	    }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            $searchModelDetalhe = new MovimentacaoEstoqueDetalheSearch();
            $dataProviderDetalhe = $searchModelDetalhe->search(Yii::$app->request->queryParams, $id);

            return $this->render('update', [
                'model' => $model,
		        'searchModelDetalhe' => $searchModelDetalhe,
            	'dataProviderDetalhe' => $dataProviderDetalhe,
            ]);
        }
    }

    /**
     * Deletes an existing MovimentacaoEstoqueMestre model.
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
     * Finds the MovimentacaoEstoqueMestre model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return MovimentacaoEstoqueMestre the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MovimentacaoEstoqueMestre::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
