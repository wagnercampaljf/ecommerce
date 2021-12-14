<?php

namespace backend\controllers;

use Yii;
use common\models\ProdutoFilial;
use common\models\ProdutoFilialSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Produto;
use common\models\Filial;
use yii\web\Response;
use Livepixel\MercadoLivre\Meli;
use yii\helpers\ArrayHelper;


/**
 * ProdutoFilialController implements the CRUD actions for ProdutoFilial model.
 */
class ProdutoFilialController extends Controller
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
        ];
    }

    /**
     * Lists all ProdutoFilial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProdutoFilialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProdutoFilial model.
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
     * Creates a new ProdutoFilial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProdutoFilial();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ProdutoFilial model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id]);
            return $this->render('update', [
                'model' => $model,
                'mensagem' => '<div class="text-primary h4">Produto atualizado no com sucesso!</div>',
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ProdutoFilial model.
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
     * Finds the ProdutoFilial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProdutoFilial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProdutoFilial::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionGetProduto($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => Produto::findOne($id)->nome]];
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = Produto::find()
            ->select(['produto.id', "produto.nome||' ('||produto.codigo_global||')' as text"])
            ->where([
                'like',
                'lower(produto.nome)',
                strtolower($q)
            ])
            ->orWhere([
                'lower(produto.id::VARCHAR)' =>  strtolower($q)
            ])
            ->orWhere(['like', 'produto.codigo_global', $q])
            ->limit(10)
            ->createCommand()
            ->queryAll();
            $out['results'] = array_values($results);
        }
        
        return $out;
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
            ->limit(10)
            ->createCommand()
            ->queryAll();
            $out['results'] = array_values($results);
        }
        
        return $out;
    }
    
    public function actionAtualizarml($id)
    {
        
        $model = $this->findModel($id);
        
        $produto_filial_mercado_livre = $model->meli_id;
        $refresh_token__meli_filial = $model->filial->refresh_token_meli;

        if($model->produto_filial_origem_id != null){
            $model = ProdutoFilial::findOne($model->produto_filial_origem_id);
        }

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($refresh_token__meli_filial);
        $response = ArrayHelper::getValue($user, 'body');
        
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            
            $meliAccessToken = $response->access_token;
            
            if (is_null($model->valorMaisRecente)) {
                Yii::error("Produto Filial: {$model->produto->nome}({$model->id}), não possui valor", 'error_yii');
                
                return $this->render('update', [
                    'model' => $model,
                    'erro' => "Produto sem valor cadastrado",
                ]);
            }
            
            //$page = $this->render(__DIR__ . 'lojista/views/mercado-livre/produto.php', ['produto' => $model]);
            $title = Yii::t('app', '{nome} ({cod})', ['cod' => $model->produto->codigo_global, 'nome' => $model->produto->nome ]);
            
            //Update Item
            $body = [
                "title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                'attributes' =>[
                    [
                        'id' => 'PART_NUMBER',
                        'name' => 'Número da peça',
                        'value_id' => NULL,
                        'value_name' => $model->produto->codigo_global,
                        'value_struct' => NULL,
                        'attribute_group_id' => 'DFLT',
                        'attribute_group_name' => 'Outros',
                    ],
                    [
                        "id"=> "BRAND",
                        "name"=> "Marca",
                        "value_id"=> null,
                        "value_name"=> $model->produto->fabricante->nome,
                        "value_struct"=> null,
                        "attribute_group_id"=> "OTHERS",
                        "attribute_group_name"=> "Outros"
                    ],
                    [
                        "id" => "EAN",
                        "value_name" => $model->produto->codigo_barras
                    ],
                ]
                
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
            //echo "<pre>"; print_r($response); echo "<pre>"; die;
            if ($response['httpCode'] >= 300) {
                return $this->render('update', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Título não atualizado no Mercado Livre</div>',
                ]);
            }
            
            //1 para me2 (Mercado Envios)
            //2 para not_especified (a combinar)
            //3 para customizado
            
            switch ($model->envio) {
                case 1:
                    $modo = "me2";
                    break;
                case 2:
                    $modo = "not_specified";
                    break;
                case 3:
                    $modo = "custom";
                    break;
            }
            $body = [
                "shipping" => [
                    "mode"=> $modo,
                    "local_pick_up" => true,
                    "free_shipping" => false,
                    "free_methods" => [],
                ],
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body,[]);
            $permalink = "";
            if ($response['httpCode'] >= 300) {
                return $this->render('update', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Modo de envio não atualizado no Mercado Livre</div>',
                ]);
            }
            
            //Update Descrição
            //$body = ['text' => $page];
            //$response = $meli->put("items/{$model->meli_id}/description?access_token=" . $meliAccessToken, $body, []);
            
            $body = [
                "pictures" => $model->produto->getUrlImagesML(),
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body,[]);
            if ($response['httpCode'] >= 300) {
                return $this->render('update', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Imagens não atualizadas no Mercado Livre</div>',
                ]);
            }
            
            //Update Item
            $body = [
                "price" => round($model->getValorMercadoLivre(), 2),
                "available_quantity" => $model->quantidade,
                
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                return $this->render('update', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Preço e quantidade não atualizados no Mercado Livre</div>',
                ]);
            }
            else {
                   
                $permalink = '<div class="h4"><a class="text-success" href="'.ArrayHelper::getValue($response, 'body.permalink').'">Link para o ML (Principal)</a></div>';
                
                $produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$model->id])->all();
                
                foreach ($produtos_filiais_outros as $produto_filial_outro){
                    $preco_outro = round(round($model->getValorMercadoLivre(), 2), 2);
                    
                    $user_outro     = $meli->refreshAccessToken($produto_filial_outro->filial->refresh_token_meli);
                    $response_outro = ArrayHelper::getValue($user_outro, 'body');
                    
                    if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
                        
                        $meliAccessToken_outro = $response_outro->access_token;
                        if($produto_filial_outro->meli_id != null){
                            $body = [
                                "available_quantity" => $model->quantidade,
                                "price" => $preco_outro,
                                "shipping" => [
                                    "mode" => $modo,
                                    "local_pick_up" => true,
                                    "free_shipping" => false,
                                    "free_methods" => [],
                                ],
                            ];
                            $response_outro = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, []);
                            //echo "<pre>"; print_r($response); echo "<pre>"; die;
                            if ($response_outro['httpCode'] >= 300) {
                                return $this->render('update', [
                                    'model' => $model,
                                    'erro' => '<div class="text-text-warning h4">Conta principal do Mercado Livre altarada com sucesso, conta secundária não alterada</div>',
                                ]);
                            }
                            else{
                                $permalink = $permalink . '<div class="h4"><a class="text-success" href="'.ArrayHelper::getValue($response_outro, 'body.permalink').'">Link para o ML (Secundária)</a></div>';
                            }
                            
                        }
                        else{
                            $title = Yii::t('app', '{nome} ({cod})', [
                                'cod' => $model->produto->codigo_global,
                                'nome' => $model->produto->nome
                            ]);
                            $body = [
                                "title" => utf8_encode((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                                "category_id" => utf8_encode(ArrayHelper::getValue($response, 'body.category_id')),
                                "listing_type_id" => "bronze",
                                "currency_id" => "BRL",
                                "price" => $preco_outro,
                                "available_quantity" => utf8_encode($model->quantidade),
                                "condition" => "new",
                                "pictures" => $model->produto->getUrlImagesML(),
                                "shipping" => [
                                    "mode" => "me2",
                                    "local_pick_up" => true,
                                    "free_shipping" => false,
                                    "free_methods" => [],
                                ],
                                "sale_terms" => [
                                    [       "id" => "WARRANTY_TYPE",
                                        "value_id" => "2230280"
                                    ],
                                    [       "id" => "WARRANTY_TIME",
                                        "value_name" => "3 meses"
                                    ]
                                ]
                            ];
                            $response_outro = $meli->post("items?access_token=" . $meliAccessToken_outro, $body);
                            if ($response_outro['httpCode'] < 300) {
                                $produto_filial_outro->meli_id = $response_outro['body']->id;
                                $produto_filial_outro->save();
                                $permalink = $permalink . '<div class="h4"><a class="text-success" href="'.ArrayHelper::getValue($response_outro, 'body.permalink').'">Link para o ML (Secundária)</a></div>';
                            }
                            else {
                                return $this->render('update', [
                                    'model' => $model,
                                    'erro' => '<div class="text-text-warning h4">Conta principal do Mercado Livre altarada com sucesso, conta secundária não criado</div>',
                                ]);
                            }
                        }
                    }
                }
            }
            
            
            return $this->render('update', [
                'model' => $model,
                'mensagem' => '<div class="text-primary h4">Produto atualizado no Mercado Livre com sucesso!</div>',
                'link_mercado_livre' => $permalink,
            ]);
            
        }
    }
}
