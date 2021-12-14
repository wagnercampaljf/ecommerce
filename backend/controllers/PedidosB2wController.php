<?php

namespace backend\controllers;


use common\models\PedidoProdutoFilial;

use common\models\PedidoSkyhubSearch;

use console\models\SkyhubClient;

use yii\filters\AccessControl;
use Yii;

use yii\data\ActiveDataProvider;

use yii\web\Controller;

use yii\filters\VerbFilter;


/**
 * PedidosController implements the CRUD actions for Pedido model.
 */
class PedidosB2wController extends Controller
{
    
    const APP_ID = '3029992417140266';
    const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';
    
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
   /* public function actionIndex()
    {
        if ($_SESSION['__id'] == 56) {
            Yii::$app->user->logout();
        }
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
    }*/


    public function actionIndex()
    {


        $skyhubSearchModel = new PedidoSkyhubSearch();
        $skyhubDataProvider = $skyhubSearchModel->cloudSearch(Yii::$app->request->get());


        return $this->render('index', [

            'skyhubDataProvider'        => $skyhubDataProvider,

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


}
