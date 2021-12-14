<?php

namespace backend\controllers;

use Yii;
use backend\models\NotaFiscalPedidoProduto;
use backend\models\NotaFiscalPedidoProdutoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use backend\models\NotaFiscal;
use backend\models\NotaFiscalProduto;
use yii\db\Query;

/**
 * NotaFiscalPedidoProdutoController implements the CRUD actions for NotaFiscalPedidoProduto model.
 */
class NotaFiscalPedidoProdutoController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all NotaFiscalPedidoProduto models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NotaFiscalPedidoProdutoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NotaFiscalPedidoProduto model.
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
     * Creates a new NotaFiscalPedidoProduto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new NotaFiscalPedidoProduto();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing NotaFiscalPedidoProduto model.
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
     * Deletes an existing NotaFiscalPedidoProduto model.
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
     * Finds the NotaFiscalPedidoProduto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NotaFiscalPedidoProduto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NotaFiscalPedidoProduto::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionValidarProduto()
    {
        var_dump($_POST); die;
        $nota = NotaFiscal::findOne(NotaFiscalProduto::findOne($_POST['id_nota_produto'])->nota_fiscal_id);
        if (isset($_POST['keylist'])) {
            foreach ($_POST['keylist'] as $id_pedido_produto) {

                $model = NotaFiscalPedidoProduto::findOne($id_pedido_produto);
                $model->nota_fiscal_produto_id = $_POST['id_nota_produto'];
                $model->e_validado = true;
                $model->save();
            }
            return $this->redirect("../nota-fiscal-produto/index?id=" . $nota->id);
        }
    }
}
