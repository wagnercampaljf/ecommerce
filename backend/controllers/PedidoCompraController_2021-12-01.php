<?php

namespace backend\controllers;

use backend\models\NotaFiscal;
use Yii;
use backend\models\PedidoCompra;
use backend\models\PedidoCompraSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\PedidoCompraProdutoFilialSearch;
use common\models\Filial;
use yii\web\Response;
use common\models\ProdutoFilial;
use backend\models\PedidoCompraProdutoFilial;
use backend\models\NotaFiscalPedidoProduto;
use backend\models\NotaFiscalProduto;
use common\models\Fornecedor;
use common\models\Produto;
use console\controllers\actions\omie\Omie;

/**
 * PedidoCompraController implements the CRUD actions for PedidoCompra model.
 */
class PedidoCompraController extends Controller
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
     * Lists all PedidoCompra models.
     * @return mixed
     */
    public function actionIndex($mensagem = null)
    {
        $searchModel = new PedidoCompraSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'mensagem' => $mensagem
        ]);
    }

    /**
     * Displays a single PedidoCompra model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $searchModel = new PedidoCompraProdutoFilialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new PedidoCompra model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PedidoCompra();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/pedido-compra-produto-filial/create', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PedidoCompra model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $searchModel = new PedidoCompraProdutoFilialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Deletes an existing PedidoCompra model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $modelPedidoCompraProdutoFilial = PedidoCompraProdutoFilial::find()->where(['pedido_compra_id' => $id])->all();

        foreach ($modelPedidoCompraProdutoFilial as $pedidoCompraProdutoFilial) {
            $pedidoCompraProdutoFilial->delete();
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PedidoCompra model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PedidoCompra the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PedidoCompra::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetFilial($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => Filial::findOne($id)->nome]];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = Filial::find()
                ->select(['filial.id', 'filial.nome as text'])
                ->where([
                    'like',
                    'lower(filial.nome)',
                    strtolower($q)
                ])
                ->orWhere([
                    'lower(filial.id::VARCHAR)' =>  strtolower($q)
                ])
                ->andWhere(['<>', 'id', 98])
                ->limit(10)
                ->createCommand()
                ->queryAll();
            $out['results'] = array_values($results);
        }

        return $out;
    }

    public function actionPedidoCompraAutorizar()
    {
        $dados = Yii::$app->request->post();
        $modelPedidoCompra = PedidoCompra::findOne($dados['PedidoCompra']['id']);
        $modelPedidoCompraProdutoFilial = PedidoCompraProdutoFilial::findAll(array("pedido_compra_id" => $dados['PedidoCompra']['id']));
        $modelPedidoCompra->corpo_email = $dados['PedidoCompra']['corpo_email'];
        $modelPedidoCompra->save();

        $descricao = '';

        foreach ($modelPedidoCompraProdutoFilial as $produto) {
            $modelNFProdutoValidacao = new NotaFiscalPedidoProduto();
            $modelNFProdutoValidacao->pedido_compras_produto_filial_id = $produto->id;
            $modelNFProdutoValidacao->save();
            $descricao .= ProdutoFilial::findOne($produto->produto_filial_id)->produto->codigo_fabricante . " * " . $produto->quantidade . ' und(s) - ';
        }


        $emails = str_replace(";", ",", str_replace(" ", "", $modelPedidoCompra->email));
        $emails_destinatarios = explode(",", $emails);

        $key = array_search('', $emails_destinatarios);

        if ($key !== false) {
            unset($emails_destinatarios[$key]);
        }

        var_dump(\Yii::$app->mailer->compose()
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
            ->setTo($emails_destinatarios)
            ->setSubject('Nº ' . $modelPedidoCompra->id . ' - ' . $modelPedidoCompra->descricao)
            ->setTextBody($modelPedidoCompra->corpo_email)
            ->send());

        $modelPedidoCompra->status = 2; //enviado
        $modelPedidoCompra->save();

        return $this->redirect(['index']);
    }

    public function actionCriarPedidoNota($cChaveNFe, $perc_nota = 100)
    {
        $result = PedidoCompra::CriarPedidoCompras($cChaveNFe, $perc_nota);
        return $this->actionIndex($result['mensagem']);
    }
}
