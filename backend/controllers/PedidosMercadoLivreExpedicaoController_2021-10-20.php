<?php
//1111
namespace backend\controllers;

use common\mail\AsyncMailer;
use common\models\PedidoMercadoLivrePagamento;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;
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
use common\models\Filial;
use Livepixel\MercadoLivre\Meli;
use common\models\PedidoMercadoLivreProdutoProdutoFilialSearch;
use common\models\ProdutoFilial;
use common\models\Administrador;
use backend\models\NotaFiscal;
use common\models\LogExpedicao;
use backend\models\Relatorios\PreNota;

class PedidosMercadoLivreExpedicaoController extends Controller
{

    const APP_ID = '3029992417140266';
    const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';

    public $layout = "layout_limpo";

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
    public function actionIndex(
        $filtro = null,
        $filtro_status_pedido_enviado = null,
        $filtro_status_pedido_nao_enviado = null,
        $filtro_status_etiqueta_impressa = null,
        $filtro_status_etiqueta_nao_impressa = null,
        $data_inicial = null,
        $data_final = null


    ) {

        return $this->render('index', [
            "filtro" => $filtro,
            'filtro_status_pedido_enviado'         => $filtro_status_pedido_enviado,
            'filtro_status_pedido_nao_enviado'         => $filtro_status_pedido_nao_enviado,

            'filtro_status_etiqueta_impressa'         => $filtro_status_etiqueta_impressa,
            'filtro_status_etiqueta_nao_impressa'         => $filtro_status_etiqueta_nao_impressa,

            'data_inicial' => $data_inicial,
            'data_final' => $data_final,

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

    public function actionEnviarPedido($id)
    {

        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $id])->one();

        $pedido_mercado_livre->e_pedido_enviado = true;
        $pedido_mercado_livre->data_hora_envio = date("Y-m-d H:i:s");
        $pedido_mercado_livre->enviado_por = Yii::$app->user->id;
        $pedido_mercado_livre->save();

        return $this->redirect(['index', 'filtro' => null]);
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
        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $id])->one();

        //echo "<pre>"; print_r($pedido); echo "</pre>";

        $pedido_mercado_livre_produto = PedidoMercadoLivreProduto::find()->andWhere(['=', 'pedido_mercado_livre_id', $id])->all();

        $pedido_mercado_livre_produto_pagamento = PedidoMercadoLivrePagamento::find()->andWhere(['=', 'pedido_mercado_livre_id', $id])->one();



        /*$dataProvider = new ActiveDataProvider([
            'query' => PedidoMercadoLivreProduto::find()->andWhere(['=','pedido_mercado_livre_id', $id]),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => false,
        ]);*/

        $dataProvider = new ActiveDataProvider(
            [
                'query' => PedidoMercadoLivre::find()->andWhere(['=', 'id', $id]),
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

    public function actionCreate($id)
    {
        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $id])->one();

        if ($pedido_mercado_livre) {


            $model = new  PedidoMercadoLivre();

            if ($model->load(Yii::$app->request->post()) && $model->save()) {

                return $this->redirect(['pedidos-mercado-livre-expedicao/index', 'id' => $model->id]);
            } else {
                return $this->render('pedidos-mercado-livre-expedicao/index', [
                    'model' => $model,
                ]);
            }
        }
    }

    public function actionUpdateObservacaoAjax($id, $observacao)
    {
        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $id])->one();

        if ($pedido_mercado_livre) {

            $usuario_nome = "";
            if (isset(Yii::$app->user)) {
                $administrador = Administrador::find()->andWhere(["=", "id", Yii::$app->user->id])->one();
                if ($administrador) {
                    $usuario_nome = $administrador->nome . " ";
                }
            }

            $observacao_antiga     = $pedido_mercado_livre->observacao;
            $observacao_nova     = str_replace($observacao_antiga, "", $usuario_nome . date("d-m-Y H:i:s") . ":\n" . $observacao . "\n\n");
            $pedido_mercado_livre->observacao    = $observacao_antiga . $observacao_nova;
            //$pedido_mercado_livre->observacao .= $usuario_nome.date("d-m-Y H:i:s").":\n".$observacao."\n\n";
            if ($pedido_mercado_livre->save()) {
                return "Salvou";
                //return $this->redirect(['pedidos-mercado-livre-expedicao/index', 'id' => $model->id]);

            } else {
                return "Não Salvou";
                //return $this->render('pedidos-mercado-livre-expedicao/index', ['model' => $model, ]);
            }
        }
    }

    public function actionUpdateObservacaoAjaxNovo($id, $observacao)
    {


	if(is_null($observacao) || $observacao == ""){
		return substr($pedido_mercado_livre->observacao, 0);
	}

        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $id])->one();

        if ($pedido_mercado_livre) {

            $usuario_nome = "";
            if (isset(Yii::$app->user)) {
                $administrador = Administrador::find()->andWhere(["=", "id", Yii::$app->user->id])->one();
                if ($administrador) {
                    $usuario_nome = $administrador->nome . " ";
                }
            }

            $observacao_antiga     = $pedido_mercado_livre->observacao;
            //$observacao_nova     = $observacao_antiga."\n".$usuario_nome . date("d-m-Y H:i:s") . ":	" . $observacao ;
	    $observacao_nova     = $usuario_nome . date("d-m-Y H:i:s") . ":     " . $observacao."\n".$observacao_antiga ;
            $pedido_mercado_livre->observacao    = $observacao_nova;
            //$pedido_mercado_livre->observacao .= $usuario_nome.date("d-m-Y H:i:s").":\n".$observacao."\n\n";
            if ($pedido_mercado_livre->save()) {
                return substr($pedido_mercado_livre->observacao, 0);
		//return "Salvou";
                //return $this->redirect(['pedidos-mercado-livre-expedicao/index', 'id' => $model->id]);

            } else {
                return substr($pedido_mercado_livre->observacao, 0);
                //return $this->render('pedidos-mercado-livre-expedicao/index', ['model' => $model, ]);
            }
        }
    }


    public function actionUpdateObservacao($id)
    {
        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $id])->one();

        if ($pedido_mercado_livre) {

            if ($pedido_mercado_livre->load(Yii::$app->request->post()) && $pedido_mercado_livre->save()) {
                return "Salvou";
                //return $this->redirect(['pedidos-mercado-livre-expedicao/index', 'id' => $model->id]);

            } else {
                return "Salvou";
                //return $this->render('pedidos-mercado-livre-expedicao/index', ['model' => $model, ]);
            }
        }
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


        $searchModelProdutoFilial = new PedidoMercadoLivreProdutoProdutoFilialSearch();
        $dataProviderProdutoFilial = $searchModelProdutoFilial->search(['PedidoMercadoLivreProdutoProdutoFilialSearch' => ['pedido_mercado_livre_produto_id' => $id]]);

        return $this->render('view/_mercado-livre-produto', [
            'model' => $pedido_mercado_livre_produto,
            //'pedido' => $pedido_mercado_livre,
            'searchModelProdutoFilial' => $searchModelProdutoFilial,
            'dataProviderProdutoFilial' => $dataProviderProdutoFilial,
        ]);
    }

    public function actionMercadoLivreProdutoProdutoFilialView($pedido_mercado_livre_produto_id, $pedido_mercado_livre_produto_produto_filial_id)
    {

        $pedido_mercado_livre_produto_produto_filial = PedidoMercadoLivreProdutoProdutoFilial::findOne($pedido_mercado_livre_produto_produto_filial_id);


        return $this->render('view/pedido-mercado-livre-produto-produto-filial/view', [
            'model' => $pedido_mercado_livre_produto_produto_filial,
            'pedido_mercado_livre_produto_id' => $pedido_mercado_livre_produto_id,
        ]);
    }


    /*public function actionMercadoLivreProdutoProdutoFilialCreate($pedido_mercado_livre_produto_id)
    {
        $model = new PedidoMercadoLivreProdutoProdutoFilial();
        $model->pedido_mercado_livre_produto_id = $pedido_mercado_livre_produto_id;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['mercado-livre-produto', 'id' => $model->pedido_mercado_livre_produto_id]);
        } else {
            return $this->render('view/pedido-mercado-livre-produto-produto-filial/create', [
                'model' => $model,
                'pedido_mercado_livre_produto_id' => $model->pedido_mercado_livre_produto_id,
            ]);
        }
    }*/

    // ALTERAÇÃO 02-11-2020

    public function actionMercadoLivreProdutoProdutoFilialCreate($pedido_mercado_livre_produto_id)
    {
        $model = new PedidoMercadoLivreProdutoProdutoFilial();
        $model->pedido_mercado_livre_produto_id = $pedido_mercado_livre_produto_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            // echo '<pre>'; print_r($model) ; echo '</pre>';

            //return $this->redirect(['mercado-livre-produto', 'id' => $model->pedido_mercado_livre_produto_id]);



            $pedido_mercado_livre_produto = PedidoMercadoLivreProduto::find()->andWhere(['=', 'id', $pedido_mercado_livre_produto_id])->one();


            return $this->redirect(['pedidos-mercado-livre/mercado-livre-view', 'id' => $pedido_mercado_livre_produto->pedido_mercado_livre_id]);
        } else {
            return $this->render('view/pedido-mercado-livre-produto-produto-filial/create', [
                'model' => $model,
                'pedido_mercado_livre_produto_id' => $model->pedido_mercado_livre_produto_id,
            ]);
        }
    }


    public function actionMercadoLivreProdutoProdutoFilialUpdate($pedido_mercado_livre_produto_id, $pedido_mercado_livre_produto_produto_filial_id)
    {

        $model = PedidoMercadoLivreProdutoProdutoFilial::find()->andWhere(['=', 'id', $pedido_mercado_livre_produto_produto_filial_id])->one();
        //echo "<pre>"; print_r($model); echo "</pre>";die;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['mercado-livre-produto', 'id' => $model->pedido_mercado_livre_produto_id]);
        } else {
            return $this->render('view/pedido-mercado-livre-produto-produto-filial/update', [
                'model' => $model,
                'pedido_mercado_livre_produto_id' => $model->pedido_mercado_livre_produto_id,
            ]);
        }
    }


    public function actionMercadoLivreProdutoProdutoFilialDelete($pedido_mercado_livre_produto_id, $pedido_mercado_livre_produto_produto_filial_id)
    {

        $model = PedidoMercadoLivreProdutoProdutoFilial::find()->andWhere(['=', 'id', $pedido_mercado_livre_produto_produto_filial_id])->one();
        //echo "<pre>"; print_r($model); echo "</pre>";die;

        $model->delete();

        return $this->redirect(['mercado-livre-produto', 'id' => $pedido_mercado_livre_produto_id]);
        //$this->actionMercadoLivreProduto($pedido_mercado_livre_produto_id);

    }



    public function actionMercadoLivreProdutoUpdate($id)
    {

        $model = PedidoMercadoLivreProduto::findOne($id);

        echo "<pre>";
        print_r($model);
        echo "</pre>";

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

        $e_pedido_autorizado = $model->e_pedido_autorizado;

        $model->e_pedido_autorizado = true;
        $model->save();


        //die;

        /*if(isset($model->user_id)){
            if(!is_null($model->user_id) && $model->user_id != "" ){
                
                $pedidos_mercado_livre_produto = PedidoMercadoLivreProduto::find()->andWhere(['=', 'pedido_mercado_livre_id', $model->id])->all();
                
                foreach ($pedidos_mercado_livre_produto as $k => $pedido_mercado_livre_produto){

                    $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $pedido_mercado_livre_produto->produto_filial_id])->one();
                    
                    $pedidos_mercado_livre_produto_produto_filial = PedidoMercadoLivreProdutoProdutoFilial::find()->andWhere(['=', 'pedido_mercado_livre_produto_id', $pedido_mercado_livre_produto->id])->all();

                    foreach ($pedidos_mercado_livre_produto_produto_filial as $k => $pedido_mercado_livre_produto_produto_filial){

                        $produto_filial_alterado = ProdutoFilial::find()->andWhere(['=','id', $pedido_mercado_livre_produto_produto_filial->produto_filial_id])->one();
                                                
                        $omie = new Omie(static::APP_ID, static::SECRET_KEY);
                        
                        //SP Principal
                        $APP_KEY_OMIE           = '468080198586';
                        $APP_SECRET_OMIE        = '7b3fb2b3bae35eca3b051b825b6d9f43';
                        
                        if($model->user_id == "435343067"){
                            //SP Duplicada
                            $APP_KEY_OMIE       = '1017311982687';
                            $APP_SECRET_OMIE    = '78ba33370fac6178da52d42240591291';
                        }
                        
                        $body = [
                            "call" => "ConsultarPedido",
                            "app_key" => $APP_KEY_OMIE,
                            "app_secret" => $APP_SECRET_OMIE,
                            "param" => [
                                "codigo_pedido_integracao"  => $model->pedido_meli_id,
                                //"numero_pedido"  => "31571",
                            ]
                        ];
                        $response_pedido = $omie->consulta("/api/v1/produtos/pedido/?JSON=",$body);
                        if($response_pedido["httpCode"] == 200){
                            //echo "<pre>"; print_r($response_pedido); echo "</pre>";
                            //die;
                            
                            foreach($response_pedido["body"]["pedido_venda_produto"]["det"] as $j => $produto){
                                
                                //echo "<pre>"; print_r($produto); echo "</pre>"; //die;
                                
                                if(("PA".$produto_filial->produto->id) == $produto["produto"]["codigo"]){
                                    echo "<br><br><br><br><br><br><br>Mesmo produto!";
                                }
                                else{
                                    echo "<br><br><br><br><br><br><br>Produto diferente!";
                                }
                                
                                $body = [
                                    "call" => "AlterarPedidoVenda",
                                    "app_key" => $APP_KEY_OMIE,
                                    "app_secret" => $APP_SECRET_OMIE,
                                    "param" => [
                                        "cabecalho" => [
                                            "codigo_pedido_integracao"  => ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto.cabecalho.codigo_pedido_integracao'),
                                        ],
                                        "det"=> [
                                            "ide"=> [
                                                "codigo_item_integracao"    => $produto["ide"]["codigo_item_integracao"],
                                            ],
                                            "produto" => [
                                                "codigo_produto_integracao" => "PA".$produto_filial_alterado->produto->id,
                                                "quantidade"                => $produto["produto"]["quantidade"],
                                                "valor_unitario"            => $produto["produto"]["valor_unitario"],
                                                "descricao"                 => "(".$produto_filial_alterado->produto->codigo_global.") ".$produto_filial_alterado->produto->nome,
                                            ],
                                        ],
                                    ],
                                ];
                                
                                $body = [
                                    "call" => "AlterarPedidoVenda",
                                    "app_key" => $APP_KEY_OMIE,
                                    "app_secret" => $APP_SECRET_OMIE,
                                    "param" => $response_pedido["body"]["pedido_venda_produto"],
                                ];
                                
                                //echo "<pre>"; print_r($body); echo "</pre>";
                                
                                //echo "<pre>"; print_r($body["param"]["det"][0]["produto"]["codigo"]); echo "</pre>";
                                //echo "<pre>"; print_r($body["param"]["det"][0]["produto"]["codigo_produto"]); echo "</pre>";
                                //echo "<pre>"; print_r($body["param"]["det"][0]["produto"]["descricao"]); echo "</pre>";
                                
                                $body["param"]["det"][0]["produto"]["codigo"] = "PA".$produto_filial_alterado->produto->id;
                                $body["param"]["det"][0]["produto"]["codigo_produto"] = $produto_filial_alterado->produto->codigo_global;
                                $body["param"]["det"][0]["produto"]["descricao"] = "(".$produto_filial_alterado->produto->codigo_global.") ".$produto_filial_alterado->produto->nome;
                                
                                //echo "<pre>"; print_r($body["param"]["det"][0]["produto"]["codigo"]); echo "</pre>";
                                //echo "<pre>"; print_r($body["param"]["det"][0]["produto"]["codigo_produto"]); echo "</pre>";
                                //echo "<pre>"; print_r($body["param"]["det"][0]["produto"]["descricao"]); echo "</pre>";
                                
                                //$response_omie = $omie->cria_pedido("api/v1/produtos/pedido/?JSON=",$body);
                                //echo "<pre>"; print_r($response_omie); echo "</pre>";
                            }
                        }
                    }
                }
            }
        }*/

        // ALTERAÇÃO 02-11-2020
        return $this->redirect(['/pedidos-mercado-livre/index', 'id' => $model->id]);
    }



    public function actionMercadoLivreCancelado($id)
    {

        $model = PedidoMercadoLivre::findOne($id);

        $e_pedido_cancelado = $model->e_pedido_cancelado;

        $model->e_pedido_cancelado = true;
        $model->save();


        // ALTERAÇÃO 02-11-2020
        return $this->redirect(['/pedidos-mercado-livre/index', 'id' => $model->id]);
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


    public function actionObterNotaFiscal($id)

    {

        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $id])->one();
        if ($pedido_mercado_livre) {

            $omie = new Omie(1, 1);
            $APP_KEY_OMIE_SP                   = '468080198586';
            $APP_SECRET_OMIE_SP                = '7b3fb2b3bae35eca3b051b825b6d9f43';
            $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
            $APP_SECRET_OMIE_CONTA_DUPLICADA   = '78ba33370fac6178da52d42240591291';
	    $APP_KEY_OMIE_MG4                  = '1758907907757';
            $APP_SECRET_OMIE_MG4               = '0a69c9b49e5a188e5f43d5505f2752bc';

            $APP_KEY_OMIE       = $APP_KEY_OMIE_SP;
            $APP_SECRET_OMIE    = $APP_SECRET_OMIE_SP;
            if ($pedido_mercado_livre->user_id == '435343067') {
                $APP_KEY_OMIE       = $APP_KEY_OMIE_CONTA_DUPLICADA;
                $APP_SECRET_OMIE    = $APP_SECRET_OMIE_CONTA_DUPLICADA;
            }elseif($pedido_mercado_livre->user_id == '195972862'){
		$APP_KEY_OMIE       = $APP_KEY_OMIE_MG4;
                $APP_SECRET_OMIE    = $APP_SECRET_OMIE_MG4;
	    }

            $body = [
                "call" => "ConsultarPedido",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "codigo_pedido_integracao" => $pedido_mercado_livre->pedido_meli_id,
                ]
            ];
            $response_pedido = $omie->consulta_pedido("api/v1/geral/pedido/?JSON=", $body);
            //echo "<pre>"; print_r($response_pedido); echo "</pre>"; die;

            $body = [
                "call" => "ConsultarNF",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "nIdPedido" => ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto.cabecalho.codigo_pedido'),
                ]
            ];
            $response_nota_fiscal = $omie->consulta("api/v1/produtos/nfconsultar/?JSON=", $body);
            //echo "<pre>"; print_r($response_nota_fiscal); echo "</pre>";die;


            $body = [
                "call" => "GetUrlDanfe",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "nCodNF" => ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF'),
                ]
            ];
            $response_url_nota_fiscal = $omie->consulta("/api/v1/produtos/notafiscalutil/?JSON=", $body);
            //echo "<pre>"; print_r($response_url_nota_fiscal); echo "</pre>"; die;
            //echo "<pre>"; print_r($response_url_nota_fiscal["body"]); echo "</pre>";
            //echo "<pre>"; print_r($response_url_nota_fiscal["body"]["cUrlDanfe"]); echo "</pre>";die;

            if ($response_nota_fiscal["httpCode"] == 200) {
                $url = $response_url_nota_fiscal["body"]["cUrlDanfe"];

                //SALVAR NOTA FISCAL
                $file_name = "/var/www/etiquetas/nota_fiscal_" . $pedido_mercado_livre->shipping_id . ".pdf";

                if (file_put_contents($file_name, file_get_contents($url))) {
                    echo "File downloaded successfully";

                    //header('Content-disposition: Attachment; filename="'.$pedido_mercado_livre->pedido_meli_id.".pdf".'"');
                    //header('Content-type: "application/pdf"');
                    //readfile($file_name);
                } else {
                    echo "File downloading failed.";
                }
                //SALVAR NOTA FISCAL

                $pedido_mercado_livre->quantidade_impressoes_nota_fiscal += 1;
                $pedido_mercado_livre->save();
                //echo $url; die;
                return $this->redirect($url);
            }
        }
    }


    public function actionGerarXMLOmie($id)
    {

        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $id])->one();
        if ($pedido_mercado_livre) {

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
            $response_pedido = $meli->consulta_pedido("api/v1/geral/pedido/?JSON=", $body);
            //echo "<pre>"; print_r($response_pedido); echo "</pre>"; die;

            $body = [
                "call" => "ConsultarNF",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "nIdPedido" => ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto.cabecalho.codigo_pedido'),
                ]
            ];
            $response_nota_fiscal = $meli->consulta("/api/v1/produtos/nfconsultar/?JSON=", $body);
            //echo "<pre>"; print_r($response_nota_fiscal); echo "</pre>";die;

            $body = [
                "call" => "GetUrlNotaFiscal",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "nCodNF" => ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF'),
                ]
            ];
            $response_url_nota_fiscal = $meli->consulta("/api/v1/produtos/notafiscalutil/?JSON=", $body);
            //echo "<pre>"; print_r($response_url_nota_fiscal); echo "</pre>"; die;


            //return $this->redirect(ArrayHelper::getValue($response_url_nota_fiscal, 'body.cUrlNF'));

            $url_xml_nota_fiscal = ArrayHelper::getValue($response_url_nota_fiscal, 'body.cUrlNF');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url_xml_nota_fiscal);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $xml_nota_fiscal = curl_exec($ch);
            curl_close($ch);
            //print_r($xml_nota_fiscal);

            $arquivo_nome = "/var/tmp/" . ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF') . ".xml";
            $arquivo = fopen($arquivo_nome, "a");
            fwrite($arquivo, $xml_nota_fiscal);
            fclose($arquivo);

            header('Content-disposition: Attachment; filename="' . ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF') . '.xml"');
            header('Content-type: "text/xml"; charset="utf8"');
            readfile($arquivo_nome);
        }

        return $this->redirect(['index']);
    }

    public function actionGerarEtiqueta($id)
    {
        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $id])->one();
        if ($pedido_mercado_livre) {

            $file_name = "/var/www/etiquetas/" . $pedido_mercado_livre->shipping_id . ".pdf";
            if (file_exists($file_name)) {
                return $this->redirect("https://www.pecaagora.com/etiquetas/" . $pedido_mercado_livre->shipping_id . ".pdf");
            }

            $filial = ($pedido_mercado_livre->user_id == "193724256") ? Filial::find()->andWhere(['=', 'id', 72])->one() : (($pedido_mercado_livre->user_id == "435343067") ? Filial::find()->andWhere(['=', 'id', 98])->one() : Filial::find()->andWhere(['=', 'id', 94])->one());
            $meli = new Meli(static::APP_ID, static::SECRET_KEY);
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            $meliAccessToken = $response->access_token;
            //print_r($response); die;

            //$response_order = $meli->get("/orders/2592738061?access_token=" . $meliAccessToken);
            //print_r($response_order); die;

            //$response_etiqueta = $meli->get("/shipment_labels?shipment_ids=40023553497&savePdf=Y&access_token=" . $meliAccessToken);
            //print_r($response_etiqueta);

            $url = "https://api.mercadolibre.com/shipment_labels?shipment_ids=" . $pedido_mercado_livre->shipping_id . "&savePdf=Y&access_token=" . $meliAccessToken;

            //return $this->redirect($url);

            //$file_name = "/var/tmp/etiquetas/".$pedido_mercado_livre->shipping_id.".pdf";//basename($url);
            $file_name = "/var/www/etiquetas/" . $pedido_mercado_livre->shipping_id . ".pdf";

            if (file_put_contents($file_name, file_get_contents($url))) {
                echo "File downloaded successfully";

                $pedido_mercado_livre->e_etiqueta_impressa = true;
                $pedido_mercado_livre->quantidade_impressoes_etiqueta += 1;
                $pedido_mercado_livre->save();

                return $this->redirect("https://www.pecaagora.com/etiquetas/" . $pedido_mercado_livre->shipping_id . ".pdf");

                header('Content-disposition: Attachment; filename="' . $pedido_mercado_livre->pedido_meli_id . ".pdf" . '"');
                header('Content-type: "application/pdf"');
                readfile($file_name);
            } else {
                echo "File downloading failed.";
            }
        }

        return $this->redirect($url);
        //return $this->redirect(['index']);

    }

    public function actionFaturamento($filtro = null, $e_mercado_livre_principal = null, $e_mercado_livre_filial = null, $e_mercado_livre_mg4 = null)
    {
        $this->layout = "main";

        $e_mercado_livre_principal  = ($e_mercado_livre_principal == null) ? false : true;
        $e_mercado_livre_filial     = ($e_mercado_livre_filial == null) ? false : true;
	$e_mercado_livre_mg4	    = ($e_mercado_livre_mg4 == null) ? false : true;
        if (!$e_mercado_livre_principal && !$e_mercado_livre_filial && !$e_mercado_livre_mg4) {
            $e_mercado_livre_principal  = true;
            $e_mercado_livre_filial     = true;
	    $e_mercado_livre_mg4     	= true;
        }

        return $this->render('index-faturamento', ["filtro" => $filtro, "e_mercado_livre_principal" => $e_mercado_livre_principal, "e_mercado_livre_filial" => $e_mercado_livre_filial, "e_mercado_livre_mg4" => $e_mercado_livre_mg4]);
    }

    public function actionSubirXML($id)
    {

        $this->layout = "layout_limpo";

        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $id])->one();
        if ($pedido_mercado_livre) {

	    echo "Pedido enontrado";
            
            $APP_KEY_OMIE_SP                   = '468080198586';
            $APP_SECRET_OMIE_SP                = '7b3fb2b3bae35eca3b051b825b6d9f43';
            $APP_KEY_OMIE_MG                   = '469728530271';
            $APP_SECRET_OMIE_MG                = '6b63421c9bb3a124e012a6bb75ef4ace';
            $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
            $APP_SECRET_OMIE_CONTA_DUPLICADA   = '78ba33370fac6178da52d42240591291';
            $APP_KEY_OMIE_MG4                  = '1758907907757';
            $APP_SECRET_OMIE_MG4               = '0a69c9b49e5a188e5f43d5505f2752bc';
            
            //Obter access_toke da conta principal do ML
            $meli               = new Meli(static::APP_ID, static::SECRET_KEY);
            $filial             = Filial::find()->andwhere(['=', 'id', 72])->one();
            $user               = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response           = ArrayHelper::getValue($user, 'body');
            $meliAccessToken    = $response->access_token;
            
            //Obter access_toke da conta duplicada do ML
            $filial_conta_duplicada             = Filial::find()->andwhere(['=', 'id', 98])->one();
            $user_conta_duplicada               = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
            $response_conta_duplicada           = ArrayHelper::getValue($user_conta_duplicada, 'body');
            $meliAccessToken_conta_duplicada    = $response_conta_duplicada->access_token;
            
            //Obter access_toke da conta duplicada do ML
            $filial_MG4             = Filial::find()->andwhere(['=', 'id', 94])->one();
            $user_MG4               = $meli->refreshAccessToken($filial_MG4->refresh_token_meli);
            $response_MG4           = ArrayHelper::getValue($user_MG4, 'body');
            $meliAccessToken_MG4    = $response_MG4->access_token;
            
            $pedidos_mercado_livre = PedidoMercadoLivre::find() ->andWhere(['=','e_xml_subido',false])
            ->andWhere(['=','e_pedido_autorizado',true])
            ->andWhere(['=','e_pedido_enviado',false])
            //->andWhere(['id' => [ 16927 ]])
            ->andWhere(['=', 'status', 'paid'])
            ->orderBy(['id'=>SORT_DESC])
            ->all();
            
            //Analisa qual conta do ML o pedido pertence e obtem o access_token respectivo
            $token              = $meliAccessToken;
            $APP_KEY_OMIE       = $APP_KEY_OMIE_SP;

            switch ($pedido_mercado_livre->user_id){
                case "435343067":
                    echo " - Conta Duplicada";
                    $token              = $meliAccessToken_conta_duplicada;
                    $APP_KEY_OMIE       = $APP_KEY_OMIE_CONTA_DUPLICADA;
                    $APP_SECRET_OMIE    = $APP_SECRET_OMIE_CONTA_DUPLICADA;
                    break;
                case "195972862":
                    echo " - Conta MG";
                    $token              = $meliAccessToken_MG4;
                    $APP_KEY_OMIE       = $APP_KEY_OMIE_MG4;
                    $APP_SECRET_OMIE    = $APP_SECRET_OMIE_MG4;
                    break;
                default:
                    echo " - Conta Principal";
                    $token              = $meliAccessToken;
                    $APP_KEY_OMIE       = $APP_KEY_OMIE_SP;
                    $APP_SECRET_OMIE    = $APP_SECRET_OMIE_SP;
            }
            
            $response_invoice = $meli->get("/users/".$pedido_mercado_livre->user_id."/invoices/shipments/".$pedido_mercado_livre->shipping_id."?access_token=".$token);
            
            //$response_invoice = $meli->get("/users/".$pedido_mercado_livre->user_id."/invoices/shipments/40704136889?access_token=".$token);
            //print_r($response_invoice);
            //die;
            
            if($response_invoice['httpCode'] < 300){
                
                echo " - ";print_r($response_invoice["body"]->attributes->invoice_key);
                $nota_fiscal = NotaFiscal::find()->andWhere(["=", "chave_nf", $response_invoice["body"]->attributes->invoice_key])->one();
                if($nota_fiscal){
                    $pedido_mercado_livre->nota_fiscal_id = $nota_fiscal->id;
                }
                
                echo " - XML já subido anteriomente";
                
                $pedido_mercado_livre->e_xml_subido = true;
                if($pedido_mercado_livre->save()){
                    echo " - Status alterado";
                }
                else{
                    echo " - Status não alterado";
                }
            }
            else{
                echo " - XML ainda não subido";
            }
            
            //continue;
            
            $omie = new Omie(1, 1);
            
            //Consulta pedido no Omie, verificando se existe pedido criado com o código integração com o numero do pedido do ML
            $body = [
                "call" => "ConsultarPedido",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "codigo_pedido_integracao" => $pedido_mercado_livre->pedido_meli_id,
                ]
            ];
            $response_pedido = $omie->consulta_pedido("api/v1/geral/pedido/?JSON=",$body);
            //echo "==>\n\n"; print_r($response_pedido); echo "\n\n<=="; die;
            if($response_pedido["httpCode"] < 300){
                
                echo " - Pedido encontrado no Omie";
                
                //Consulta Nota fiscal do pedido, se encontrar, baixa a XML da nota
                $body = [
                    "call" => "ConsultarNF",
                    "app_key" => $APP_KEY_OMIE,
                    "app_secret" => $APP_SECRET_OMIE,
                    "param" => [
                        "nIdPedido" => ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto.cabecalho.codigo_pedido'),
                    ]
                ];
                $response_nota_fiscal = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
                //echo "==>\n\n"; print_r($response_nota_fiscal); echo "\n\n<==";//die;
                if($response_nota_fiscal["httpCode"] < 300){
                    
                    echo " - Nota encontrada";
                    
                    //Obtem a XML da nota para ser enviada para o ML
                    $body = [
                        "call" => "GetUrlNotaFiscal",
                        "app_key" => $APP_KEY_OMIE,
                        "app_secret" => $APP_SECRET_OMIE,
                        "param" => [
                            "nCodNF" => ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF'),
                        ]
                    ];
                    $response_url_nota_fiscal = $omie->consulta("/api/v1/produtos/notafiscalutil/?JSON=",$body);
                    //echo "==>\n\n"; print_r($response_url_nota_fiscal); echo "\n\n<=="; die;
                    if($response_nota_fiscal["httpCode"] < 300){
                        
                        echo " - XML gerada";
                        
                        //Trecho que obtem a XML da nota para subir no ML
                        $url_xml_nota_fiscal = ArrayHelper::getValue($response_url_nota_fiscal, 'body.cUrlNF');
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url_xml_nota_fiscal);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $xml_nota_fiscal = curl_exec($ch);
                        curl_close($ch);
                        //echo "==>\n\n"; print_r($xml_nota_fiscal); echo "\n\n<==";
                        //die;
                        
                        //Grava o xml da nota em um arquivo no servidor
                        $arquivo_nome = "/var/tmp/".ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF').".xml";
                        $arquivo = fopen($arquivo_nome, "a");
                        fwrite($arquivo, $xml_nota_fiscal);
                        fclose($arquivo);
                        
                        //Faz o envio do xml da nota para o ML
                        $url = "https://api.mercadolibre.com/shipments/".$pedido_mercado_livre->shipping_id."/invoice_data/?access_token=".$token."&siteId=MLB";
                        $ch = curl_init( $url );
                        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml_nota_fiscal );
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                        $result = curl_exec($ch);
                        curl_close($ch);
                        //echo "==>\n\n"; print_r($result); echo "\n\n<==";
                        
                        $resultado = json_decode($result);
                        //echo "==>\n\n"; print_r($resultado); echo "\n\n<==";
                        
                        if($resultado->status < 400){
                            echo " - XML subida";
                            
                            echo " - ";print_r($response_nota_fiscal["body"]["compl"]["cChaveNFe"]);
                            $nota_fiscal = NotaFiscal::find()->andWhere(["=", "chave_nf", $response_nota_fiscal["body"]["compl"]["cChaveNFe"]])->one();
                            if($nota_fiscal){
                                $pedido_mercado_livre->nota_fiscal_id = $nota_fiscal->id;
                            }
                            
                            $pedido_mercado_livre->e_xml_subido = true;
                            if($pedido_mercado_livre->save()){
                                echo " - Status alterado";
                            }
                            else{
                                echo " - Status não alterado";
                            }
                        }
                        else{
                            echo " - XML não subida";
                            echo "==>\n\n"; print_r($resultado); echo "\n\n<==";
                        }
                    }
                    else{
                        echo " - XML não gerada";
                    }
                }
                else{
                    echo " - Nota não encontrada";
                }
            }
            else{
                echo " - Pedido não encotrado no Omie";
            }

        }

	return $this->render('index-faturamento', ["filtro" => null, "e_mercado_livre_principal" => true, "e_mercado_livre_filial" => true, "e_mercado_livre_mg4" => true]);
        //return $this->redirect(['index']);

    }

    public function actionSeparacao($filtro = null, $e_apenas_nao_impressos = false)
    {
        $this->layout = "layout_limpo";

        return $this->render('index-separacao', ["filtro" => $filtro, "e_mail" => "estoquesp.pecaagora@gmail.com", "e_apenas_nao_impressos" => $e_apenas_nao_impressos]);
    }

    public function actionSeparacaoPreNota($id)
    {

        $this->layout = "main_sem_menu";

        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $id])->one();
        if ($pedido_mercado_livre) {
            $pedido_mercado_livre->e_pre_nota_impressa = true;
            $pedido_mercado_livre->quantidade_impressoes_pre_nota++;
            $pedido_mercado_livre->save();

            $relatorio = new PreNota();
            $relatorio->init($pedido_mercado_livre);
        }

        return $this->redirect($url);
        //return $this->redirect(['index']);

    }

    public function actionExpedicao($chave = null)
    {

        $this->layout = "layout_limpo";

        $status = "";

        $contador = 0;
        $arquivo_nome = '/var/tmp/contador_pedidos_enviados.txt';

        if ($chave != null) {

            $log_expedicao              = new LogExpedicao;
            $log_expedicao->descricao   = $chave;
            $log_expedicao->salvo_em    = date("Y-m-d H:i:s");
            $log_expedicao->salvo_por   = Yii::$app->user->identity->id;
            $log_expedicao->save();

            $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(["=", "shipping_id", $chave])->one();
            if ($pedido_mercado_livre) {
                if ($pedido_mercado_livre->e_pedido_enviado) {
                    $administrador = Administrador::find()->andWhere(["=", "id", $pedido_mercado_livre->enviado_por])->one();
//var_dump($administrador); die;
                    $status = " <table>
                                    <tr>
                                        <td colspan='2'>
                                            <h2>
                                                <b>
                                                    <div style='color: red;'>
                                                        Pedido já enviado anteriormente: " . $chave . "
                                                    </div>
                                                </b>
                                            </h2>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span  style='font-size: 20px;'>
                                                <b>
                                                    Pedido enviado por:
                                                </b>
                                            </span>
                                        </td>
                                        <td>
                                            <span  style='font-size: 20px;'>
                                                <b>" .
                        $administrador->nome . "
                                                </b>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span  style='font-size: 20px;'>
                                                <b>
                                                    Enviado em:
                                                </b>
                                            </span>
                                        </td>
                                        <td>
                                            <span  style='font-size: 20px;'>
                                                <b>" .
                        $pedido_mercado_livre->data_hora_envio . "
                                                </b>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                                <br>";
                } else {
                    $pedido_mercado_livre->e_pedido_enviado = true;
                    $pedido_mercado_livre->data_hora_envio  = date("Y-m-d H:i:s");
                    $pedido_mercado_livre->enviado_por      = Yii::$app->user->id;
                    if ($pedido_mercado_livre->save()) {
                        $status = "<h1><b><span style='color: green;'>Pedido enviado: " . $chave . "</span></b></h1>";

                        $arquivo = fopen($arquivo_nome, "r");
                        $acrescimo = fgets($arquivo, 4096) + 1;
                        fclose($arquivo);
                        $arquivo = fopen($arquivo_nome, "w");
                        fwrite($arquivo, $acrescimo);
                        fclose($arquivo);
                    } else {
                        $status = "<h1><b><span style='color: red;'>Pedido não enviado: " . $chave . "</span></b></h1>";
                    }
                }
            } else {
                $nota_fiscal    = NotaFiscal::find()->andWhere(["=", "nota_fiscal.chave_nf", $chave])->one();
                if ($nota_fiscal) {
                    $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(["=", "nota_fiscal_id", $nota_fiscal->id])->one();
                    if ($pedido_mercado_livre) {
                        if ($pedido_mercado_livre->e_pedido_enviado) {
                            $administrador = Administrador::find()->andWhere(["=", "id", $pedido_mercado_livre->enviado_por])->one();
                            $status = " <table>
                                            <tr>
                                                <td colspan='2'>
                                                    <h2>
                                                        <b>
                                                            <div style='color: red;'>
                                                                Pedido já enviado anteriormente: " . $chave . "
                                                            </div>
                                                        </b>
                                                    </h2>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span  style='font-size: 20px;'>
                                                        <b>
                                                            Pedido enviado por:
                                                        </b>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span  style='font-size: 20px;'>
                                                        <b>" .
                                $administrador->nome . "
                                                        </b>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span  style='font-size: 20px;'>
                                                        <b>
                                                            Enviado em:
                                                        </b>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span  style='font-size: 20px;'>
                                                        <b>" .
                                $pedido_mercado_livre->data_hora_envio . "
                                                        </b>
                                                    </span>
                                                </td>
                                            </tr>
                                        </table><br>";
                        } else {
                            $pedido_mercado_livre->e_pedido_enviado = true;
                            $pedido_mercado_livre->data_hora_envio  = date("Y-m-d H:i:s");
                            $pedido_mercado_livre->enviado_por      = Yii::$app->user->id;
                            if ($pedido_mercado_livre->save()) {
                                $status = "<h1><b><span style='color: green;'>Pedido enviado: " . $chave . "</span></b></h1>";

                                $arquivo = fopen($arquivo_nome, "r");
                                $acrescimo = fgets($arquivo, 4096) + 1;
                                fclose($arquivo);
                                $arquivo = fopen($arquivo_nome, "w");
                                fwrite($arquivo, $acrescimo);
                                fclose($arquivo);
                            } else {
                                $status = "<h1><b><span style='color: red;'>Pedido não enviado: " . $chave . "</span></b></h1>";
                            }
                        }
                    } else {
                        $status = "<h1><b><span style='color: red;'>Pedido não encontrado: " . $chave . "</span></b></h1>";
                    }
                } else {
                    $status = "<h1><b><span style='color: red;'>Nota fiscal não encontrada: " . $chave . "</span></b></h1>";
                }
            }

            $logs_expedicao              = LogExpedicao::find()->andwhere(["=", "descricao", $chave])->orderBy(["salvo_em" => SORT_DESC])->all();
            $status .= "<table border  width=500>
                            <tr>
                                <td colspan='2' align=center>
                                    <span  style='font-size: 16px;'>
                                        <b>
                                            LOG
                                        </b>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td align=center>
                                    <span  style='font-size: 14px;'>
                                        <b>
                                            Data/Hora:
                                        </b>
                                    </span>
                                </td>
                                <td align=center>
                                    <span  style='font-size: 14px;'>
                                        <b>
                                            Colaborador
                                        </b>
                                    </span>
                                </td>
                            </tr>";

            foreach ($logs_expedicao as $k => $log_expedicao) {
                $administrador = Administrador::find()->andWhere(["=", "id", $log_expedicao->salvo_por])->one();
                $administrador_nome = "Por Status";
                if ($administrador) {
                    $administrador_nome = $administrador->nome;
                }
                $status .= "<tr>
                                <td align=center>
                                    <span  style='font-size: 14px;'>
                                             " . $log_expedicao->salvo_em . "
                                    </span>
                                </td>
                                <td>
                                    <span  style='font-size: 14px; padding: 10px'>
                                         " . $administrador_nome . "
                                    </span>
                                </td>
                            </tr>";
            }

            $status .= "</table><br>";
        }

        $searchModel = new PedidoMercadoLivreSearch();
        //$dataProvider = $searchModel->search(['PedidoMercadoLivreSearch'=> ["pedido_meli_id"=>null, 'chave' => $chave, "e_pedido_enviado" => false, "e_pedido_cancelado" => false, "e_pedido_mercado_envios" => true]]);
        $dataProvider = $searchModel->search(['PedidoMercadoLivreSearch' => ["pedido_meli_id" => null, 'chave' => $chave, "e_pedido_enviado" => false, "e_pedido_cancelado" => false, "e_pedido_autorizado" => true, "e_pedido_mercado_envios" => true]]);

        $arquivo = fopen("/var/tmp/contador_pedidos_enviados.txt", "r");
        $contador = fgets($arquivo, 4096);
        fclose($arquivo);

        return $this->render('index-expedicao', ["searchModel" => $searchModel, "dataProvider" => $dataProvider, "status" => $status, "contador" => $contador]);
    }

    public function actionZerarContador($chave = null)
    {
        $arquivo_nome = '/var/tmp/contador_pedidos_enviados.txt';
        $arquivo = fopen($arquivo_nome, "w");
        fwrite($arquivo, 0);
        fclose($arquivo);

        return $this->redirect(['pedidos-mercado-livre-expedicao/expedicao', 'filtro' => null]);
    }
}
