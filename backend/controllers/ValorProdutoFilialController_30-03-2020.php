<?php

namespace backend\controllers;

use Yii;
use common\models\ValorProdutoFilial;
use common\models\ValorProdutoFilialSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ProdutoFilial;
use common\models\Produto;
use common\models\Filial;
use yii\web\Response;
use Livepixel\MercadoLivre\Meli;
use yii\helpers\ArrayHelper;

/**
 * ValorProdutoFilialController implements the CRUD actions for ValorProdutoFilial model.
 */
class ValorProdutoFilialController extends Controller
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
     * Lists all ValorProdutoFilial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ValorProdutoFilialSearch();
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ValorProdutoFilial model.
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
     * Creates a new ValorProdutoFilial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ValorProdutoFilial();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $valor_produto_filial = ProdutoFilial::find()->andWhere(['=','id',$model->produto_filial_id])->one();
            $valor_produto_filial->quantidade = 99999;
            $valor_produto_filial->save();
            
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ValorProdutoFilial model.
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
            return $this->render('update', ['model' => $model,]);
        }
    }

    /**
     * Deletes an existing ValorProdutoFilial model.
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
     * Finds the ValorProdutoFilial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ValorProdutoFilial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ValorProdutoFilial::  find()//findOne($id)
                                            //->select(['valor_produto_filial.id','produto_filial.filial_id as filial_id'])
                                            ->joinWith(['produtoFilial', 'produtoFilial.produto', 'produtoFilial.filial'])
                                            ->andWhere(['=','valor_produto_filial.id',$id])
                                            ->one()
            ) !== null) {
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
            ->select(['produto.id', 'produto.nome as text'])
            ->where([
                'like',
                'lower(produto.nome)',
                strtolower($q)
            ])
            ->orWhere([
                'lower(produto.id::VARCHAR)' =>  strtolower($q)
            ])
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
    
    public function actionGetProdutoFilial($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => ProdutoFilial::findOne($id)->filial->nome ." - ". ProdutoFilial::findOne($id)->produto->nome ."(". ProdutoFilial::findOne($id)->produto->codigo_global . ")"]];
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = ProdutoFilial::find()
            ->select(['produto_filial.id', "(coalesce((filial.nome),'') ||' - '|| coalesce((produto.nome),'') || '('|| coalesce((produto.codigo_global),'')||')') as text"])
            ->joinWith(['produto', 'filial'])
            ->where([
                'like',
                'lower(produto.nome)',
                strtolower($q)
            ])
            ->orWhere([
                'lower(produto_filial.id::VARCHAR)' =>  strtolower($q)
            ])
            ->orWhere(['like', 'produto.codigo_global', $q])
            ->andWhere(['<>', 'filial_id', 43])
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
        
        //echo "<pre>"; print_r($model); echo "<pre>"; die;
        
        $produto_filial = ProdutoFilial::find()->andWhere(['=','id',$model->produto_filial_id])->one();
        
        $produto_filial_mercado_livre = $produto_filial->meli_id;
        $refresh_token__meli_filial = $produto_filial->filial->refresh_token_meli;
        
        if($produto_filial->produto_filial_origem_id != null){
            $produto_filial = ProdutoFilial::findOne($produto_filial->produto_filial_origem_id);
        }
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($refresh_token__meli_filial);
        $response = ArrayHelper::getValue($user, 'body');
        
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            
            $meliAccessToken = $response->access_token;
            
            if (is_null($produto_filial->valorMaisRecente)) {
                Yii::error("Produto Filial: {$produto_filial->produto->nome}({$produto_filial->id}), não possui valor", 'error_yii');
                
                return $this->render('view', [
                    'model' => $model,
                    'erro' => "Produto sem valor cadastrado",
                ]);
            }
            
            //$page = $this->render(__DIR__ . 'lojista/views/mercado-livre/produto.php', ['produto' => $model]);
            $title = Yii::t('app', '{nome} ({cod})', ['cod' => $produto_filial->produto->codigo_global, 'nome' => $produto_filial->produto->nome ]);
            
            //Update Item
            $body = [
                "title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                'attributes' =>[
                    [
                        'id' => 'PART_NUMBER',
                        'name' => 'Número da peça',
                        'value_id' => NULL,
                        'value_name' => $produto_filial->produto->codigo_global,
                        'value_struct' => NULL,
                        'attribute_group_id' => 'DFLT',
                        'attribute_group_name' => 'Outros',
                    ],
                    [
                        "id"=> "BRAND",
                        "name"=> "Marca",
                        "value_id"=> null,
                        "value_name"=> $produto_filial->produto->fabricante->nome,
                        "value_struct"=> null,
                        "attribute_group_id"=> "OTHERS",
                        "attribute_group_name"=> "Outros"
                    ],
                    [
                        "id" => "EAN",
                        "value_name" => $produto_filial->produto->codigo_barras
                    ],
                ]
                
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
            //echo "<pre>"; print_r($response); echo "<pre>"; die;
            if ($response['httpCode'] >= 300) {
                return $this->render('view', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Título não atualizado no Mercado Livre</div>',
                ]);
            }
            
            //1 para me2 (Mercado Envios)
            //2 para not_especified (a combinar)
            //3 para customizado
            
            switch ($produto_filial->envio) {
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
                return $this->render('view', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Modo de envio não atualizado no Mercado Livre</div>',
                ]);
            }
            
            //Update Descrição
            //$body = ['text' => $page];
            //$response = $meli->put("items/{$model->meli_id}/description?access_token=" . $meliAccessToken, $body, []);
            
            $body = [
                "pictures" => $produto_filial->produto->getUrlImagesML(),
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body,[]);
	    //print_r($response);
            if ($response['httpCode'] >= 300) {
                return $this->render('view', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Imagens não atualizadas no Mercado Livre</div>',
                ]);
            }

            //Update Item
            $body = [
                "price" => round($produto_filial->getValorMercadoLivre(), 2),
                "available_quantity" => $produto_filial->quantidade,
                
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                return $this->render('view', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Preço e quantidade não atualizados no Mercado Livre</div>',
                ]);
            }
            else {
                
                $permalink = '<div class="h4"><a class="text-success" href="'.ArrayHelper::getValue($response, 'body.permalink').'">Link para o ML (Principal)</a></div>';
                
                $produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produto_filial->id])->all();
                
                foreach ($produtos_filiais_outros as $produto_filial_outro){
                    $preco_outro = round($produto_filial->getValorMercadoLivre(), 2);
                    
                    $user_outro     = $meli->refreshAccessToken($produto_filial_outro->filial->refresh_token_meli);
                    $response_outro = ArrayHelper::getValue($user_outro, 'body');
                    
                    if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
                        
                        $meliAccessToken_outro = $response_outro->access_token;
                        if($produto_filial_outro->meli_id != null){
                            $body = [
                                "available_quantity" => $produto_filial->quantidade,
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
                                'cod' => $produto_filial->produto->codigo_global,
                                'nome' => $produto_filial->produto->nome
                            ]);
			    $condicao = ($produto_filial->produto->e_usado)? "used" : "new";
                            $body = [
                                "title" => utf8_encode((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                                "category_id" => utf8_encode(ArrayHelper::getValue($response, 'body.category_id')),
                                "listing_type_id" => "bronze",
                                "currency_id" => "BRL",
                                "price" => $preco_outro,
                                "available_quantity" => utf8_encode($produto_filial->quantidade),
                                "condition" => $condicao,
                                "pictures" => $produto_filial->produto->getUrlImagesML(),
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
                                return $this->render('view', [
                                    'model' => $model,
                                    'erro' => '<div class="text-text-warning h4">Conta principal do Mercado Livre altarada com sucesso, conta secundária não criado</div>',
                                ]);
                            }
                        }
                    }
                }
            }
            
            
            return $this->render('view', [
                'model' => $model,
                'mensagem' => '<div class="text-primary h4">Produto atualizado no Mercado Livre com sucesso!</div>',
                'link_mercado_livre' => $permalink,
            ]);
            
        }
    }
    
    public function actionCriarml($id)
    {
        
        $model = $this->findModel($id);
        
        //echo "<pre>"; print_r($model); echo "<pre>"; die;
        
        $produto_filial = ProdutoFilial::find()->andWhere(['=','id',$model->produto_filial_id])->one();
        //echo "<pre>"; print_r($produto_filial); echo "<pre>"; die;
        
        if($produto_filial->meli_id <> null and $produto_filial->meli_id <> "" ){
            return $this->render('view', [
                'model' => $model,
                'erro' => '<div class="text-warning h4">Produto já criado no Mercado Livre</div>',
            ]);
        }
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        
        $user = $meli->refreshAccessToken($produto_filial->filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $subcategoriaMeli = $produto_filial->produto->subcategoria->meli_id;
            if (!isset($subcategoriaMeli)) {
                return $this->render('view', [
                    'model' => $model,
                    'erro' => "Produto sem subcategoria",
                ]);
            }

            //$page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produtoFilial]);
            
            $title = Yii::t('app', '{nome} ({cod})', [
                'cod' => $produto_filial->produto->codigo_global,
                'nome' => $produto_filial->produto->nome
            ]);
            
            switch ($produto_filial->envio) {
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
	    $condicao = ($produto_filial->produto->e_usado)? "used" : "new";
            $body = [
                "title" => (strlen($title) <= 60) ? $title : substr($title, 0, 60),
                "category_id" => utf8_encode($subcategoriaMeli),
                "listing_type_id" => "bronze",
                "currency_id" => "BRL",
                "price" => utf8_encode(round($produto_filial->getValorMercadoLivre(), 2)),
                "available_quantity" => utf8_encode($produto_filial->quantidade),
                "seller_custom_field" => utf8_encode($produto_filial->id),
                "condition" => $condicao,
                //"description" => utf8_encode($page),
                //"description" => ["plain_text" => utf8_encode($page)],
                //"description" => ["plain_text" => $page],
                "pictures" => $produto_filial->produto->getUrlImagesML(),
                "shipping" => [
                    "mode" => $modo,
                    "local_pick_up" => true,
                    "free_shipping" => false,
                    "free_methods" => [],
                ],
                "warranty" => "6 meses",
		'attributes' =>[
                                [
                                'id'                    => 'PART_NUMBER',
                                'name'                  => 'Número de Peça',
                                'value_id'              => null,
                                'value_name'            => $produto_filial->produto->codigo_global,
                                'value_struct'          => null,
                                'values'                => [[
                                        'id'    => null,
                                        'name'  => $produto_filial->produto->codigo_global,
                                        'struct'=> null,
                                ]],
                                'attribute_group_id'    => "OTHERS",
                                'attribute_group_name'  => "Outros"
                                ]
                              ]
                ];
                
            $response = $meli->post("items?access_token=" . $meliAccessToken, $body);
            if ($response['httpCode'] >= 300) {
                return $this->render('view', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Produto não criado no ML</div>',
                ]);
            } else {
                $produto_filial->meli_id = $response['body']->id;
                if (!$produto_filial->save()) {
                    return $this->render('view', [
                        'model' => $model,
                        'erro' => '<div class="text-danger h4">meli_id não salvo no estoque</div>',
                    ]);
                }
                else{
                    return $this->render('view', [
                        'model' => $model,
                        'mensagem' => '<div class="text-primary h4">Produto criado no Mercado Livre com sucesso!</div>',
                        'link_mercado_livre' => '<div class="h4"><a class="text-success" href="'.ArrayHelper::getValue($response, 'body.permalink').'">Link para o ML</a></div>',
                    ]);
                }
            }
        }
    }
}
