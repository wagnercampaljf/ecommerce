<?php

namespace backend\controllers;

use common\mail\AsyncMailer;
use common\models\PedidoMercadoLivrePagamento;
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
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\PedidoMercadoLivreSearch;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreProduto;
use console\controllers\actions\omie\Omie;

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
        
        $pedido_mercado_livre_produto = PedidoMercadoLivreProduto::find()->andWhere(['=','pedido_mercado_livre_id',$id])->all();
        
        $pedido_mercado_livre_produto_pagamento = PedidoMercadoLivrePagamento::find()->andWhere(['=','pedido_mercado_livre_id',$id])->one();


        
        /*$dataProvider = new ActiveDataProvider([
            'query' => PedidoMercadoLivreProduto::find()->andWhere(['=','pedido_mercado_livre_id', $id]),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => false,
        ]);*/

        $dataProvider = new ActiveDataProvider(
            [
                'query' => PedidoMercadoLivre::find()->andWhere(['=','id', $id]),
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]
        );


        return $this->render('view/_mercado-livre', [
            'dataProvider' => $dataProvider,
            'model' => $pedido_mercado_livre,
            'produtos' => $pedido_mercado_livre_produto,
            'pagamento' => $pedido_mercado_livre_produto_pagamento,
        ]);

    }



    public function actionMercadoLivreProduto($id, $acao = 'view')

    {

        //$pedido_mercado_livre = PedidoMercadoLivre::findOne($id);

        //$model = new PedidoMercadoLivreProduto();
        //$model->load(Yii::$app->request->post());
        //echo "<pre>"; print_r($model); echo "</pre>";


        //$pedido_mercado_livre = PedidoMercadoLivre::findOne($id, $acao = 'view');
        //$pedido_mercado_livre = PedidoMercadoLivre::findOne($id);

        $pedido_mercado_livre_produto = PedidoMercadoLivreProduto::findOne($id);



        return $this->render('view/_mercado-livre-produto', [
            'model' => $pedido_mercado_livre_produto,
            //'pedido' => $pedido_mercado_livre,

        ]);
    }




    
    public function actionMercadoLivreProdutoUpdate($id)
    {

        $model = PedidoMercadoLivreProduto::findOne($id);
        
        echo "<pre>"; print_r($model); echo "</pre>";

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                //return $this->render('view/_mercado-livre-view', ['model' => $model, //'dataProvider' => $dataProvider, ]);
                return $this->redirect(['mercado-livre-view', 'id' => $model->pedido_mercado_livre_id]);
            }
        }
        
    }
    
    public function actionMercadoLivreAutorizar($id)
    {
        
        $model = PedidoMercadoLivre::findOne($id);
        
        echo "<pre>"; print_r($model->email_enderecos); echo "</pre>";die;
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                //return $this->render('view/_mercado-livre-view', ['model' => $model, //'dataProvider' => $dataProvider, ]);
                return $this->redirect(['mercado-livre-view', 'id' => $model->pedido_mercado_livre_id]);
            }
        }
        
    }
    
    public function actionMercadoLivreUpdate($id)
    {
        
        /*$model = new PedidoMercadoLivre();
        $model->load(Yii::$app->request->post());
        echo "<pre>"; print_r($model->email_texto); echo "</pre>";die;*/
        
        $model = PedidoMercadoLivre::findOne($id);
        
        //echo "<pre>"; print_r($model); echo "</pre>";
        
        if ($model->load(Yii::$app->request->post())) {
            //echo "<pre>"; print_r($model); echo "</pre>";
            if ($model->save()) {
                //return $this->render('view/_mercado-livre-view', ['model' => $model, //'dataProvider' => $dataProvider, ]);
                return $this->redirect(['mercado-livre-view', 'id' => $id]);
            }
        }
        
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
    
    public function actionGerarXMLOmie($id)
    {
        
        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=','id',$id])->one();
        if($pedido_mercado_livre){
            
            $meli = new Omie(1, 1);
            $APP_KEY_OMIE       = '468080198586';
            $APP_SECRET_OMIE    = '7b3fb2b3bae35eca3b051b825b6d9f43';
            
            $body = [
                "call" => "ConsultarPedido",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "codigo_pedido_integracao" => $pedido_mercado_livre->pedido_meli_id,
                ]
            ];
            $response_pedido = $meli->consulta_pedido("api/v1/geral/pedido/?JSON=",$body);
            //echo "<pre>"; print_r($response_pedido); echo "</pre>";
            
            $body = [
                "call" => "ConsultarNF",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "nIdPedido" => ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto.cabecalho.codigo_pedido'),
                ]
            ];
            $response_nota_fiscal = $meli->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
            //echo "<pre>"; print_r($response_nota_fiscal); echo "</pre>";
            
            $body = [
                "call" => "GetUrlNotaFiscal",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "nCodNF" => ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF'),
                ]
            ];
            $response_url_nota_fiscal = $meli->consulta("/api/v1/produtos/notafiscalutil/?JSON=",$body);
            //echo "<pre>"; print_r($response_url_nota_fiscal); echo "</pre>"; 
            
            return $this->redirect(ArrayHelper::getValue($response_url_nota_fiscal, 'body.cUrlNF'));
            
            $url_xml_nota_fiscal = ArrayHelper::getValue($response_url_nota_fiscal, 'body.cUrlNF');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url_xml_nota_fiscal);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $xml_nota_fiscal = curl_exec($ch);
            curl_close($ch);
            print_r($xml_nota_fiscal);
            
            $arquivo = fopen("/var/tmp/texte.xml", "a");
            fwrite($arquivo, $xml_nota_fiscal);
            fclose($arquivo);
                
            die;
            
        }
        
        return $this->redirect(['index']);
    }

}
