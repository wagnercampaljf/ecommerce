<?php

namespace backend\controllers;

use Yii;
use backend\models\PedidoCompraProdutoFilial;
use backend\models\PedidoCompraProdutoFilialSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ProdutoFilial;
use yii\web\Response;
use backend\models\PedidoCompra;
use common\models\Pedido;
use common\models\ValorProdutoFilial;

/**
 * PedidoCompraProdutoFilialController implements the CRUD actions for PedidoCompraProdutoFilial model.
 */
class PedidoCompraProdutoFilialController extends Controller
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
     * Lists all PedidoCompraProdutoFilial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PedidoCompraProdutoFilialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PedidoCompraProdutoFilial model.
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
     * Creates a new PedidoCompraProdutoFilial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $modelCompra = new PedidoCompra();
        $modelProduto = new PedidoCompraProdutoFilial();
        $searchModel = new PedidoCompraProdutoFilialSearch();
        $dataProvider = null;

        if ($id) {
            $modelProduto->pedido_compra_id = $id;
            $modelCompra = PedidoCompra::findOne($id);
            $modelCompra->load(Yii::$app->request->post());
            $modelCompra->save();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

            return $this->render('create', [
                'modelProduto' => $modelProduto,
                'modelCompra' => $modelCompra,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            if ($modelProduto->load(Yii::$app->request->post()) && $modelProduto->save()) {
                $modelCompra = PedidoCompra::findOne($modelProduto->pedido_compra_id);
                $modelCompra->valor_total_pedido += $modelProduto->valor_compra * $modelProduto->quantidade;
                if ($modelProduto->e_atualizar_site) {
                    $modelValorProdutoFilial = new ValorProdutoFilial();
                    $modelValorProdutoFilial->valor = $modelProduto->valor_venda;
                    $modelValorProdutoFilial->dt_inicio = date("Y-m-d H:i:s");
                    $modelValorProdutoFilial->produto_filial_id = $modelProduto->produto_filial_id;
                    $modelValorProdutoFilial->promocao = false;
                    $modelValorProdutoFilial->valor_compra = $modelProduto->valor_compra;
                    $modelValorProdutoFilial->save();
                }
                $modelCompra->save();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $modelProduto->pedido_compra_id);
                $modelProduto = new PedidoCompraProdutoFilial();
                $modelProduto->pedido_compra_id = $modelCompra->id;

                return $this->render('create', [
                    'modelProduto' => $modelProduto,
                    'modelCompra' => $modelCompra,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }
        }
    }

    /**
     * Updates an existing PedidoCompraProdutoFilial model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id = null, $idProduto = null)
    {
        $modelCompra = new PedidoCompra();
        $modelProduto = new PedidoCompraProdutoFilial();
        $searchModel = new PedidoCompraProdutoFilialSearch();
        $dataProvider = null;

        if ($id) {
            $modelCompra = PedidoCompra::findOne($id);
            if ($modelCompra->load(Yii::$app->request->post())) {
                $modelCompra->save();
            }
            if ($idProduto) {
                $modelProduto = PedidoCompraProdutoFilial::findOne($idProduto);

                if (!empty(Yii::$app->request->post())) {

                    $modelCompra->valor_total_pedido -= $modelProduto->valor_compra * $modelProduto->quantidade;
                    $modelProduto->load(Yii::$app->request->post());

                    if ($modelProduto->e_atualizar_site) {
                        $modelValorProdutoFilial = new ValorProdutoFilial();
                        $modelValorProdutoFilial->valor = $modelProduto->valor_venda;
                        $modelValorProdutoFilial->dt_inicio = date("Y-m-d H:i:s");
                        $modelValorProdutoFilial->produto_filial_id = $modelProduto->produto_filial_id;
                        $modelValorProdutoFilial->promocao = false;
                        $modelValorProdutoFilial->valor_compra = $modelProduto->valor_compra;
                        $modelValorProdutoFilial->save();
                    }
                    $modelCompra->valor_total_pedido += $modelProduto->valor_compra * $modelProduto->quantidade;
                    $modelProduto->save();
                    $modelCompra->save();
                    $modelProduto = new PedidoCompraProdutoFilial();
                }
            }
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);
            return $this->render('create', [
                'modelCompra' => $modelCompra,
                'modelProduto' => $modelProduto,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    public function actionDuplicata($id)
    {
        $modelPedidoCompra = PedidoCompra::findOne($id);
        $modelPedidoCompraProdutoFilial = PedidoCompraProdutoFilial::find()->where(['pedido_compra_id' => $id])->all();
        $newModelPedidoCompra = new PedidoCompra();

        $newModelPedidoCompra->descricao          = $modelPedidoCompra->descricao;
        $newModelPedidoCompra->data               = date("Y-m-d H:i:s");
        $newModelPedidoCompra->observacao         = $modelPedidoCompra->observacao;
        $newModelPedidoCompra->email              = $modelPedidoCompra->email;
        $newModelPedidoCompra->corpo_email        = $modelPedidoCompra->corpo_email;
        $newModelPedidoCompra->status             = 1;
        $newModelPedidoCompra->filial_id          = $modelPedidoCompra->filial_id;
        $newModelPedidoCompra->valor_total_pedido = $modelPedidoCompra->valor_total_pedido;
        $newModelPedidoCompra->save(false);

        foreach ($modelPedidoCompraProdutoFilial as $item) {

            $newModelCompraProdutoFilial = new PedidoCompraProdutoFilial();
            $newModelCompraProdutoFilial->quantidade = $item->quantidade;
            $newModelCompraProdutoFilial->valor_compra = $item->valor_compra;
            $newModelCompraProdutoFilial->valor_venda = $item->valor_venda;
            $newModelCompraProdutoFilial->pedido_compra_id = $newModelPedidoCompra->id;
            $newModelCompraProdutoFilial->produto_filial_id = $item->produto_filial_id;
            $newModelCompraProdutoFilial->observacao = $item->observacao;
            $newModelCompraProdutoFilial->e_verificado = $item->e_verificado;
            $newModelCompraProdutoFilial->e_atualizar_site = false;
            $newModelCompraProdutoFilial->save();
        }

        return $this->redirect(['/pedido-compra/index']);
    }

    /**
     * Deletes an existing PedidoCompraProdutoFilial model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $modelCompra = PedidoCompra::findOne($model->pedido_compra_id);

        $modelCompra->valor_total_pedido -= $model->valor_compra * $model->quantidade;
        $modelCompra->save();
        $model->delete();

        return $this->redirect(['pedido-compra-produto-filial/update', 'id' => $model->pedido_compra_id]);
    }

    /**
     * Finds the PedidoCompraProdutoFilial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PedidoCompraProdutoFilial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PedidoCompraProdutoFilial::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetProdutoFilial($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => 'PA' . ProdutoFilial::findOne($id)->produto->id . ' ' . ProdutoFilial::findOne($id)->produto->nome . "(" . ProdutoFilial::findOne($id)->produto->codigo_global . ")"]];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = ProdutoFilial::find()
                ->select(['produto_filial.id', "produto.nome as text"])
                ->joinWith(['produto', 'filial'])
                ->where([
                    'like',
                    'lower(produto.nome)',
                    strtolower($q)
                ])
                ->orWhere([
                    'lower(produto_filial.id::VARCHAR)' =>  strtolower($q)
                ])
                ->orWhere(['like', 'produto.nome', $q])
                ->andWhere(['<>', 'filial_id', 98])
                ->limit(10)
                ->createCommand()
                ->queryAll();
            $out['results'] = array_values($results);
        }

        return $out;
    }

    public function actionInsertGridCompraProduto($id)
    {
        $modelGrid = new PedidoCompraProdutoFilial();

        $modelGrid->load(Yii::$app->request->post());
        return $this->redirect(['create', 'id' => $id]);
    }

    public function actionAutorizarEmail($id)
    {
        $modelPedidoCompra = PedidoCompra::findOne($id);
        $modelPedidoCompraProdutoFilial = PedidoCompraProdutoFilial::find()->where(['pedido_compra_id' => $modelPedidoCompra->id])->all();

        return $this->render('enviaEmail', [
            'modelPedidoCompra' => $modelPedidoCompra,
            'modelPedidoCompraProdutoFilial' => $modelPedidoCompraProdutoFilial,

        ]);
    }

    public function actionValidarPedido()
    {
        $dadosValidacao = Yii::$app->request->post();
        if (isset($dadosValidacao['selection'])) {
            foreach ($dadosValidacao['selection'] as $dado) {
                $pcpf = PedidoCompraProdutoFilial::findOne($dado);
                $pcpf->e_verificado = true;
                $pcpf->save();
            }
        }

        return $this->redirect(['pedido-compra/view', 'id' => $dadosValidacao['PedidoCompra']['id']]);
    }
}
