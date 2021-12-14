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

/**
 * PedidosController implements the CRUD actions for Pedido model.
 */
class PedidosMercadoLivreController extends Controller
{
    const APP_KEY_OMIE_SP                   = '468080198586';
    const APP_SECRET_OMIE_SP                = '7b3fb2b3bae35eca3b051b825b6d9f43';
    const APP_KEY_OMIE_MG                   = '469728530271';
    const APP_SECRET_OMIE_MG                = '6b63421c9bb3a124e012a6bb75ef4ace';
    
    const APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
    const APP_SECRET_OMIE_CONTA_DUPLICADA   = '78ba33370fac6178da52d42240591291';
    
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
    public function actionIndex($filtro = null)
    {

        return $this->render('index', ["filtro" => $filtro]);
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


        $searchModelProdutoFilial = new PedidoMercadoLivreProdutoProdutoFilialSearch();
        $dataProviderProdutoFilial = $searchModelProdutoFilial->search(['PedidoMercadoLivreProdutoProdutoFilialSearch'=> ['pedido_mercado_livre_produto_id' => $id]]);
        
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



            $pedido_mercado_livre_produto = PedidoMercadoLivreProduto::find()->andWhere(['=','id',$pedido_mercado_livre_produto_id ])->one();


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
        
        $e_pedido_autorizado = $model->e_pedido_autorizado;


        if ( $model->e_pedido_autorizado = true){

            $model->e_pedido_autorizado = false;

        }

        
        $model->e_pedido_autorizado = true;
        $model->save();
        
        //echo "<pre>"; print_r($model->email_enderecos); echo "</pre>";die;
        
        //return $this->render('view/_mercado-livre-view', ['model' => $model, //'dataProvider' => $dataProvider, ]);
        
        
        //Cria um array com os dados dos produtos que vão ser enviados para o cliente.
        $produtos_email = array();
        $pedido_mercado_livre_produtos = PedidoMercadoLivreProduto::find()->andWhere(['=', 'pedido_mercado_livre_id', $model->id])->all();
        foreach($pedido_mercado_livre_produtos as $k => $pedido_mercado_livre_produto){
            $pedido_mercado_livre_produto_produto_filials = PedidoMercadoLivreProdutoProdutoFilial::find()->andWhere(['=','pedido_mercado_livre_produto_id', $pedido_mercado_livre_produto->id])->all();
            
            foreach($pedido_mercado_livre_produto_produto_filials as $i => $pedido_mercado_livre_produto_produto_filial){
                
                $produto_filial = ProdutoFilial::find()->andWhere(['=','id',$pedido_mercado_livre_produto_produto_filial->produto_filial_id])->one();

		//echo "<pre>"; print_r($produto_filial); echo "</pre>";

                $email_texto = $model->email_texto;
                
                $emails = $model->email_enderecos.(($pedido_mercado_livre_produto_produto_filial->email != null && $pedido_mercado_livre_produto_produto_filial->email != "") ? ",".$pedido_mercado_livre_produto_produto_filial->email : "");
                $emails = str_replace(";",",",str_replace(" ","",$emails));
                $emails_destinatarios = explode(",",$emails);
                
                if($pedido_mercado_livre_produto_produto_filial->quantidade > 0){

                    $texto_unidades = (($pedido_mercado_livre_produto_produto_filial->quantidade > 1) ? " Unidades" : " Unidade");
                    
                    $codigo_fabricante = $produto_filial->produto->codigo_fabricante;
                    switch ($produto_filial->filial_id){
                        case 43:
                            $codigo_fabricante = str_replace('.M', '', $codigo_fabricante);
                            break;
                        case 60:
                            $codigo_fabricante = str_replace('L', '', $codigo_fabricante);
                            $codigo_fabricante = substr($codigo_fabricante, 0, 2)."-".substr($codigo_fabricante, 2);
                            break;
                        case 72:
                            $codigo_fabricante = str_replace('.B', '', $codigo_fabricante);
                            break;
                        case 97:
                            $codigo_fabricante = str_replace('D', '', $codigo_fabricante);
                            break;
                    }
                    
                    $email_texto = str_replace("{codigo}",$codigo_fabricante, $email_texto);
                    $email_texto = str_replace("{descricao}",$produto_filial->produto->nome." (".$produto_filial->produto->codigo_global.")", $email_texto);
                    $email_texto = str_replace("{quantidade}"," * ". $pedido_mercado_livre_produto_produto_filial->quantidade." ".$texto_unidades, $email_texto);
                    $email_texto = str_replace("{valor}",$pedido_mercado_livre_produto_produto_filial->valor." * ".$pedido_mercado_livre_produto_produto_filial->quantidade." ".$texto_unidades, $email_texto);
                    
                    if($pedido_mercado_livre_produto_produto_filial->observacao != null && $pedido_mercado_livre_produto_produto_filial->observacao != ""){
                        $email_texto = str_replace("{observacao}",$pedido_mercado_livre_produto_produto_filial->observacao, $email_texto);
                    }
                    else{
                        $email_texto = str_replace("\nObservação: {observacao}","", $email_texto);
                    }
                    
                    $assunto = $model->email_assunto;//"Pedido ".$codigo_fabricante." * ". $pedido_mercado_livre_produto_produto_filial->quantidade." ".$texto_unidades;
                    $assunto = str_replace("{quantidade}", $pedido_mercado_livre_produto_produto_filial->quantidade." ".$texto_unidades, $assunto);
                    $assunto = str_replace("{codigo_fabricante}", $codigo_fabricante, $assunto);
                    
                    /*if($model->receiver_name != "" && $model->receiver_name != null){
                        $assunto .= " - ".$model->receiver_name;
                    }
                    else{
                        $assunto .= " - ".$model->buyer_first_name." ".$model->buyer_last_name;
                    }*/
                    if($model->buyer_first_name != "" && $model->buyer_first_name != null){
                        //;
                        $assunto = str_replace("{nome}", $model->buyer_first_name." ".$model->buyer_last_name, $assunto);
                    }
                    else{
                        //$assunto .= " - ".$model->receiver_name;
                        $assunto = str_replace("{nome}", $model->receiver_name, $assunto);
                    }
                    
                    echo "<pre>"; print_r($assunto); echo "</pre>";
                    echo "<pre>"; print_r($emails_destinatarios); echo "</pre>";
                    echo "<pre>"; print_r($email_texto); echo "</pre>";
                    
                    //echo "<pre>"; print_r($emails_destinatarios); echo "</pre>"; die;
                    //echo "<pre>"; print_r($email_texto); echo "</pre>";
                    
                    //echo "<pre>"; print_r(\Yii::$app->params['supportEmail']); echo "</pre>";
                    
                    /*if(\Yii::$app->mailer   ->compose()
                        ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                        //->setTo(["wagnercampaljf@yahoo.com.br","dev.pecaagora@gmail.com","compras.pecaagora@gmail.com","dev2.pecaagora@gmail.com"])
                        ->setTo($emails_destinatarios)
                        //->setSubject(\Yii::$app->name . ' - Garantia '.$model->nome)
                        ->setSubject($assunto)
                        ->setTextBody($email_texto)
                        //->setHtmlBody($email_texto)
                        ->send())
                    {
                        $model->e_pedido_autorizado = true;
                        $model->save();
                    }*/

//echo 11111;
                    if(!$e_pedido_autorizado){
                        var_dump(\Yii::$app->mailer   ->compose()
                        ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                        //->setTo(["wagnercampaljf@yahoo.com.br","dev.pecaagora@gmail.com","compras.pecaagora@gmail.com","dev2.pecaagora@gmail.com"])
                        ->setTo($emails_destinatarios)
                        //->setSubject(\Yii::$app->name . ' - Garantia '.$model->nome)
                        ->setSubject($assunto)
                        ->setTextBody($email_texto)
                        //->setHtmlBody($email_texto)
                        ->send());
//echo 22222;
                    }
                }
            }
        }
      
        
        if(isset($model->user_id)){
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
        }

        // ALTERAÇÃO 02-11-2020
        return $this->redirect(['/pedidos-mercado-livre/index', 'id' => $model->id]);
        
    }


    public function actionMercadoLivreDesautorizar($id)
    {
        
        $model = PedidoMercadoLivre::findOne($id);
        
        $model->e_pedido_autorizado = false;
        $model->save();
        
        return $this->redirect(['mercado-livre-view', 'id' => $model->id]);
        
    }
    

    public function actionMercadoLivreCancelado($id)
    {
        $model = PedidoMercadoLivre::findOne($id);

        $e_pedido_cancelado = $model->e_pedido_cancelado;

        if ( $model->e_pedido_cancelado = true){

            $model->e_pedido_autorizado = false;

        }

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
            //echo "<pre>"; print_r($response_pedido); echo "</pre>"; die;
            
            $body = [
                "call" => "ConsultarNF",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "nIdPedido" => ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto.cabecalho.codigo_pedido'),
                ]
            ];
            $response_nota_fiscal = $meli->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
            //echo "<pre>"; print_r($response_nota_fiscal); echo "</pre>";die;
            
            $body = [
                "call" => "GetUrlNotaFiscal",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "nCodNF" => ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF'),
                ]
            ];
            $response_url_nota_fiscal = $meli->consulta("/api/v1/produtos/notafiscalutil/?JSON=",$body);
            //echo "<pre>"; print_r($response_url_nota_fiscal); echo "</pre>"; die;
            
            //return $this->redirect(ArrayHelper::getValue($response_url_nota_fiscal, 'body.cUrlNF'));
            
            $url_xml_nota_fiscal = ArrayHelper::getValue($response_url_nota_fiscal, 'body.cUrlNF');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url_xml_nota_fiscal);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $xml_nota_fiscal = curl_exec($ch);
            curl_close($ch);
            //print_r($xml_nota_fiscal);
            
            $arquivo_nome = "/var/tmp/".ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF').".xml";
            $arquivo = fopen($arquivo_nome, "a");
            fwrite($arquivo, $xml_nota_fiscal);
            fclose($arquivo);
            
            header('Content-disposition: Attachment; filename="'.ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF').'.xml"');
            header('Content-type: "text/xml"; charset="utf8"');
            readfile($arquivo_nome);
            
        }
        
        return $this->redirect(['index']);
    }
    
    public function actionGerarEtiqueta($id){
        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=','id',$id])->one();
        if($pedido_mercado_livre){
            
            $filial = ($pedido_mercado_livre->user_id == "193724256") ? Filial::find()->andWhere(['=','id',72])->one() : Filial::find()->andWhere(['=','id',98])->one();
            $meli = new Meli(static::APP_ID, static::SECRET_KEY);
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            $meliAccessToken = $response->access_token;
            //print_r($response); die;
            
            //$response_order = $meli->get("/orders/2592738061?access_token=" . $meliAccessToken);
            //print_r($response_order); die;
            
            //$response_etiqueta = $meli->get("/shipment_labels?shipment_ids=40023553497&savePdf=Y&access_token=" . $meliAccessToken);
            //print_r($response_etiqueta);
            
            $url = "https://api.mercadolibre.com/shipment_labels?shipment_ids=".$pedido_mercado_livre->shipping_id."&savePdf=Y&access_token=" . $meliAccessToken;
            
            $file_name = "/var/tmp/etiquetas/".$pedido_mercado_livre->shipping_id.".pdf";//basename($url);
            
            if(file_put_contents( $file_name,file_get_contents($url))) {
                echo "File downloaded successfully";
                
                header('Content-disposition: Attachment; filename="'.$pedido_mercado_livre->pedido_meli_id.".pdf".'"');
                header('Content-type: "application/pdf"');
                readfile($file_name);
            }
            else {
                echo "File downloading failed.";
            }
        }
    }

    public function actionBaixarPedidoML($order = null)
    {
        
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        
        $filial = Filial::find()->andWhere(['=','id',72])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;
        
        $filial_conta_duplicada = Filial::find()->andWhere(['=','id',98])->one();
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
        $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;
        
        $response_order = $meli->get("/orders/".$order."?access_token=" . $meliAccessToken);
        $token = $meliAccessToken;
        //echo "Order: ".$order."<pre>"; print_r($response_order); echo "</pre>"; die;
        
        if($response_order["httpCode"] >= 300){
            $response_order = $meli->get("/orders/".$order."?access_token=" . $meliAccessToken_conta_duplicada);
            if($response_order["httpCode"] < 300){
                $token = $meliAccessToken_conta_duplicada;
            }
            else{
                return $this->render('index', ["filtro" => null]);
            }
        }
        //echo "<pre>"; print_r($response_order); echo "</pre>"; die;
        
        $post_ml = [
            "resource"          => "/orders/".ArrayHelper::getValue($response_order, 'body.id'),
            "user_id"           => ArrayHelper::getValue($response_order, 'body.seller.id'),
            "topic"             => "orders_v2",
            "application_id"    => 3029992417140266,
            "attempts"          => 1,
            "sent"              => "2019-01-29T11:26:26.150Z",
            "received"          => "2019-01-29T11:26:26.126Z"
        ];
        
        if (ArrayHelper::getValue($post_ml, 'topic')=="orders" or ArrayHelper::getValue($post_ml, 'topic')=="created_orders" or ArrayHelper::getValue($post_ml, 'topic')=="orders_v2"){
            $meli = new Meli('3029992417140266', '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh');
            //$user = $meli->refreshAccessToken('TG-5e2efe08144ef6000642cdb6-193724256');
            $filial = Filial::find()->andWhere(['=', 'id', 72])->one();
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            
            if (ArrayHelper::getValue($post_ml, 'user_id')=="435343067"){
                $filial = Filial::find()->andWhere(['=', 'id', 98])->one();
                $user = $meli->refreshAccessToken($filial->refresh_token_meli);
                //print_r($user); die;
            }
            
            $response = ArrayHelper::getValue($user, 'body');
            
            //print_r($response);
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                
                //Obter dados do pedido do ML
                $response_order = $meli->get(ArrayHelper::getValue($post_ml, 'resource')."?access_token=" . $meliAccessToken);
                //echo "<pre>"; print_r($response_order); echo "</pre>"; die;
                if ($response_order['httpCode'] >= 300) {
                    echo "Sem informações do Pedido";
                }
                else {
                    $this->criarPedidoMercadoLivre($response_order);
                }
                //echo "<pre>"; print_r($response_order); echo "</pre>"; //die;
                
                /*if(ArrayHelper::keyExists('body.shipping.receiver_address', $response_order, false)){
                 Yii::$app->response->statusCode = 400;
                 return "Sem endereço";
                 }*/
                
                if(ArrayHelper::keyExists('body.shipping.id', $response_order, false)){
                    Yii::$app->response->statusCode = 400;
                    return "Sem endere  o";
                }
                
                $envio_dados = $meli->get("/shipments/".ArrayHelper::getValue($response_order, 'body.shipping.id')."?access_token=" . $meliAccessToken);
                //echo "<pre>"; print_r($envio_dados); echo "</pre>"; //die;
                
                if ($envio_dados['httpCode'] >= 300) {
                    echo "Sem informações de envio";
                }
                else {
                    $this->criarPedidoMercadoLivre($response_order, $envio_dados);
                }
                
                //die;
                $meli = new Omie(1,1);
                $body = [
                    "call" => "ConsultarCliente",
                    "app_key" => static::APP_KEY_OMIE_SP,
                    "app_secret" => static::APP_SECRET_OMIE_SP,
                    "param" => [
                        "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                    ]
                ];
                $response_omie = $meli->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
                
                if (ArrayHelper::getValue($response_omie, 'httpCode') == 200){
                    echo "Cliente já cadastrado <br><br>";
                } else{
                    //Adicionar novo CLIENTE
                    
                    $body = [
                        "call" => "IncluirCliente",
                        "app_key" => static::APP_KEY_OMIE_SP,
                        "app_secret" => static::APP_SECRET_OMIE_SP,
                        "param" => [
                            "codigo_cliente_integracao" => substr(ArrayHelper::getValue($response_order, 'body.buyer.id'),0,20),
                            "razao_social"              => str_replace(" ","%20",substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'), 0, 60)),
                            "cnpj_cpf"                  => substr(ArrayHelper::getValue($response_order, 'body.buyer.billing_info.doc_number'),0,20),
                            "nome_fantasia"             => str_replace(" ","%20",substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'),0,100)),
                            //"telefone1_ddd"             => substr(ArrayHelper::getValue($response_order, 'body.buyer.phone.area_code'),0,5),
                            //"telefone1_numero"          => substr(ArrayHelper::getValue($response_order, 'body.buyer.phone.number'),0,15),
                            "telefone1_ddd"             => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'),0,5),
                            "telefone1_numero"          => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'),0,15),
                            "contato"                   => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_name'),0,100)),
                            "endereco"                  => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_name'),0,60)),
                            "endereco_numero"           => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_number'),0,10),
                            "bairro"                    => str_replace(" ","%20",substr(((ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')=="") ? "Centro" : ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')),0,30)),
                            "complemento"               => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.comment'),0,40),
                            "estado"                    => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'),-2)),
                            "cidade"                    => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.city.name'),0,40)),
                            "cep"                       => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.zip_code'),0,10),
                            "email"                     => "cliente.pecaagora@gmail.com",//ArrayHelper::getValue($response_order, 'body.buyer.email'),
                        ]
                    ];
                    $response_omie = $meli->cria_cliente("api/v1/geral/clientes/?JSON=",$body);
                    echo "<br><br>"; print_r($response_omie);
                    
                    $body = [
                        "call" => "ConsultarCliente",
                        "app_key" => static::APP_KEY_OMIE_SP,
                        "app_secret" => static::APP_SECRET_OMIE_SP,
                        "param" => [
                            "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                        ]
                    ];
                    $response_omie = $meli->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
                }
                
                //Verificar se existe pedido
                $body = [
                    "call" => "ConsultarPedido",
                    "app_key" => static::APP_KEY_OMIE_SP,
                    "app_secret" => static::APP_SECRET_OMIE_SP,
                    "param" => [
                        "codigo_pedido_integracao" => ArrayHelper::getValue($response_order, 'body.id'),
                    ]
                ];
                $response_omie = $meli->consulta_pedido("api/v1/geral/pedidos/?JSON=",$body);
                
                
                if (ArrayHelper::getValue($response_omie, 'httpCode') == 200){
                    echo "Pedido já cadastrado <br><br>";
                } else{
                    //Adicionar novo PEDIDO
                    //echo "=====>>".ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')."<<====="; die;
                    $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                    if(!$produtoML){
                        $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id_sem_juros',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                        
                        if(!$produtoML){
                            $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id_full',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                        }
                    }
                    //echo " <==> Produto_filial: ";print_r($produtoML);
                    
                    //$cfop   = "6.102";
                    $cfop   = "6.108";
                    $csosn  = "102";
                    if (substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'),-2) == "SP"){
                        $cfop   = "5.405";
                        $csosn  = "500";
                    }
                    
                    /////////////////////////////
                    //IMPOSTO
                    /////////////////////////////
                    
                    //echo "<pre>"; var_dump($produtoML); echo "</pre>"; die;
                    
                    $imposto = array();
                    $imposto = $this->gerarImposto($csosn, $produtoML->produto);
                    
                    /////////////////////////////
                    //IMPOSTO
                    /////////////////////////////
                    
                    $body = [
                        "call" => "IncluirPedido",
                        "app_key" => static::APP_KEY_OMIE_SP,
                        "app_secret" => static::APP_SECRET_OMIE_SP,
                        "param" => [
                            "cabecalho" => [
                                "bloqueado"                 => "N",
                                //"codigo_cliente"            => ArrayHelper::getValue($response_omie, 'body.codigo_cliente_omie'),
                                "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                                "codigo_pedido_integracao"  => ArrayHelper::getValue($response_order, 'body.id'),
                                "etapa"                     => "10",
                                "data_previsao"             => substr(ArrayHelper::getValue($response_order, 'body.date_created'),8,2).'/'.substr(ArrayHelper::getValue($response_order, 'body.date_created'),5,2).'/'.substr(ArrayHelper::getValue($response_order, 'body.date_created'),0,4),
                                "quantidade_itens"          => ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                            ],
                            "det"=> [
                                "ide"=> [
                                    "codigo_item_integracao"    => $produtoML->produto->codigo_global,
                                    "regra_impostos"            => 0,
                                    "simples_nacional"           => "",
                                ],
                                "imposto" => $imposto,
                                
                                "produto" => [
                                    "codigo_produto_integracao" => "PA".$produtoML->produto->id,
                                    "cfop"                      => $cfop,
                                    "quantidade"                => ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                                    "valor_unitario"            => ArrayHelper::getValue($response_order, 'body.order_items.0.unit_price'),
                                ],
                            ],
                            "frete" => [
                                "codigo_transportadora" => 505552563,
                                "modalidade"            => 0,
                                "quantidade_volumes"    => 1,
                                "especie_volumes"       => "CAIXA"
                            ],
                            "informacoes_adicionais"    => [
                                "numero_contrato"           => ArrayHelper::getValue($response_order, 'body.payments.0.id'),
                                "numero_pedido_cliente"     => ArrayHelper::getValue($response_order, 'body.id'),
                                "consumidor_final"          => "S",
                                "codigo_categoria"          => "1.01.03",
                                "codVend"                   => 500726231,
                                "codigo_conta_corrente"     => 502875713,
                            ],
                        ],
                    ];
                    
                    //echo " <==> Body Pedido: ";print_r($body);
                    $response_omie = $meli->cria_pedido("api/v1/produtos/pedido/?JSON=",$body);
                    echo "<br><br> Resposta Pedido: "; print_r($response_omie);
                }
                
                ////////////////////////////////////////////////////////////////////////////////
                //CONTA DUPLICADA OMIE
                ////////////////////////////////////////////////////////////////////////////////
                if (ArrayHelper::getValue($post_ml, 'user_id')=="435343067"){
                    $body = [
                        "call" => "ConsultarCliente",
                        "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                        "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                        "param" => [
                            "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                        ]
                    ];
                    $response_omie = $meli->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
                    
                    if (ArrayHelper::getValue($response_omie, 'httpCode') == 200){
                        echo "Cliente já cadastrado <br><br>";
                    } else{
                        //Adicionar novo CLIENTE
                        
                        $body = [
                            "call" => "IncluirCliente",
                            "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                            "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                            "param" => [
                                "codigo_cliente_integracao" => substr(ArrayHelper::getValue($response_order, 'body.buyer.id'),0,20),
                                "razao_social"              => str_replace(" ","%20",substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'), 0, 60)),
                                "cnpj_cpf"                  => substr(ArrayHelper::getValue($response_order, 'body.buyer.billing_info.doc_number'),0,20),
                                "nome_fantasia"             => str_replace(" ","%20",substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name')." ".ArrayHelper::getValue($response_order, 'body.buyer.last_name'),0,100)),
                                "telefone1_ddd"             => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'),0,5),
                                "telefone1_numero"          => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'),0,15),
                                "contato"                   => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_name'),0,100)),
                                "endereco"                  => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_name'),0,60)),
                                "endereco_numero"           => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_number'),0,10),
                                "bairro"                    => str_replace(" ","%20",substr(((ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')=="") ? "Centro" : ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')),0,30)),
                                "complemento"               => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.comment'),0,40),
                                "estado"                    => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'),-2)),
                                "cidade"                    => str_replace(" ","%20",substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.city.name'),0,40)),
                                "cep"                       => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.zip_code'),0,10),
                                "email"                     => "cliente.pecaagora@gmail.com",//ArrayHelper::getValue($response_order, 'body.buyer.email'),
                            ]
                        ];
                        $response_omie = $meli->cria_cliente("api/v1/geral/clientes/?JSON=",$body);
                        echo "<br><br>"; print_r($response_omie);
                        
                        $body = [
                            "call" => "ConsultarCliente",
                            "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                            "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                            "param" => [
                                "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                            ]
                        ];
                        $response_omie = $meli->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
                    }
                    
                    //Verificar se existe pedido
                    $body = [
                        "call" => "ConsultarPedido",
                        "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                        "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                        "param" => [
                            "codigo_pedido_integracao" => ArrayHelper::getValue($response_order, 'body.id'),
                        ]
                    ];
                    $response_omie = $meli->consulta_pedido("api/v1/geral/pedidos/?JSON=",$body);
                    
                    if (ArrayHelper::getValue($response_omie, 'httpCode') == 200){
                        echo "Pedido já cadastrado <br><br>";
                    } else{
                        //Adicionar novo PEDIDO
                        $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                        if(!$produtoML){
                            $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id_sem_juros',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                            
                            if(!$produtoML){
                                $produtoML = ProdutoFilial::find()->andWhere(['=','meli_id_full',ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                            }
                        }
                        //echo " <==> Produto_filial: ";print_r($produtoML);
                        
                        //$cfop   = "6.102";
                        $cfop   = "6.108";
                        $csosn  = "102";
                        if (substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'),-2) == "SP"){
                            $cfop   = "5.405";
                            $csosn  = "500";
                        }
                        
                        $imposto = $this->gerarImposto($csosn, $produtoML->produto);
                        
                        $body = [
                            "call" => "IncluirPedido",
                            "app_key" => static::APP_KEY_OMIE_CONTA_DUPLICADA,
                            "app_secret" => static::APP_SECRET_OMIE_CONTA_DUPLICADA,
                            "param" => [
                                "cabecalho" => [
                                    "bloqueado"                 => "N",
                                    "codigo_cliente_integracao" => ArrayHelper::getValue($response_order, 'body.buyer.id'),
                                    "codigo_pedido_integracao"  => ArrayHelper::getValue($response_order, 'body.id'),
                                    "etapa"                     => "10",
                                    "data_previsao"             => substr(ArrayHelper::getValue($response_order, 'body.date_created'),8,2).'/'.substr(ArrayHelper::getValue($response_order, 'body.date_created'),5,2).'/'.substr(ArrayHelper::getValue($response_order, 'body.date_created'),0,4),
                                    "quantidade_itens"          => ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                                ],
                                "det"=> [
                                    "ide"=> [
                                        "codigo_item_integracao"    => $produtoML->produto->codigo_global,
                                        "regra_impostos"            => 0,
                                        "simples_nacional"           => "",
                                    ],
                                    "imposto" => $imposto,
                                    "produto" => [
                                        "codigo_produto_integracao" => "PA".$produtoML->produto->id,
                                        "cfop"                      => $cfop,
                                        "quantidade"                => ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                                        "valor_unitario"            => ArrayHelper::getValue($response_order, 'body.order_items.0.unit_price'),
                                    ],
                                ],
                                "frete" => [
                                    "codigo_transportadora" => 1018250911,
                                    "modalidade"            => 0,
                                    "quantidade_volumes"    => 1,
                                    "especie_volumes"       => "CAIXA"
                                ],
                                "informacoes_adicionais"    => [
                                    "numero_contrato"           => ArrayHelper::getValue($response_order, 'body.payments.0.id'),
                                    "numero_pedido_cliente"     => ArrayHelper::getValue($response_order, 'body.id'),
                                    "consumidor_final"          => "S",
                                    "codigo_categoria"          => "1.01.03",
                                    "codVend"                   => 1018256043,
                                    "codigo_conta_corrente"     => 1018255531,
                                ],
                            ],
                        ];
                        
                        //echo " <==> Body Pedido: ";print_r($body);
                        $response_omie = $meli->cria_pedido("api/v1/produtos/pedido/?JSON=",$body);
                        echo "<br><br> Resposta Pedido: "; print_r($response_omie);
                    }
                }
                ////////////////////////////////////////////////////////////////////////////////
                //CONTA DUPLICADA OMIE
                ////////////////////////////////////////////////////////////////////////////////
            }
        }
        
        return $this->render('index', ["filtro" => $order]);
    }
    
    public function gerarImposto($csosn, $produto){
        
        $imposto = [
            "ipi" => [
                "cod_sit_trib_ipi"  => 99,
                "enquadramento_ipi" => 999,
                "tipo_calculo_ipi"  => "B",
            ],
            "pis_padrao" => [
                "cod_sit_trib_pis"  => 49,
                "tipo_calculo_pis"  => "B",
            ],
            "cofins_padrao" => [
                "cod_sit_trib_cofins"   => 49,
                "tipo_calculo_cofins"   => "B",
            ],
            "icms_sn" => [
                "cod_sit_trib_icms_sn" => $csosn,
            ],
        ];
        
        $codigos = [
            40161010,
            6813,
            70071100,
            70072100,
            70091000,
            83012000,
            83023000,
            84073390,
            84073490,
            840820,
            840991,
            840999,
            841330,
            84148021,
            84148022,
            841520,
            84212300,
            84213100,
            84314100,
            84314200,
            84339090,
            848310,
            84832000,
            848330,
            848340,
            848350,
            850520,
            85071000,
            8511,
            851220,
            85123000,
            851240,
            85129000,
            85272,
            853910,
            85443000,
            870600,
            8707,
            8708,
            90292010,
            90299010,
            90303921,
            90318040,
            9032892,
            91040000,
            94012000
        ];
        
        //echo "<pre>"; print_r($imposto); echo "</pre>";
        
        foreach($codigos as $k => $codigo){
            
            //echo "<br>".$k." - ".$codigo;
            
            $quantidade_caracteres = strlen($codigo);
            $ncm = str_replace('.','',$produto->codigo_montadora);
            $sub_ncm = substr($ncm,0,$quantidade_caracteres);
            
            //echo " - ".$quantidade_caracteres." - ".$sub_ncm;
            
            if($sub_ncm == $codigo){
                $imposto["cofins_padrao"]["cod_sit_trib_cofins"] = "06";
                $imposto["cofins_padrao"]["tipo_calculo_cofins"] = "";
                
                $imposto["pis_padrao"]["cod_sit_trib_pis"] = "06";
                $imposto["pis_padrao"]["tipo_calculo_pis"] = "";
                
                break;
            }
        }
        
        //echo "<pre>"; print_r($imposto); echo "</pre>";
        
    }
    
    public function criarPedidoMercadoLivre($order = null, $shipping = null){
        
        //echo "<pre>"; print_r($order); echo "</pre>";
        //echo "<pre>"; print_r($shipping); echo "</pre>";die;
        
        if(is_null($order)){
            return;
        }
        
        $pedido_mercado_livre = PedidoMercadoLivre::find()->andwhere(['=', 'pedido_meli_id', ArrayHelper::getValue($order, 'body.id')])->one();
        if($pedido_mercado_livre){
            $pedido_mercado_livre->pedido_meli_id       = (string) ArrayHelper::getValue($order, 'body.id');
            $pedido_mercado_livre->total_amount         = ArrayHelper::getValue($order, 'body.total_amount');
            $pedido_mercado_livre->date_created         = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.date_created'),0,19));
            $pedido_mercado_livre->date_closed          = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.date_closed'),0,19));
            $pedido_mercado_livre->last_updated         = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.last_updated'),0,19));
            $pedido_mercado_livre->paid_amount          = (string) ArrayHelper::getValue($order, 'body.paid_amount');
            $pedido_mercado_livre->shipping_id          = (string) ArrayHelper::getValue($order, 'body.shipping.id');
            $pedido_mercado_livre->status               = (string) ArrayHelper::getValue($order, 'body.status');
            $pedido_mercado_livre->buyer_id             = (string) ArrayHelper::getValue($order, 'body.buyer.id');
            $pedido_mercado_livre->buyer_nickname       = (string) (isset($order["body"]->buyer->nickname)) ? ArrayHelper::getValue($order, 'body.buyer.nickname') : "";
            $pedido_mercado_livre->buyer_email          = (string) (isset($order["body"]->buyer->email)) ? ArrayHelper::getValue($order, 'body.buyer.email') : "";
            $pedido_mercado_livre->buyer_first_name     = (string) (isset($order["body"]->buyer->first_name)) ? ArrayHelper::getValue($order, 'body.buyer.first_name') : "";
            $pedido_mercado_livre->buyer_last_name      = (string) (isset($order["body"]->buyer->last_name)) ? ArrayHelper::getValue($order, 'body.buyer.last_name') : "";
            $pedido_mercado_livre->buyer_doc_type       = (string) (isset($order["body"]->buyer->billing_info->doc_type)) ? ArrayHelper::getValue($order, 'body.buyer.billing_info.doc_type') : "";
            $pedido_mercado_livre->buyer_doc_number     = (string) (isset($order["body"]->buyer->billing_info->doc_number)) ? ArrayHelper::getValue($order, 'body.buyer.billing_info.doc_number') : "";
            $pedido_mercado_livre->user_id              = (string) ArrayHelper::getValue($order, 'body.seller.id');
            $pedido_mercado_livre->pack_id              = (string) ArrayHelper::getValue($order, 'body.pack_id');
            
            if(is_null($pedido_mercado_livre->email_enderecos)){
                $pedido_mercado_livre->email_enderecos = "entregasp.pecaagora@gmail.com; notafiscal.pecaagora@gmail.com; compras.pecaagora@gmail.com; entregasp.pecaagora@gmail.com";
                
            }
            
            if($pedido_mercado_livre->save()){
                echo "Pedido alterado";
            }
            else{
                echo "Pedido não alterado";
            }
        }
        else{
            $pedido_mercado_livre = new PedidoMercadoLivre();
            $pedido_mercado_livre->pedido_meli_id       = (string) ArrayHelper::getValue($order, 'body.id');
            $pedido_mercado_livre->total_amount         = ArrayHelper::getValue($order, 'body.total_amount');
            $pedido_mercado_livre->date_created         = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.date_created'),0,19));
            $pedido_mercado_livre->date_closed          = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.date_closed'),0,19));
            $pedido_mercado_livre->last_updated         = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.last_updated'),0,19));
            $pedido_mercado_livre->paid_amount          = (string) ArrayHelper::getValue($order, 'body.paid_amount');
            $pedido_mercado_livre->shipping_id          = (string) ArrayHelper::getValue($order, 'body.shipping.id');
            $pedido_mercado_livre->status               = (string) ArrayHelper::getValue($order, 'body.status');
            $pedido_mercado_livre->buyer_id             = (string) ArrayHelper::getValue($order, 'body.buyer.id');
            $pedido_mercado_livre->buyer_nickname       = (string) (isset($order["body"]->buyer->nickname)) ? ArrayHelper::getValue($order, 'body.buyer.nickname') : "";
            $pedido_mercado_livre->buyer_email          = (string) (isset($order["body"]->buyer->email)) ? ArrayHelper::getValue($order, 'body.buyer.email') : "";
            $pedido_mercado_livre->buyer_first_name     = (string) ArrayHelper::getValue($order, 'body.buyer.first_name');
            $pedido_mercado_livre->buyer_last_name      = (string) ArrayHelper::getValue($order, 'body.buyer.last_name');
            $pedido_mercado_livre->buyer_doc_type       = (string) ArrayHelper::getValue($order, 'body.buyer.billing_info.doc_type');
            $pedido_mercado_livre->buyer_doc_number     = (string) ArrayHelper::getValue($order, 'body.buyer.billing_info.doc_number');
            $pedido_mercado_livre->user_id              = (string) ArrayHelper::getValue($order, 'body.seller.id');
            $pedido_mercado_livre->pack_id              = (string) ArrayHelper::getValue($order, 'body.pack_id');
            $pedido_mercado_livre->email_enderecos      = "entregasp.pecaagora@gmail.com, notafiscal.pecaagora@gmail.com, compras.pecaagora@gmail.com";
            
            if($pedido_mercado_livre->save()){
                echo "Pedido criado";
            }
            else{
                echo "Pedido não criado";
            }
        }
        
        if($pedido_mercado_livre){
            
            //echo "123"; var_dump((string)ArrayHelper::getValue($shipping, 'body.receiver_address.city.id'));die;
            
            //Cadastra os dados de envio e do recebedor
            if(!is_null($shipping)){
                $pedido_mercado_livre->shipping_base_cost                   = ArrayHelper::getValue($shipping, 'body.base_cost');
                $pedido_mercado_livre->shipping_status                      = (string) ArrayHelper::getValue($shipping, 'body.status');
                $pedido_mercado_livre->shipping_substatus                   = (string) ArrayHelper::getValue($shipping, 'body.substatus');
                $pedido_mercado_livre->shipping_date_created                = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.date_created'),0,19));
                $pedido_mercado_livre->shipping_last_updated                = str_replace("T", " ",substr(ArrayHelper::getValue($order, 'body.last_updated'),0,19));
                $pedido_mercado_livre->shipping_tracking_number             = (string) ArrayHelper::getValue($shipping, 'body.tracking_number');
                $pedido_mercado_livre->shipping_tracking_method             = (string) ArrayHelper::getValue($shipping, 'body.tracking_method');
                $pedido_mercado_livre->shipping_service_id                  = (string) ArrayHelper::getValue($shipping, 'body.service_id');
                $pedido_mercado_livre->receiver_id                          = (string) ArrayHelper::getValue($shipping, 'body.receiver_id');
                $pedido_mercado_livre->receiver_address_id                  = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.id');
                $pedido_mercado_livre->receiver_address_line                = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.address_line');
                $pedido_mercado_livre->receiver_street_name                 = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_name');
                $pedido_mercado_livre->receiver_street_number               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_number');
                $pedido_mercado_livre->receiver_comment                     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.comment');
                $pedido_mercado_livre->receiver_zip_code                    = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.zip_code');
                $pedido_mercado_livre->receiver_city_id                     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.id');
                $pedido_mercado_livre->receiver_city_name                   = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.name');
                $pedido_mercado_livre->receiver_state_id                    = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.id');
                $pedido_mercado_livre->receiver_state_name                  = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.name');
                $pedido_mercado_livre->receiver_country_id                  = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.id');
                $pedido_mercado_livre->receiver_country_name                = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.name');
                $pedido_mercado_livre->receiver_neighborhood_id             = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.id');
                $pedido_mercado_livre->receiver_neighborhood_name           = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.name');
                $pedido_mercado_livre->receiver_municipality_id             = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.id');
                $pedido_mercado_livre->receiver_municipality_name           = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.name');
                $pedido_mercado_livre->receiver_delivery_preference         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.delivery_preference');
                $pedido_mercado_livre->receiver_name                        = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_name');
                $pedido_mercado_livre->receiver_phone                       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_phone');
                $pedido_mercado_livre->shipping_option_id                   = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.id');
                $pedido_mercado_livre->shipping_option_shipping_method_id   = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.shipping_method_id');
                $pedido_mercado_livre->shipping_option_name                 = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.name');
                $pedido_mercado_livre->shipping_option_list_cost            = ArrayHelper::getValue($shipping, 'body.shipping_option.list_cost');
                $pedido_mercado_livre->shipping_option_cost                 = ArrayHelper::getValue($shipping, 'body.shipping_option.cost');
                $pedido_mercado_livre->shipping_option_delivery_type        = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.delivery_type');
                //echo "<pre>"; print_r($pedido_mercado_livre->receiver_city_id); echo "</pre>";
                //echo "<pre>"; var_dump( ArrayHelper::getValue($shipping, 'body.receiver_address.city.id')); echo "</pre>";
                //echo "<pre>"; print_r($pedido_mercado_livre); echo "</pre>";
                
                //var_dump($pedido_mercado_livre->save()); die;
                
                if($pedido_mercado_livre->save()){
                    echo "<br><br><br>Shipping alterado";
                }
                else{
                    echo "<br><br><br>Shipping não alterado";
                }
                
                
                //SHIPPING
                $pedido_mercado_livre_shipments = PedidoMercadoLivreShipments::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($shipping, 'body.id')])->one();
                if($pedido_mercado_livre_shipments){
                    echo "<br><br>dados shipments já cadastrados";
                    
                    $pedido_mercado_livre_shipments->meli_id                                = (string) ArrayHelper::getValue($shipping, 'body.id');
                    $pedido_mercado_livre_shipments->mode                                   = (string) ArrayHelper::getValue($shipping, 'body.mode');
                    $pedido_mercado_livre_shipments->created_by                             = (string) ArrayHelper::getValue($shipping, 'body.created_by');
                    $pedido_mercado_livre_shipments->order_id                               = (string) ArrayHelper::getValue($shipping, 'body.order_id');
                    $pedido_mercado_livre_shipments->order_cost                             = (float) ArrayHelper::getValue($shipping, 'body.order_cost');
                    $pedido_mercado_livre_shipments->base_cost                              = (float) ArrayHelper::getValue($shipping, 'body.base_cost');
                    $pedido_mercado_livre_shipments->site_id                                = (string) ArrayHelper::getValue($shipping, 'body.site_id');
                    $pedido_mercado_livre_shipments->status                                 = (string) ArrayHelper::getValue($shipping, 'body.status');
                    $pedido_mercado_livre_shipments->substatus                              = (string) ArrayHelper::getValue($shipping, 'body.substatus');
                    $pedido_mercado_livre_shipments->history_date_cancelled                 = (isset($shipping["body"]->status_history->date_cancelled)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_cancelled'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_delivered                 = (isset($shipping["body"]->status_history->date_delivered)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_delivered'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_first_visit               = (isset($shipping["body"]->status_history->date_first_visit)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_first_visit'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_handling                  = (isset($shipping["body"]->status_history->date_handling)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_handling'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_not_delivered             = (isset($shipping["body"]->status_history->date_not_delivered)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_not_delivered'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_ready_to_ship             = (isset($shipping["body"]->status_history->date_ready_to_ship)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_ready_to_ship'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_shipped                   = (isset($shipping["body"]->status_history->date_shipped)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_shipped'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_returned                  = (isset($shipping["body"]->status_history->date_returned)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_returned'),0,19)) : "";
                    $pedido_mercado_livre_shipments->date_created                           = (isset($shipping["body"]->status_history->date_created)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_created'),0,19)) : "";
                    $pedido_mercado_livre_shipments->last_updated                           = (isset($shipping["body"]->status_history->last_updated)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.last_updated'),0,19)) : "";
                    $pedido_mercado_livre_shipments->tracking_number                        = (string) ArrayHelper::getValue($shipping, 'body.tracking_number');
                    $pedido_mercado_livre_shipments->tracking_method                        = (string) ArrayHelper::getValue($shipping, 'body.tracking_method');
                    $pedido_mercado_livre_shipments->service_id                             = (string) ArrayHelper::getValue($shipping, 'body.service_id');
                    $pedido_mercado_livre_shipments->sender_id                              = (string) ArrayHelper::getValue($shipping, 'body.sender_id');
                    $pedido_mercado_livre_shipments->receiver_id                            = (string) ArrayHelper::getValue($shipping, 'body.receiver_id');
                    $pedido_mercado_livre_shipments->receiver_address_id                    = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.id');
                    $pedido_mercado_livre_shipments->receiver_address_address_line          = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.address_line');
                    $pedido_mercado_livre_shipments->receiver_address_street_name           = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_name');
                    $pedido_mercado_livre_shipments->receiver_address_street_number         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_number');
                    $pedido_mercado_livre_shipments->receiver_address_comment               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.comment');
                    $pedido_mercado_livre_shipments->receiver_address_zip_code              = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.zip_code');
                    $pedido_mercado_livre_shipments->receiver_address_city_id               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.id');
                    $pedido_mercado_livre_shipments->receiver_address_city_name             = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.name');
                    $pedido_mercado_livre_shipments->receiver_address_state_id              = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.id');
                    $pedido_mercado_livre_shipments->receiver_address_state_name            = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.name');
                    $pedido_mercado_livre_shipments->receiver_address_country_id            = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.id');
                    $pedido_mercado_livre_shipments->receiver_address_country_name          = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.name');
                    $pedido_mercado_livre_shipments->receiver_address_neighborhood_id       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.id');
                    $pedido_mercado_livre_shipments->receiver_address_neighborhood_name     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.name');
                    $pedido_mercado_livre_shipments->receiver_address_municipality_id       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.id');
                    $pedido_mercado_livre_shipments->receiver_address_municipality_name     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.name');
                    $pedido_mercado_livre_shipments->receiver_address_delivery_preference   = (isset($shipping["body"]->receiver_address->delivery_preference)) ? (string) ArrayHelper::getValue($shipping, 'body.receiver_address.delivery_preference') : "";
                    $pedido_mercado_livre_shipments->receiver_address_receiver_name         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_name');
                    $pedido_mercado_livre_shipments->receiver_address_receiver_phone        = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_phone');
                    $pedido_mercado_livre_shipments->shipping_option_id                     = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.id');
                    $pedido_mercado_livre_shipments->shipping_option_shipping_method_id     = (isset($shipping["body"]->shipping_option->shipping_method_id)) ? (string) ArrayHelper::getValue($shipping, 'body.shipping_option.shipping_method_id') : "";
                    $pedido_mercado_livre_shipments->shipping_option_name                   = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.name');
                    $pedido_mercado_livre_shipments->shipping_option_currency_id            = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.currency_id');
                    $pedido_mercado_livre_shipments->shipping_option_list_cost              = (float) ArrayHelper::getValue($shipping, 'body.shipping_option.list_cost');
                    $pedido_mercado_livre_shipments->shipping_option_cost                   = (float) ArrayHelper::getValue($shipping, 'body.shipping_option.cost');
                    $pedido_mercado_livre_shipments->delivery_type                          = (isset($shipping["body"]->shipping_option->delivery_type)) ? (string) ArrayHelper::getValue($shipping, 'body.shipping_option.delivery_type') : "";
                    $pedido_mercado_livre_shipments->comments                               = (string) ArrayHelper::getValue($shipping, 'body.comments');
                    $pedido_mercado_livre_shipments->date_first_printed                     = str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.date_first_printed'),0,19));
                    $pedido_mercado_livre_shipments->market_place                           = (string) ArrayHelper::getValue($shipping, 'body.market_place');
                    $pedido_mercado_livre_shipments->type                                   = (isset($shipping["body"]->type)) ? (string) ArrayHelper::getValue($shipping, 'body.type') : "";
                    $pedido_mercado_livre_shipments->logistic_type                          = (isset($shipping["body"]->logistic_type)) ? (string) ArrayHelper::getValue($shipping, 'body.logistic_type') : "";
                    $pedido_mercado_livre_shipments->application_id                         = (isset($shipping["body"]->application_id)) ? (string) ArrayHelper::getValue($shipping, 'body.application_id') : "";
                    $pedido_mercado_livre_shipments->pedido_mercado_livre_id                = $pedido_mercado_livre->id;
                    
                    //echo "<pre>===>>"; print_r($pedido_mercado_livre_shipments); echo "<<==="; die;
                    
                    if($pedido_mercado_livre_shipments->save()){
                        echo "<br><br><br>Shipping tabela alterado";
                        
                        //echo "<pre>===>>"; print_r($pedido_mercado_livre_shipments); echo "<<==="; die;
                        
                        foreach(ArrayHelper::getValue($shipping, 'body.shipping_items') as $shipping_item){
                            
                            //echo "<pre>"; print_r($shipping_item); echo "</pre>"; die;
                            
                            $status_item = "alterado";
                            $pedido_mercado_livre_shipments_item = PedidoMercadoLivreShipmentsItem::find()->andWhere(['=', 'pedido_mercado_livre_shipments_id', $pedido_mercado_livre_shipments->id])
                            ->andWhere(['=', 'meli_id', $shipping_item->id])
                            ->one();
                            if(!$pedido_mercado_livre_shipments_item){
                                $pedido_mercado_livre_shipments_item = new PedidoMercadoLivreShipmentsItem;
                                $status_item = "criado";
                            }
                            
                            $pedido_mercado_livre_shipments_item->pedido_mercado_livre_shipments_id   = $pedido_mercado_livre_shipments->id;
                            $pedido_mercado_livre_shipments_item->meli_id                             = (string) $shipping_item->id;
                            $pedido_mercado_livre_shipments_item->description                         = (string) $shipping_item->description;
                            $pedido_mercado_livre_shipments_item->quantity                            = $shipping_item->quantity;
                            $pedido_mercado_livre_shipments_item->dimensions                          = (string) $shipping_item->dimensions;
                            $pedido_mercado_livre_shipments_item->dimensions_source_id                = (string) $shipping_item->dimensions_source->id;
                            $pedido_mercado_livre_shipments_item->dimensions_source_origin            = (string) $shipping_item->dimensions_source->origin;
                            if($pedido_mercado_livre_shipments_item->save()){
                                echo "<br><br><br>Shipping Item ".$status_item;
                            }
                            else{
                                echo "<br><br><br>Shipping Item não ".$status_item;
                            }
                        }
                    }
                    else{
                        echo "<br><br><br>Shipping tabela não alterado";
                    }
                }
                else{
                    echo "<br><br>dados shipments não cadastrados";
                    
                    $pedido_mercado_livre_shipments = new PedidoMercadoLivreShipments;
                    $pedido_mercado_livre_shipments->meli_id                                = (string) ArrayHelper::getValue($shipping, 'body.id');
                    $pedido_mercado_livre_shipments->mode                                   = (string) ArrayHelper::getValue($shipping, 'body.mode');
                    $pedido_mercado_livre_shipments->created_by                             = (string) ArrayHelper::getValue($shipping, 'body.created_by');
                    $pedido_mercado_livre_shipments->order_id                               = (string) ArrayHelper::getValue($shipping, 'body.order_id');
                    $pedido_mercado_livre_shipments->order_cost                             = (float) ArrayHelper::getValue($shipping, 'body.order_cost');
                    $pedido_mercado_livre_shipments->base_cost                              = (float) ArrayHelper::getValue($shipping, 'body.base_cost');
                    $pedido_mercado_livre_shipments->site_id                                = (string) ArrayHelper::getValue($shipping, 'body.site_id');
                    $pedido_mercado_livre_shipments->status                                 = (string) ArrayHelper::getValue($shipping, 'body.status');
                    $pedido_mercado_livre_shipments->substatus                              = (string) ArrayHelper::getValue($shipping, 'body.substatus');
                    $pedido_mercado_livre_shipments->history_date_cancelled                 = (isset($shipping["body"]->status_history->date_cancelled)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_cancelled'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_delivered                 = (isset($shipping["body"]->status_history->date_delivered)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_delivered'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_first_visit               = (isset($shipping["body"]->status_history->date_first_visit)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_first_visit'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_handling                  = (isset($shipping["body"]->status_history->date_handling)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_handling'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_not_delivered             = (isset($shipping["body"]->status_history->date_not_delivered)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_not_delivered'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_ready_to_ship             = (isset($shipping["body"]->status_history->date_ready_to_ship)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_ready_to_ship'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_shipped                   = (isset($shipping["body"]->status_history->date_shipped)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_shipped'),0,19)) : "";
                    $pedido_mercado_livre_shipments->history_date_returned                  = (isset($shipping["body"]->status_history->date_returned)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_returned'),0,19)) : "";
                    $pedido_mercado_livre_shipments->date_created                           = (isset($shipping["body"]->status_history->date_created)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.date_created'),0,19)) : "";
                    $pedido_mercado_livre_shipments->last_updated                           = (isset($shipping["body"]->status_history->last_updated)) ? str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.status_history.last_updated'),0,19)) : "";
                    $pedido_mercado_livre_shipments->tracking_number                        = (string) ArrayHelper::getValue($shipping, 'body.tracking_number');
                    $pedido_mercado_livre_shipments->tracking_method                        = (string) ArrayHelper::getValue($shipping, 'body.tracking_method');
                    $pedido_mercado_livre_shipments->service_id                             = (string) ArrayHelper::getValue($shipping, 'body.service_id');
                    $pedido_mercado_livre_shipments->sender_id                              = (string) ArrayHelper::getValue($shipping, 'body.sender_id');
                    $pedido_mercado_livre_shipments->receiver_id                            = (string) ArrayHelper::getValue($shipping, 'body.receiver_id');
                    $pedido_mercado_livre_shipments->receiver_address_id                    = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.id');
                    $pedido_mercado_livre_shipments->receiver_address_address_line          = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.address_line');
                    $pedido_mercado_livre_shipments->receiver_address_street_name           = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_name');
                    $pedido_mercado_livre_shipments->receiver_address_street_number         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_number');
                    $pedido_mercado_livre_shipments->receiver_address_comment               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.comment');
                    $pedido_mercado_livre_shipments->receiver_address_zip_code              = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.zip_code');
                    $pedido_mercado_livre_shipments->receiver_address_city_id               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.id');
                    $pedido_mercado_livre_shipments->receiver_address_city_name             = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.name');
                    $pedido_mercado_livre_shipments->receiver_address_state_id              = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.id');
                    $pedido_mercado_livre_shipments->receiver_address_state_name            = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.name');
                    $pedido_mercado_livre_shipments->receiver_address_country_id            = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.id');
                    $pedido_mercado_livre_shipments->receiver_address_country_name          = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.name');
                    $pedido_mercado_livre_shipments->receiver_address_neighborhood_id       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.id');
                    $pedido_mercado_livre_shipments->receiver_address_neighborhood_name     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.name');
                    $pedido_mercado_livre_shipments->receiver_address_municipality_id       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.id');
                    $pedido_mercado_livre_shipments->receiver_address_municipality_name     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.name');
                    $pedido_mercado_livre_shipments->receiver_address_delivery_preference   = (isset($shipping["body"]->receiver_address->delivery_preference)) ? (string) ArrayHelper::getValue($shipping, 'body.receiver_address.delivery_preference') : "";
                    $pedido_mercado_livre_shipments->receiver_address_receiver_name         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_name');
                    $pedido_mercado_livre_shipments->receiver_address_receiver_phone        = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_phone');
                    $pedido_mercado_livre_shipments->shipping_option_id                     = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.id');
                    $pedido_mercado_livre_shipments->shipping_option_shipping_method_id     = (isset($shipping["body"]->shipping_option->shipping_method_id)) ? (string) ArrayHelper::getValue($shipping, 'body.shipping_option.shipping_method_id') : "";
                    $pedido_mercado_livre_shipments->shipping_option_name                   = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.name');
                    $pedido_mercado_livre_shipments->shipping_option_currency_id            = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.currency_id');
                    $pedido_mercado_livre_shipments->shipping_option_list_cost              = (float) ArrayHelper::getValue($shipping, 'body.shipping_option.list_cost');
                    $pedido_mercado_livre_shipments->shipping_option_cost                   = (float) ArrayHelper::getValue($shipping, 'body.shipping_option.cost');
                    $pedido_mercado_livre_shipments->delivery_type                          = (isset($shipping["body"]->shipping_option->delivery_type)) ? (string) ArrayHelper::getValue($shipping, 'body.shipping_option.delivery_type') : "";
                    $pedido_mercado_livre_shipments->comments                               = (string) ArrayHelper::getValue($shipping, 'body.comments');
                    $pedido_mercado_livre_shipments->date_first_printed                     = str_replace("T", " ",substr(ArrayHelper::getValue($shipping, 'body.date_first_printed'),0,19));
                    $pedido_mercado_livre_shipments->market_place                           = (string) ArrayHelper::getValue($shipping, 'body.market_place');
                    $pedido_mercado_livre_shipments->type                                   = (isset($shipping["body"]->type)) ? (string) ArrayHelper::getValue($shipping, 'body.type') : "";
                    $pedido_mercado_livre_shipments->logistic_type                          = (isset($shipping["body"]->logistic_type)) ? (string) ArrayHelper::getValue($shipping, 'body.logistic_type') : "";
                    $pedido_mercado_livre_shipments->application_id                         = (isset($shipping["body"]->application_id)) ? (string) ArrayHelper::getValue($shipping, 'body.application_id') : "";
                    $pedido_mercado_livre_shipments->pedido_mercado_livre_id                = $pedido_mercado_livre->id;
                    
                    
                    //echo "<pre>===>>"; var_dump($pedido_mercado_livre_shipments); echo "<<===";
                    //die;
                    
                    //echo "======>>>"; var_dump($pedido_mercado_livre_shipments->save()); echo "<<<======";
                    
                    if($pedido_mercado_livre_shipments->save()){
                        echo "<br><br><br>Shipping tabela criado";
                        
                        foreach(ArrayHelper::getValue($shipping, 'body.shipping_items') as $shipping_item){
                            
                            $status_item = "alterado";
                            $pedido_mercado_livre_shipments_item = PedidoMercadoLivreShipmentsItem::find()->andWhere(['=', 'pedido_mercado_livre_shipments_id', $pedido_mercado_livre_shipments->id])
                            ->andWhere(['=', 'meli_id', $shipping_item->id])
                            ->one();
                            if(!$pedido_mercado_livre_shipments_item){
                                $pedido_mercado_livre_shipments_item = new PedidoMercadoLivreShipmentsItem;
                                $status_item = "criado";
                            }
                            
                            //echo "<pre>"; print_r($shipping_item); echo "</pre>"; die;
                            $pedido_mercado_livre_shipments_item->pedido_mercado_livre_shipments_id   = $pedido_mercado_livre_shipments->id;
                            $pedido_mercado_livre_shipments_item->meli_id                             = (string) $shipping_item->id;
                            $pedido_mercado_livre_shipments_item->description                         = (string) $shipping_item->description;
                            $pedido_mercado_livre_shipments_item->quantity                            = $shipping_item->quantity;
                            $pedido_mercado_livre_shipments_item->dimensions                          = (string) $shipping_item->dimensions;
                            $pedido_mercado_livre_shipments_item->dimensions_source_id                = (string) (isset($shipping["body"]->dimensions_source->id)) ? ArrayHelper::getValue($shipping, 'body.dimensions_source.id') : "";
                            $pedido_mercado_livre_shipments_item->dimensions_source_origin            = (string) (isset($shipping["body"]->dimensions_source->origin)) ? ArrayHelper::getValue($shipping, 'body.dimensions_source.origin') : "";
                            if($pedido_mercado_livre_shipments_item->save()){
                                echo "<br><br><br>Shipping Item ".$status_item;
                            }
                            else{
                                echo "<br><br><br>Shipping Item não ".$status_item;
                            }
                        }
                    }
                    else{
                        echo "<br><br><br>Shipping tabela não criado";
                        
                        
                    }
                }
                //SHIPPING
            }
            
            //Cadastra os dados dos produtos
            
            $produtos_email = "
Boa tarde, Como vai?
                
Segue dados de um novo pedido a ser faturado. Nosso financeiro irá realizar o pagamento conforme combinado anteriormente.
                
Cód.: {codigo}
Descrição: {descricao}
Quantidade: {quantidade}
Valor: R$ {valor}
Observação: {observacao}
                
Envio: Carmópolis de Minas, 963, Vila Maria.
                
  
Atenciosamente,
                
                
Peça Agora
Site: https://www.pecaagora.com/
E-mail: compras.pecaagora@gmail.comSetor de Compras:(32)3015-0023Whatsapp:(32)988354007
Skype: pecaagora";
            
            
            
            foreach(ArrayHelper::getValue($order, 'body.order_items') as $k => $produto){
                
                $pedido_mercado_livre_produto = PedidoMercadoLivreProduto::find()->andWhere(['=', 'pedido_mercado_livre_id', $pedido_mercado_livre->id])
                ->andWhere(['=', 'produto_meli_id', ArrayHelper::getValue($produto, 'item.id')])
                ->one();
                if($pedido_mercado_livre_produto){
                    $pedido_mercado_livre_produto->pedido_mercado_livre_id = $pedido_mercado_livre->id;
                    $pedido_mercado_livre_produto->produto_meli_id         = ArrayHelper::getValue($produto, 'item.id');
                    $pedido_mercado_livre_produto->title                   = ArrayHelper::getValue($produto, 'item.title');
                    $pedido_mercado_livre_produto->categoria_meli_id       = ArrayHelper::getValue($produto, 'item.category_id');
                    $pedido_mercado_livre_produto->condition               = ArrayHelper::getValue($produto, 'item.condition');
                    $pedido_mercado_livre_produto->quantity                = ArrayHelper::getValue($produto, 'quantity');
                    $pedido_mercado_livre_produto->unit_price              = ArrayHelper::getValue($produto, 'unit_price');
                    $pedido_mercado_livre_produto->full_unit_price         = ArrayHelper::getValue($produto, 'full_unit_price');
                    $pedido_mercado_livre_produto->sale_fee                = ArrayHelper::getValue($produto, 'sale_fee');
                    $pedido_mercado_livre_produto->listing_type_id         = ArrayHelper::getValue($produto, 'listing_type_id');
                    
                    //$produto_filial = ProdutoFilial::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])->one();
                    $produto_filial = ProdutoFilial::find()	->orWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])
                    ->orWhere(['=', 'meli_id_sem_juros', ArrayHelper::getValue($produto, 'item.id')])
                    ->orWhere(['=', 'meli_id_full', ArrayHelper::getValue($produto, 'item.id')])
                    ->one();
                    if($produto_filial){
                        $pedido_mercado_livre_produto->produto_filial_id = $produto_filial->id;
                    }
                    
                    if($pedido_mercado_livre_produto->save()){
                        echo "<br><br><br>Produto alterado";
                    }
                    else{
                        echo "<br><br><br>Produto não alterado";
                    }
                }
                else{
                    //echo "<pre>==>"; print_r($pedido_mercado_livre->id); echo "<==</pre>"; die;
                    $pedido_mercado_livre_produto                          = new PedidoMercadoLivreProduto();
                    $pedido_mercado_livre_produto->pedido_mercado_livre_id = $pedido_mercado_livre->id;
                    $pedido_mercado_livre_produto->produto_meli_id         = ArrayHelper::getValue($produto, 'item.id');
                    $pedido_mercado_livre_produto->title                   = ArrayHelper::getValue($produto, 'item.title');
                    $pedido_mercado_livre_produto->categoria_meli_id       = ArrayHelper::getValue($produto, 'item.category_id');
                    $pedido_mercado_livre_produto->condition               = ArrayHelper::getValue($produto, 'item.condition');
                    $pedido_mercado_livre_produto->quantity                = ArrayHelper::getValue($produto, 'quantity');
                    $pedido_mercado_livre_produto->unit_price              = ArrayHelper::getValue($produto, 'unit_price');
                    $pedido_mercado_livre_produto->full_unit_price         = ArrayHelper::getValue($produto, 'full_unit_price');
                    $pedido_mercado_livre_produto->sale_fee                = ArrayHelper::getValue($produto, 'sale_fee');
                    $pedido_mercado_livre_produto->listing_type_id         = ArrayHelper::getValue($produto, 'listing_type_id');
                    
                    //$produto_filial = ProdutoFilial::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])->one();
                    $produto_filial = ProdutoFilial::find()     ->orWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])
                    ->orWhere(['=', 'meli_id_sem_juros', ArrayHelper::getValue($produto, 'item.id')])
                    ->orWhere(['=', 'meli_id_full', ArrayHelper::getValue($produto, 'item.id')])
                    ->one();
                    if($produto_filial){
                        $pedido_mercado_livre_produto->produto_filial_id = $produto_filial->id;
                    }
                    
                    if($pedido_mercado_livre_produto->save()){
                        echo "<br><br><br>Produto criado";
                    }
                    else{
                        echo "<br><br><br>Produto não criado";
                    }
                }
                
                /*$produtos_email .= "\n\nCódigo: {codigo}";
                 if($produto_filial){
                 $produtos_email .= $produto_filial->produto->codigo_fabricante;
                 }
                 $produtos_email .= "\nDescrição: {descricao}";//.ArrayHelper::getValue($produto, 'item.title');
                 $produtos_email .= "\nQuantidade: {quantidade}";//.ArrayHelper::getValue($produto, 'quantity');
                 $produtos_email .= "\nValor: {valor}";
                 $produtos_email .= "\nObservação: {observacao}";
                 if($pedido_mercado_livre_produto->valor_cotacao <> null && $pedido_mercado_livre_produto->valor_cotacao > 0){
                 $produtos_email .= $pedido_mercado_livre_produto->valor_cotacao;
                 }*/
                
                
            }
            
            //Cadastra os dados dos pagamentos
            foreach(ArrayHelper::getValue($order, 'body.payments') as $k => $pagamento){
                
                $pedido_mercado_livre_pagamento = PedidoMercadoLivrePagamento::find()   ->andWhere(['=', 'pedido_mercado_livre_id', $pedido_mercado_livre->id])
                ->andWhere(['=', 'pagamento_meli_id', ArrayHelper::getValue($pagamento, 'id')])
                ->one();
                if($pedido_mercado_livre_pagamento){
                    $pedido_mercado_livre_pagamento->pedido_mercado_livre_id    = $pedido_mercado_livre->id;
                    $pedido_mercado_livre_pagamento->pagamento_meli_id          = (string) ArrayHelper::getValue($pagamento, 'id');
                    $pedido_mercado_livre_pagamento->payer_id                   = (string) ArrayHelper::getValue($pagamento, 'payer_id');
                    $pedido_mercado_livre_pagamento->card_id                    = (string) ArrayHelper::getValue($pagamento, 'card_id');
                    $pedido_mercado_livre_pagamento->payment_method_id          = (string) ArrayHelper::getValue($pagamento, 'payment_method_id');
                    $pedido_mercado_livre_pagamento->operation_type             = (string) ArrayHelper::getValue($pagamento, 'operation_type');
                    $pedido_mercado_livre_pagamento->payment_type               = (string) ArrayHelper::getValue($pagamento, 'payment_type');
                    $pedido_mercado_livre_pagamento->status                     = (string) ArrayHelper::getValue($pagamento, 'status');
                    $pedido_mercado_livre_pagamento->status_detail              = (string) ArrayHelper::getValue($pagamento, 'status_detail');
                    $pedido_mercado_livre_pagamento->transaction_amount         = ArrayHelper::getValue($pagamento, 'transaction_amount');
                    $pedido_mercado_livre_pagamento->taxes_amount               = ArrayHelper::getValue($pagamento, 'taxes_amount');
                    $pedido_mercado_livre_pagamento->shipping_cost              = ArrayHelper::getValue($pagamento, 'shipping_cost');
                    $pedido_mercado_livre_pagamento->coupon_amount              = ArrayHelper::getValue($pagamento, 'coupon_amount');
                    $pedido_mercado_livre_pagamento->overpaid_amount            = ArrayHelper::getValue($pagamento, 'overpaid_amount');
                    $pedido_mercado_livre_pagamento->total_paid_amount          = ArrayHelper::getValue($pagamento, 'total_paid_amount');
                    $pedido_mercado_livre_pagamento->installment_amount         = ArrayHelper::getValue($pagamento, 'installment_amount');
                    $pedido_mercado_livre_pagamento->date_approved              = ArrayHelper::getValue($pagamento, 'date_approved');
                    $pedido_mercado_livre_pagamento->authorization_code         = (string) ArrayHelper::getValue($pagamento, 'authorization_code');
                    $pedido_mercado_livre_pagamento->date_created               = ArrayHelper::getValue($pagamento, 'date_created');
                    $pedido_mercado_livre_pagamento->date_last_modified         = ArrayHelper::getValue($pagamento, 'date_last_modified');
                    
                    if($pedido_mercado_livre_pagamento->save()){
                        echo "<br><br><br>Pagamento alterado";
                    }
                    else{
                        echo "<br><br><br>Pagamento não alterado";
                    }
                }
                else{
                    $pedido_mercado_livre_pagamento                             = new PedidoMercadoLivrePagamento();
                    $pedido_mercado_livre_pagamento->pedido_mercado_livre_id    = $pedido_mercado_livre->id;
                    $pedido_mercado_livre_pagamento->pagamento_meli_id          = (string) ArrayHelper::getValue($pagamento, 'id');
                    $pedido_mercado_livre_pagamento->payer_id                   = (string) ArrayHelper::getValue($pagamento, 'payer_id');
                    $pedido_mercado_livre_pagamento->card_id                    = (string) ArrayHelper::getValue($pagamento, 'card_id');
                    $pedido_mercado_livre_pagamento->payment_method_id          = (string) ArrayHelper::getValue($pagamento, 'payment_method_id');
                    $pedido_mercado_livre_pagamento->operation_type             = (string) ArrayHelper::getValue($pagamento, 'operation_type');
                    $pedido_mercado_livre_pagamento->payment_type               = (string) ArrayHelper::getValue($pagamento, 'payment_type');
                    $pedido_mercado_livre_pagamento->status                     = (string) ArrayHelper::getValue($pagamento, 'status');
                    $pedido_mercado_livre_pagamento->status_detail              = (string) ArrayHelper::getValue($pagamento, 'status_detail');
                    $pedido_mercado_livre_pagamento->transaction_amount         = ArrayHelper::getValue($pagamento, 'transaction_amount');
                    $pedido_mercado_livre_pagamento->taxes_amount               = ArrayHelper::getValue($pagamento, 'taxes_amount');
                    $pedido_mercado_livre_pagamento->shipping_cost              = ArrayHelper::getValue($pagamento, 'shipping_cost');
                    $pedido_mercado_livre_pagamento->coupon_amount              = ArrayHelper::getValue($pagamento, 'coupon_amount');
                    $pedido_mercado_livre_pagamento->overpaid_amount            = ArrayHelper::getValue($pagamento, 'overpaid_amount');
                    $pedido_mercado_livre_pagamento->total_paid_amount          = ArrayHelper::getValue($pagamento, 'total_paid_amount');
                    $pedido_mercado_livre_pagamento->installment_amount         = ArrayHelper::getValue($pagamento, 'installment_amount');
                    $pedido_mercado_livre_pagamento->date_approved              = ArrayHelper::getValue($pagamento, 'date_approved');
                    $pedido_mercado_livre_pagamento->authorization_code         = (string) ArrayHelper::getValue($pagamento, 'authorization_code');
                    $pedido_mercado_livre_pagamento->date_created               = ArrayHelper::getValue($pagamento, 'date_created');
                    $pedido_mercado_livre_pagamento->date_last_modified         = ArrayHelper::getValue($pagamento, 'date_last_modified');
                    
                    if($pedido_mercado_livre_pagamento->save()){
                        echo "<br><br><br>Pagamento criado";
                    }
                    else{
                        echo "<br><br><br>Pagamento não criado";
                    }
                }
            }
            
            $pedido_mercado_livre->email_texto = $produtos_email;
            $pedido_mercado_livre->email_assunto = "Pedido {codigo_fabricante} * {quantidade} - {nome}";
            if($pedido_mercado_livre->user_id == "435343067"){
                $pedido_mercado_livre->email_assunto = "Novo Pedido {codigo_fabricante} * {quantidade} - {nome}";
            }
            $pedido_mercado_livre->save();
        }
        
        echo "<br><br><br>";
    }
    
}
