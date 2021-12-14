<?php

namespace lojista\controllers;

use common\models\PedidoSkyhub;
use common\models\PedidoSkyhubSearch;
use common\models\Usuario;
use lojista\models\Pedido;
use lojista\models\PedidoProdutoFilial;
use lojista\models\PedidoSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * PedidosController implements the CRUD actions for Pedido model.
 */
class PedidosController extends Controller
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                'imprimir-etiqueta' => 'lojista\controllers\actions\pedido\ImprimirEtiquetaAction',
                'solicitar-etiqueta' => 'lojista\controllers\actions\pedido\SolicitarEtiquetaAction',
                'fechar-etiqueta' => 'lojista\controllers\actions\pedido\FecharEtiquetaAction',
                'buscar-cliente' => 'lojista\controllers\actions\pedido\BuscarClienteAction',
            ]
        );
    }

    /**
     * Lists all Pedido models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PedidoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $skyhubSearchModel = new PedidoSkyhubSearch();
        $skyhubDataProvider = $skyhubSearchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'skyhubDataProvider' => $skyhubDataProvider,
            'skyhubSearchModel' => $skyhubSearchModel
        ]);
    }

    /**
     * Displays a single Pedido model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionView($id)
    {
        $user = Usuario::findOne(\Yii::$app->user->id);
        $model = $this->findModel($id);
        $pedido = Pedido::find()->where(['id' => $id])->andWhere(['filial_id' => $user->filial_id])->one();

        if (is_null($pedido)) {
            throw new \yii\web\ForbiddenHttpException('Sem permissão');
        }


        $dataProvider = new ActiveDataProvider([
            'query' => PedidoProdutoFilial::find()->with([
                'produtoFilial',
                'produtoFilial.produto'
            ])->where(['pedido_id' => $id]),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => false,
        ]);

        return $this->render('view', [
            'model' => $model,
            'pedidoStatus' => $model->status,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSkyhubView($code)
    {
        $pedido = PedidoSkyhub::findOne($code);

        return $this->render('view/_b2w', [
            'model' => $pedido,
        ]);
    }


    /**
     * Updates an existing Pedido model.
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
     * Deletes an existing Pedido model.
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
     * Altera status do pedido
     *
     * @param $id
     * @param $status
     * @return mixed
     * @throws \yii\web\ForbiddenHttpException
     * @author Vitor Horta 26/03/2015
     * @since 0.1
     */
    public function actionMudarStatus($id, $status)
    {
        $user = Usuario::findOne(\Yii::$app->user->id);
        $pedido = Pedido::find()->where(['id' => $id])->andWhere(['filial_id' => $user->filial_id])->one();

        if (is_null($pedido)) {
            throw new \yii\web\ForbiddenHttpException('Sem permissão');
        }

        $pedido->mudarStatus($status);

        return $this->redirect(Url::to(['/pedidos/view', 'id' => $pedido->id]));
    }

    public function actionMudarStatusSkyhub($id, $status)
    {
        $user = Usuario::findOne(\Yii::$app->user->id);
        $pedido = PedidoSkyhub::find()->where(['id' => $id])->andWhere(['filial_id' => $user->filial_id])->one();

        if (is_null($pedido)) {
            throw new \yii\web\ForbiddenHttpException('Sem permissão');
        }

        $pedido->mudarStatus($status);

        return $this->redirect(Url::to(['/pedidos/skyhub-view', 'code' => $pedido->id]));
    }


    /**
     * Finds the Pedido model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pedido the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pedido::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
