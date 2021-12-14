<?php

namespace backend\controllers;

use common\mail\AsyncMailer;
use common\models\PedidoProdutoFilial;
use common\models\PedidoSearch;
use common\models\PedidoSkyhubSearch;
use common\models\PedidoStatusAberto;
use console\models\SkyhubClient;
use frontend\controllers\MailerController;
use yii\filters\AccessControl;
use common\models\Usuario;
use Yii;
use common\models\Pedido;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\PedidoMercadoLivreSearch;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreProduto;

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

    /**
     * Lists all Pedido models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PedidoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        $skyhubSearchModel = new PedidoSkyhubSearch();
        $skyhubDataProvider = $skyhubSearchModel->cloudSearch(Yii::$app->request->get());
        
        //$searchModelPedidoMercadoLivre  = new PedidoMercadoLivreSearch();
        //$dataProviderPedidoMercadoLivre = $searchModelPedidoMercadoLivre->search(Yii::$app->request->get());
        
        //echo "<pre>"; print_r($skyhubDataProvider); echo "</pre>";

        return $this->render('index', [
            'dataProvider'              => $dataProvider,
            'filterModel'               => $searchModel,
            'skyhubDataProvider'        => $skyhubDataProvider,
            //'dataProviderMercadoLivre'  => $searchModelPedidoMercadoLivre,
            //'filterModelMercadoLivre'   => $dataProviderPedidoMercadoLivre,
        ]);
    }

    /**
     * Displays a single Pedido model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

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
        $skyhub = new SkyhubClient();
        $pedido = $skyhub->orders()->find($code);

        //echo "<pre>"; print_r($pedido); echo "</pre>";
        
        return $this->render('view/_b2w', [
            'model' => $pedido,
        ]);
    }
    
    public function actionMercadoLivreView($id)
    {
        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=','id',$id])->one();
        
        //echo "<pre>"; print_r($pedido); echo "</pre>";

        $dataProvider = new ActiveDataProvider([
            'query' => PedidoMercadoLivreProduto::find()->andWhere(['=','pedido_mercado_livre_id', $id]),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => false,
        ]);
        
        return $this->render('view/_mercado-livre', [
            'model' => $pedido_mercado_livre,
            'dataProvider' => $dataProvider,
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
     * @throws NotFoundHttpException if the model cannot be found
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
