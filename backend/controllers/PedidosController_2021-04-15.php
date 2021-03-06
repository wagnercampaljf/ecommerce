<?php

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
use common\models\Comprador;
use common\models\Empresa;
use common\models\EnderecoEmpresa;
use yii\db\Query;
use common\models\StatusPedido;

/**
 * PedidosController implements the CRUD actions for Pedido model.
 */
class PedidosController extends Controller
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


    public function actionIndex($filtro = null)
    {
        if ($_SESSION['__id'] == 56) {
            Yii::$app->user->logout();
        }
        //return $this->render('index', ["filtro" => $filtro]);

        $searchModel = new PedidoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        //$dataProvider->pagination = ['pageSize' => 6,];

        //$skyhubSearchModel = new PedidoSkyhubSearch();
        //$skyhubDataProvider = $skyhubSearchModel->cloudSearch(Yii::$app->request->get());


        //echo "<pre>"; print_r($skyhubDataProvider); echo "</pre>";

        return $this->render('index', [
            'dataProvider'              => $dataProvider,
            'filterModel'               => $searchModel,
            //'skyhubDataProvider'        => $skyhubDataProvider,
            'filtro'                    => $filtro
            //'dataProviderMercadoLivre'  => $searchModelPedidoMercadoLivre,
            //'filterModelMercadoLivre'   => $dataProviderPedidoMercadoLivre,
        ]);
    }

    public function actionCreate($id = null)
    {
        $modelPedido = new Pedido();
        $modelComprador = new Comprador();
        $modelEmpresa = new Empresa();
        $modelEndEmpresa = new EnderecoEmpresa();
        $modelPedProdFilial = new PedidoProdutoFilial();
        $modelProdFilial = new ProdutoFilial();
        $dataProvider = null;

        if ($id) {
            $post = Yii::$app->request->post();

            $modelPedido = Pedido::findOne($id);
            $modelPedido->load(Yii::$app->request->post());
            $modelPedido->valor_total = floatval($modelPedido->valor_total) + ($modelPedido->valor_total = $post['PedidoProdutoFilial']['valor'] * $post['PedidoProdutoFilial']['quantidade']);
            $modelPedido->save(false);

            $modelComprador = Comprador::findOne($modelPedido->comprador_id);
            $modelComprador->load(Yii::$app->request->post());
            $modelComprador->save(false);

            $modelEmpresa = Empresa::findOne($modelComprador->empresa_id);
            $modelEmpresa->load(Yii::$app->request->post());
            $modelEmpresa->save(false);

            $modelEndEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $modelEmpresa->id]);
            $modelEndEmpresa->load(Yii::$app->request->post());
            $modelEndEmpresa->save(false);

            $modelPedProdFilial->load(Yii::$app->request->post());
            $modelPedProdFilial->pedido_id = $modelPedido->id;
            $modelPedProdFilial->save(false);
            $modelPedProdFilial = new PedidoProdutoFilial();

            $sql = new Query;
            $sql->Select([
                'produto.id',
                'produto.nome',
                'pedido_produto_filial.valor',
                'pedido_produto_filial.quantidade'
            ])
                ->from("produto")
                ->innerJoin("produto_filial", "produto_filial.produto_id = produto.id")
                ->innerJoin("pedido_produto_filial", "pedido_produto_filial.produto_filial_id = produto_filial.id")
                ->innerJoin("pedido", "pedido_produto_filial.pedido_id = pedido.id")
                ->where("pedido.id = $id")
                ->orderBy("produto.id ASC")
                ->all();

            $dataProvider = new ActiveDataProvider([
                'query' => $sql
            ]);
        } else {
            if (Yii::$app->request->post()) {
                $post = Yii::$app->request->post();
                $modelComprador = Comprador::findOne(['cpf' => $post['Comprador']['cpf']]);
                if ($modelComprador) {
                    $modelEmpresa = Empresa::findOne($modelComprador->empresa_id);
                    $modelEmpresa->load(Yii::$app->request->post());
                    $modelEmpresa->save(false);

                    $modelEndEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $modelEmpresa->id]);
                    if ($modelEndEmpresa) {
                        $modelEndEmpresa->load(Yii::$app->request->post());
                        $modelEndEmpresa->save(false);
                    } else {
                        $modelEndEmpresa = new EnderecoEmpresa();
                        $modelEndEmpresa->load(Yii::$app->request->post());
                        $modelEndEmpresa->empresa_id = $modelEmpresa->id;
                        $modelEndEmpresa->save(false);
                    }
                    $modelEndEmpresa->load(Yii::$app->request->post());
                    $modelEndEmpresa->save(false);

                    $modelPedido->load(Yii::$app->request->post());
                    $modelPedido->valor_total = $post['PedidoProdutoFilial']['valor'] * $post['PedidoProdutoFilial']['quantidade'];
                    $modelPedido->comprador_id = $modelComprador->id;
                    $modelPedido->forma_pagamento_id = 2;
                    $modelPedido->administrador_id = Yii::$app->user->id;
                    $modelPedido->save(false);

                    $modelStatus = new StatusPedido();
                    $modelStatus->data_referencia = date('Y-m-d');
                    $modelStatus->pedido_id = $modelPedido->id;
                    $modelStatus->tipo_status_id = 1;
                    $modelStatus->save(false);

                    $modelPedProdFilial->load(Yii::$app->request->post());
                    $modelPedProdFilial->pedido_id = $modelPedido->id;
                    $modelPedProdFilial->save(false);
                    $modelPedProdFilial = new PedidoProdutoFilial();
                    $modelProdFilial = new ProdutoFilial();
                } else {
                    $modelEmpresa->load(Yii::$app->request->post());
                    $modelEmpresa->nome = $post['Comprador']['nome'];
                    $modelEmpresa->razao = $post['Comprador']['nome'];
                    $modelEmpresa->documento = $post['Comprador']['cpf'];
                    $modelEmpresa->grupo_id = 81;
                    $modelEmpresa->save(false);

                    $modelEndEmpresa->load(Yii::$app->request->post());
                    $modelEndEmpresa->empresa_id = $modelEmpresa->id;
                    $modelEndEmpresa->save(false);

                    $modelComprador = new Comprador();
                    $modelComprador->load(Yii::$app->request->post());
                    $modelComprador->empresa_id = $modelEmpresa->id;
                    $modelComprador->dt_criacao = date('Y-m-d');
                    $modelComprador->ativo = true;
                    $modelComprador->email = $post['Empresa']['email'];
                    $modelComprador->nivel_acesso_id = 1;
                    $modelComprador->save(false);

                    $modelPedido->load(Yii::$app->request->post());
                    $modelPedido->valor_total = $post['PedidoProdutoFilial']['valor'] * $post['PedidoProdutoFilial']['quantidade'];
                    $modelPedido->comprador_id = $modelComprador->id;
                    $modelPedido->forma_pagamento_id = 2;
                    $modelPedido->administrador_id = Yii::$app->user->id;
                    $modelPedido->save(false);

                    $modelStatus = new StatusPedido();
                    $modelStatus->data_referencia = date('Y-m-d');
                    $modelStatus->pedido_id = $modelPedido->id;
                    $modelStatus->tipo_status_id = 1;
                    $modelStatus->save(false);

                    $modelPedProdFilial->load(Yii::$app->request->post());
                    $modelPedProdFilial->pedido_id = $modelPedido->id;
                    $modelPedProdFilial->save(false);
                    $modelPedProdFilial = new PedidoProdutoFilial();
                    $modelProdFilial = new ProdutoFilial();
                }
                $sql = new Query;
                $sql->Select([
                    'produto.id',
                    'produto.nome',
                    'pedido_produto_filial.valor',
                    'pedido_produto_filial.quantidade'
                ])
                    ->from("produto")
                    ->innerJoin("produto_filial", "produto_filial.produto_id = produto.id")
                    ->innerJoin("pedido_produto_filial", "pedido_produto_filial.produto_filial_id = produto_filial.id")
                    ->innerJoin("pedido", "pedido_produto_filial.pedido_id = pedido.id")
                    ->where("pedido.id = $modelPedido->id")
                    ->orderBy("produto.id ASC")
                    ->all();

                $dataProvider = new ActiveDataProvider([
                    'query' => $sql
                ]);
            }
        }

        return $this->render('pedidos-internos/create', [
            'modelPedido' => $modelPedido,
            'modelComprador' => $modelComprador,
            'modelEmpresa' => $modelEmpresa,
            'modelEndEmpresa' => $modelEndEmpresa,
            'modelPedProdFilial' => $modelPedProdFilial,
            'modelProdFilial' => $modelProdFilial,
            'dataProvider' => $dataProvider,
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


    public function actionMercadoLivreProdutoProdutoFilialCreate($pedido_mercado_livre_produto_id)
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

        echo "<pre>";
        print_r($model->email_enderecos);
        echo "</pre>";
        die;

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
            throw new \yii\web\ForbiddenHttpException('Sem permiss??o');
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

            $filial = ($pedido_mercado_livre->user_id == "193724256") ? Filial::find()->andWhere(['=', 'id', 72])->one() : Filial::find()->andWhere(['=', 'id', 98])->one();
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

            $file_name = "/var/tmp/etiquetas/" . $pedido_mercado_livre->shipping_id . ".pdf"; //basename($url);

            if (file_put_contents($file_name, file_get_contents($url))) {
                echo "File downloaded successfully";

                header('Content-disposition: Attachment; filename="' . $pedido_mercado_livre->pedido_meli_id . ".pdf" . '"');
                header('Content-type: "application/pdf"');
                readfile($file_name);
            } else {
                echo "File downloading failed.";
            }
        }
    }
}
