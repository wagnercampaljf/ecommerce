<?php

namespace backend\controllers;

use Yii;
use frontend\models\MarkupMestre;
use backend\models\MarkupMestreSearch;
use backend\models\MarkupDetalheSearch;
use frontend\models\MarkupDetalhe;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * MarkupMestreController implements the CRUD actions for MarkupMestre model.
 */
class MarkupMestreController extends Controller
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
     * Lists all MarkupMestre models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MarkupMestreSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MarkupMestre model.
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
     * Creates a new MarkupMestre model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MarkupMestre();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MarkupMestre model.
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

            $searchModelDetalhe = new MarkupDetalheSearch();
            $dataProviderDetalhe = $searchModelDetalhe->search(Yii::$app->request->queryParams, $id);

            return $this->render('update', [
                'model' => $model,
                'searchModelDetalhe' => $searchModelDetalhe,
                'dataProviderDetalhe' => $dataProviderDetalhe,
            ]);
        }
    }

    /**
     * Deletes an existing MarkupMestre model.
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
     * Finds the MarkupMestre model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MarkupMestre the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MarkupMestre::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetMarkup($id, $valor)
    {
        $markup = '';
        if (empty($id)) {
            $markup = Yii::$app->db->createCommand("select margem from markup_detalhe md 
                            inner join markup_mestre mm on md.markup_mestre_id = mm.id 
                            where ($valor ::float between valor_minimo and valor_maximo) and mm.e_markup_padrao = true
                             order by mm.id desc 
                             limit 1")->queryScalar();
        } else {
            $markup = Yii::$app->db->createCommand("select margem from markup_detalhe md 
                            inner join markup_mestre mm on md.markup_mestre_id = mm.id 
                            where ($valor ::float between valor_minimo and valor_maximo) and mm.id = $id
                            order by mm.id desc 
                            limit 1")->queryScalar();
        }
        $markup_valor = $markup > 5 ? $markup : number_format($valor * $markup, 2, '.', '');
        return '{"markup_valor":"' . $markup_valor . '"}';
    }

    public function actionDuplicata($id)
    {
        $modelMarkupMestre = MarkupMestre::findOne($id);
        $modelMarkupDetalhe = MarkupDetalhe::find()->where(['markup_mestre_id' => $id])->all();
        $newModelMarkupMestre = new MarkupMestre();

        $newModelMarkupMestre->data_inicio              = date("Y-m-d H:i:s");
        $newModelMarkupMestre->observacao               = $modelMarkupMestre->observacao;
        $newModelMarkupMestre->e_margem_absoluta_padrao = $modelMarkupMestre->e_margem_absoluta_padrao;
        $newModelMarkupMestre->margem_padrao            = $modelMarkupMestre->margem_padrao;
        $newModelMarkupMestre->descricao                = $modelMarkupMestre->descricao;
        $newModelMarkupMestre->e_markup_padrao          = 1;
        $newModelMarkupMestre->save(false);

        foreach ($modelMarkupDetalhe as $item) {

            $modelMarkupDetalhe = new MarkupDetalhe();
            $modelMarkupDetalhe->markup_mestre_id   = $newModelMarkupMestre->id;
            $modelMarkupDetalhe->e_margem_absoluta  = $item->e_margem_absoluta;
            $modelMarkupDetalhe->valor_minimo       = $item->valor_minimo;
            $modelMarkupDetalhe->valor_maximo       = $item->valor_maximo;
            $modelMarkupDetalhe->margem             = $item->margem;
            $modelMarkupDetalhe->save();
        }

        return $this->redirect(['index']);
    }
}
