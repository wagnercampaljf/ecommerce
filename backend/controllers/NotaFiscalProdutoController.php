<?php

namespace backend\controllers;

use Yii;
use backend\models\NotaFiscalProduto;
use backend\models\NotaFiscalProdutoSearch;
use backend\models\NotaFiscalPedidoProduto;
use backend\models\PedidoProdutoFilialCotacao;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreProduto;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;
use common\models\PedidoProdutoFilial;
use common\models\Pedido;

/**
 * NotaFiscalProdutoController implements the CRUD actions for NotaFiscalProduto model.
 */
class NotaFiscalProdutoController extends Controller
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
     * Lists all NotaFiscalProduto models.
     * @return mixed
     */
    public function actionIndex($id)
    {

        $searchModel = new NotaFiscalProdutoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

        if (Yii::$app->request->post('hasEditable')) {

            $nota_fiscal_produto_id = Yii::$app->request->post('editableKey');
            $model = NotaFiscalProduto::findOne($nota_fiscal_produto_id);

            $out = Json::encode(['output' => '', 'message' => '']);

            $posted = current($_POST['NotaFiscalProduto']);
            $post = ['NotaFiscalProduto' => $posted];

            if ($model->load($post)) {
                $model->save();
                $output = '';

                if (isset($posted['valor_real_produto'])) {
                    $output = Yii::$app->formatter->asDecimal($model->valor_real_produto, 2);
                }

                $out = Json::encode(['output' => $output, 'message' => '']);
            }
            echo $out;
            return;
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'idnota' => $id,
        ]);
    }

    /**
     * Displays a single NotaFiscalProduto model.
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
     * Creates a new NotaFiscalProduto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new NotaFiscalProduto();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing NotaFiscalProduto model.
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
     * Deletes an existing NotaFiscalProduto model.
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
     * Finds the NotaFiscalProduto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NotaFiscalProduto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NotaFiscalProduto::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetProdutoNf($id)
    {
        $model = NotaFiscalProduto::findOne(['nota_fiscal_id' => $id]);
        if ($model) {
            return $this->render('_notas-details', ['model' => $model]);
        } else {
            return '<div class="alert alert-danger">No data found</div>';
        }
    }

    public function actionValidarProduto()
    {
        if (isset($_POST['keylist'])) {
            foreach ($_POST['keylist'] as $id) {
                $model = NotaFiscalPedidoProduto::findOne($id);
                $model->e_validado = true;
                $model->nota_fiscal_produto_id = $_POST['nota_fiscal_produto_id'];
                $model->save(false);

                if ($model->pedido_mercado_livre_produto_produto_filial_id) {

                    $modelMl = PedidoMercadoLivre::findOne(
                        PedidoMercadoLivreProduto::findOne(
                            PedidoMercadoLivreProdutoProdutoFilial::findOne(
                                $model->pedido_mercado_livre_produto_produto_filial_id
                            )->pedido_mercado_livre_produto_id
                        )->pedido_mercado_livre_id
                    );

                    $modelMl->nota_fiscal_compra_id = NotaFiscalProduto::findOne($model->nota_fiscal_produto_id)->nota_fiscal_id;
                    $modelMl->save();
                    
                } else if ($model->pedido_produto_filial_cotacao_id) {
                    $modelInterno = Pedido::findOne(
                        PedidoProdutoFilial::findOne(
                            PedidoProdutoFilialCotacao::findOne(
                                $model->pedido_produto_filial_cotacao_id
                            )->pedido_produto_filial_id
                        )->pedido_id
                    );

                    $modelInterno->nota_fiscal_compra_id = NotaFiscalProduto::findOne($model->nota_fiscal_produto_id)->nota_fiscal_id;
                    $modelInterno->save();
                }
            }
            return $this->redirect(['index', 'id' => $_POST['id_nota']]);
        }
    }
}
